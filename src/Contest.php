<?php

namespace EurovisionVoting;

use CondorcetPHP\Condorcet\Condorcet;
use CondorcetPHP\Condorcet\Election;
use CondorcetPHP\Condorcet\Result;
use CondorcetPHP\Condorcet\Timer\Chrono as Timer_Chrono;
use CondorcetPHP\Condorcet\Vote;
use EurovisionVoting\countrydata;

class Contest extends Election
{
    public array $populations = [];
    public array $votingCountries;
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
        
        return $this->populations;
    }
    
    public function readData()
    {
        $this->votingCountries = json_decode(fread(fopen(__DIR__."/../voting-countries.json", "r"), 512), true);
        $this->countryNames = countrydata::getCountryNames();
    }

    protected function registerVote(Vote $vote, array|string|null $tags): Vote
    {
        if (array_intersect(array_keys($this->groupBalance), $vote->getTags() ?? []) == null) {
            $vote->addTags('Public');
        }
        $tags === null || $vote->addTags($tags);
        $this->Votes[] = $vote;

        return $vote;
    }
    
    //Gets the number of voters in each participating country, and determines which country has the least influence per-voter.
    public function countVotersByCountry()
    {
        $minRatio = 1000000.0;
        $minRatioCountry = '';
        foreach ($this->votingCountries as $key=>$country)
        {
           $this->votesbyCountry[$country] = $this->countVotes($country);
           if (Init::$options['v']>=0) echo($country.' has '. $this->votesbyCountry[$country]." voters. ");
           $totalVotingPower = ($this->votesbyCountry[$country]*$this->populations[$country])**(1/3);
           if ($this->votesbyCountry[$country] === 0) {
               unset($this->votingCountries[$key]);
               if (Init::$options['v']>=0) echo("Removed from voting countries list.\n");
           } elseif ($totalVotingPower / $this->votesbyCountry[$country] < $minRatio) {
               $minRatio = $totalVotingPower/$this->votesbyCountry[$country];
               $minRatioCountry = $country;
               if (Init::$options['v']>=1) echo($country.' has '.($totalVotingPower/$this->votesbyCountry[$country])." voting weight per voter.\n");
           } else {
               if (Init::$options['v']>=1) echo($country.' has '.($totalVotingPower/$this->votesbyCountry[$country])." voting weight per voter.\n");
               elseif (Init::$options['v']>=0) echo ("\n");
           }
            //if (Init::$options['v']>=0) echo ("\n");
        }

        $this->typicalPopPerVoter = array_sum(tools::array_multiply($this->votesbyCountry, $this->populations)) / array_sum(tools::array_multiply($this->votesbyCountry));
        if (Init::$options['v']>=1) echo ('Typical population per voter in voting countries is '.$this->typicalPopPerVoter."\n");

        $this->votesbyCountry['WLD'] = $this->countVotes($this->votingCountries, false);
        if (Init::$options['v']>=1) echo("\$minRatio = ".$minRatio."\n");
        
        $this->populations['WLD'] = $this->countVotes($this->votingCountries, false) * array_sum($this->populations) / $this->countVotes($this->votingCountries);
    }

    public function copyContestDataForCountry (Contest $source, string $country)
    {
        $this->populations[$country] = $source->populations[$country];
        $this->groupBalance = $source->groupBalance;
        $this->votesbyCountry[$country] = $source->votesbyCountry[$country];
        foreach ($source->getCandidatesList() as $candidate) if($candidate->getName() !== $country) {
            $this->addCandidate($candidate);
        }
        foreach ($source->getVotesList($country) as $vote) {
            $this->addVote($vote);
        }
    }

    public function getResult(string $method = null, array $options = []) : Result
    {
        $chrono = (Condorcet::$UseTimer === true) ? new Timer_Chrono($this->timer) : null;
        $method = Condorcet::getMethodClass($method);

        $this->preparePairwiseAndCleanCompute();

        $this->initResult($method);

        $forceNew = $options['forceNew'] ?? 1;
        $testMode = $options['testMode'] ?? false;

        ($chrono !== null) && $chrono->setRole('GetResult for '.$method);
        return $this->Calculator[$method]->getResult($forceNew, $testMode, $options);
    }
}