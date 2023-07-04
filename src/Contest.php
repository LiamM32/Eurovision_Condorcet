<?php

namespace EurovisionVoting;

use CondorcetPHP\Condorcet\Condorcet;
use CondorcetPHP\Condorcet\Election;
use CondorcetPHP\Condorcet\Vote;
use EurovisionVoting\countrydata;

class Contest extends Election
{
    public array $populations = [];
    public array $votingCountries;
    public array $votingGroups = ['Public', 'Jury'];
    public array $groupBalance = ['Public'=>0.5, 'Jury'=>0.5];
    public array $votesbyCountry;
    public float $typicalPopPerVoter;
    public array $countryNames;
    
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
        $this->countryNames = countrydata::getCountryNames();
    }

    protected function registerVote(Vote $vote, array|string|null $tags): Vote
    {
        if (array_intersect($this->votingGroups, $vote->getTags() ?? []) == null) {
            $vote->addTags('Public');
        }
        $tags === null || $vote->addTags($tags);
        $this->Votes[] = $vote;

        return $vote;
    }
    
    //Gets the number of voters in each participating country, and determines which country has the least influence per-voter.
    public function countVotersByCountry()
    {
        global $options;

        $ratioPopulationVote = [];
        $minRatio = 1000000.0;
        $minRatioCountry = '';
        //The population for WLD should be set to $votes['WLD']^2 * $this->populations[max] / ($votes[max])^2, with max being the country with lowest voting power per-capita.
        foreach ($this->votingCountries as $key=>$country)
        {
           $this->votesbyCountry[$country] = $this->countVotes($country);
           if ($options['verbose']>=0) echo($country.' has '. $this->votesbyCountry[$country]." voters. ");
           $totalVotingPower = ($this->votesbyCountry[$country]*$this->populations[$country])**(1/3);
           if ($this->votesbyCountry[$country] === 0) {
               unset($this->votingCountries[$key]);
               if ($options['verbose']>=0) echo("Removed from voting countries list");
           } elseif ($totalVotingPower / $this->votesbyCountry[$country] < $minRatio) {
               $minRatio = $totalVotingPower/$this->votesbyCountry[$country];
               $minRatioCountry = $country;
               if ($options['verbose']>=1) echo($country.' has '.($totalVotingPower/$this->votesbyCountry[$country])." voting weight per voter.\n");
           } else {
               if ($options['verbose']>=1) echo($country.' has '.($totalVotingPower/$this->votesbyCountry[$country])." voting weight per voter.\n");
           }
            if ($options['verbose']>=0) echo ("\n");
        }

        $this->typicalPopPerVoter = array_sum(tools::array_multiply($this->votesbyCountry, $this->populations)) / array_sum($this->votesbyCountry);

        $this->votesbyCountry['WLD'] = $this->countVotes($this->votingCountries, false);
        if ($options['verbose']>=1) echo("\$minRatio = ".$minRatio."\n");
        
        $this->populations['WLD'] = $this->countVotes($this->votingCountries, false) * array_sum($this->populations) / $this->countVotes($this->votingCountries);
    }

    public function copyContestDataForCountry (Contest $source, string $country)
    {
        $this->populations[$country] = $source->populations[$country];
        $this->votingGroups = $source->votingGroups;
        $this->groupBalance = $source->groupBalance;
        $this->votesbyCountry[$country] = $source->votesbyCountry[$country];
        foreach ($source->getCandidatesList() as $candidate) if($candidate->getName() !== $country) {
            $this->addCandidate($candidate);
        }
        foreach ($source->getVotesList($country) as $vote) {
            $this->addVote($vote);
        }
    }
}