<?php

declare(strict_types=1);

namespace EurovisionVoting\Method;

use CondorcetPHP\Condorcet\Algo\Methods\Schulze\Schulze_Core;
use CondorcetPHP\Condorcet\Election;
use EurovisionVoting\Contest;

class EurovisionSchulze2 extends Schulze_Core
{
    public const METHOD_NAME = ['Eurovision Schulze 2', 'Grand Final square root'];

    protected function schulzeVariant(int $i, int $j, Election $contest): float
    {
        $nationalVotes = $contest->getVotesManager();
        $nationalMargins = [];
        $iCountry = $contest->getCandidateObjectFromKey($i)->getName();
        $jCountry = $contest->getCandidateObjectFromKey($j)->getName();
        
        foreach ($contest->votingCountries as $country)
        {
            $filteredPairwise = $contest->getResult(methodOptions: ['%tagFilter' => true, 'withTag' => true, 'tags' => $country])->pairwise;
            echo("\n\$filteredPairwise = ");
            var_dump($filteredPairwise);
            $nationalMargins[$country] = ($filteredPairwise[$iCountry]['win'][$jCountry] - $filteredPairwise[$jCountry]['win'][$iCountry])**(1/2);
        }
        
        return array_sum($nationalMargins);
    }

    protected function getStats(): array
    {
        return [];
    }
}
