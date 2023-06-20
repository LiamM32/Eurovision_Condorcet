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
        $this->votingCountries = json_decode(fread(fopen("voting-countries.json", "r"), 512), true);
        $this->populations = json_decode(fread(fopen("populations.json", "r"), 8096), true);
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
        $this->votingCountries = json_decode(fread(fopen("voting-countries.json", "r"), 512), true);
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

    //Get's the n'th largest value in an array.
    public static function large(array $array, int $rank)
    {
        sort($array);
        return $array[sizeof[$array]-$rank];
    }
}