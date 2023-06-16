<?php

namespace EurovisionVoting;

use CondorcetPHP\Condorcet\Election;

class Contest extends Election
{
    public array $populations = [];
    public array $votingCountries;
    
    public function getPopulations()
    {
        $countries = json_decode(fread(fopen("voting-countries.json", "r"), 512), true);
        $populations = json_decode(fread(fopen("populations.json", "r"), 8096), true);
        $populations = array_intersect_key($populations, array_flip($countries));
        echo ("Here are the populatins of voting countries:\n");
        var_dump($populations);
        return $populations;
    }
    
    public function readData()
    {
        $votingCountries = json_decode(fread(fopen("voting-countries.json", "r"), 512), true);
    }
}