<?php

declare(strict_types=1);

namespace EurovisionVoting\Method;

use CondorcetPHP\Condorcet\Algo\Methods\Schulze\Schulze_Core;
use CondorcetPHP\Condorcet\Election;
use EurovisionVoting\Contest;

class EurovisionSchulze extends Schulze_Core
{
    public const METHOD_NAME = ['Eurovision Schulze', 'Grand Final'];
    
    function getPopulations()
    {
        $countries = json_decode(readfile("countries.json"), true);
        $populations = json_decode(readfile("populations.json"), true);
        $populations = array_intersect_key($population, $countries);
        //var_dump(populations);
        return $populations;
    }

    protected function schulzeVariant(int $i, int $j, Election $contest): int
    {
        if($contest->populations === NULL)
        {
            $contest->populations = getPopulations();
        }
        $nationalVotes = $contest->getVotesManager();
        $nationalMargins = [];
        $iCountry = $contest->getCandidateObjectFromKey($i)->getName();
        $jCountry = $contest->getCandidateObjectFromKey($j)->getName();
        
        foreach ($contest->populations as $country)
        {
            $filteredPairwise = $contest->getResult(methodOptions: ['%tagFilter' => true, 'withTag' => true, 'tags' => $country])->pairwise;
            $nationalMargins[$country] = ( ( $filteredPairwise[$iCountry]['win'][$jName] - $filteredPairwise[$jCountry]['win'][$iName] ) * $this->populations[$country] )^(1/3);
        }
        
        return array_sum($nationalMargins);
    }

    protected function getStats(): array
    {
        return [];
    }
}
