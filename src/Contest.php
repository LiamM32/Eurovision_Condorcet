<?php

namespace EurovisionVoting;

use CondorcetPHP\Condorcet\Election;

class Contest extends Election
{
    public array $populations = [];
    public array $votingCountries;
    public array $votesbyCountry;
    
    public function parsePopulations()
    {
        $this->votingCountries = json_decode(fread(fopen(__DIR__."/../voting-countries.json", "r"), 512), true);
        $this->populations = json_decode(fread(fopen(__DIR__."/../populations.json", "r"), 8096), true);
        foreach ($this->votingCountries as $country) {
            if($this->populations[$country]===NULL) {
                echo ("\nWarning: Population of " .$country." unknown\n");
            }
        }
        $this->populations = array_intersect_key($this->populations, array_flip($this->votingCountries));
        //echo ("Here are the populations of voting countries:\n");
        //var_dump($this->populations);
        
        return $this->populations;
    }
    
    public function readData()
    {
        $this->votingCountries = json_decode(fread(fopen(__DIR__."/../voting-countries.json", "r"), 512), true);
    }
    
    //Gets the number of voters in each participating country, and determines which country has the least influence per-voter.
    public function countVotersByCountry()
    {
        $ratioPopulationVote = [];
        $minRatio = 1000000.0;
        $minRatioCountry = '';
        //The population for WLD should be set to $votes['WLD']^2 * $this->populations[max] / ($votes[max])^2, with max being the country with lowest voting power per-capita.
        foreach ($this->votingCountries as $key=>$country)
        {
           $this->votesbyCountry[$country] = $this->countVotes($country);
           echo($country.' has '. $this->votesbyCountry[$country]." voters. ");
           $totalVotingPower = ($this->votesbyCountry[$country]*$this->populations[$country])**(1/3);
           if ($this->votesbyCountry[$country] === 0) {
               unset($this->votingCountries[$key]);
               echo("Removed from voting countries list\n");
           } elseif ($totalVotingPower / $this->votesbyCountry[$country] < $minRatio) {
               $minRatio = $totalVotingPower/$this->votesbyCountry[$country];
               $minRatioCountry = $country;
               echo($country.' has '.($totalVotingPower/$this->votesbyCountry[$country])." voting weight per voter.\n");
           } else {
               echo($country.' has '.($totalVotingPower/$this->votesbyCountry[$country])." voting weight per voter.\n");
           }
        }
        $this->votesbyCountry['WLD'] = $this->countVotes($this->votingCountries, false);
        //echo("\$minRatio = ".$minRatio."\n");
        
        $this->populations['WLD'] = $this->countVotes($this->votingCountries, false) * array_sum($this->populations) / $this->countVotes($this->votingCountries);
    }
    
    //Changes the values in $populations to reduce variation, especially at the high end.
    public function getCompressedPopulations(int $repetitions, array $populations=NULL) : array
    {
        if (!isset($populations)) {
            $populations = array_intersect_key($this->populations, array_flip($this->votingCountries));
        }
        $x = 0;
        while ($x < $repetitions) {
            foreach ($this->votingCountries as $country) {
                $populations[$country] = $populations[$country]*(1 - 0.5*$populations[$country]/array_sum($populations));
            }
            $x++;
        }
        return $populations;
    }
    //
    public function getAltCompressedPopulations(int $repetitions, array $populations=NULL) : array
    {
        if (!isset($populations)) {
            $populations = $this->populations;
        }
        
        foreach ($this->populations as $country=>$pop) {
            $popOverRtV[$country] = $pop / sqrt($this->votesbyCountry);
        }
        
        $x = 0;
        while ($x < $repetitions) {
            $compressedPopulations=[];
            foreach ($this->votingCountries as $country) {
                $compressedPORV[$country] = $popOverRtV[$country]*(1 - 0.5*$popOverRtV[$country]/array_sum($popOverRtV));
                $populations[$country] = $compressedPORV[$country] * sqrt($this->votesbyCountry[$country]);
            }
            $x++;
            if (isset($compressedPORV['WLD']) AND $populations['WLD'] > array_sum($populations)-$populations['WLD']) {
                $x--;
                echo("Went through an extra population compression cycle\n");
            }
        }
        return $populations;
    }
    
    public static function large(array $array, int $rank)
    {
        sort($array);
        return $array[sizeof[$array]-$rank];
    }
}