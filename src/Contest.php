<?php

namespace EurovisionVoting;

use CondorcetPHP\Condorcet\Election;

class Contest extends Election
{
    public array $populations = [];
    public array $votingCountries;
    public array $votesbyCountry;
    
    public function getPopulations()
    {
        $this->votingCountries = json_decode(fread(fopen("voting-countries.json", "r"), 512), true);
        $this->populations = json_decode(fread(fopen("populations.json", "r"), 8096), true);
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
        $minRatio = 1.0;
        $minRatioCountry = '';
        //The population for WLD should be set to $votes['WLD']^2 * $this->populations[max] / ($votes[max])^2, with max being the country with lowest voting power per-capita.
        foreach ($this->votingCountries as $country)
        {
           $this->votesbyCountry[$country] = $this->countVotes($country);
           echo($country.' has '. $this->countVotes($country)." voters.\n");
           if ($this->votesbyCountry[$country]>0 AND ($this->votesbyCountry[$country]*$this->populations[$country])^(1/3)/$this->votesbyCountry[$country] < $minRatio) {
               $minRatio = ($this->votesbyCountry[$country]*$this->populations[$country])^(1/3)/$this->votesbyCountry[$country];
               $minRatioCountry = $country;
           }
        }
    }
}