<?php

declare(strict_types=1);

namespace EurovisionVoting\Method;

use CondorcetPHP\Condorcet\Algo\Methods\Schulze\Schulze_Core;
use CondorcetPHP\Condorcet\Election;

class EurovisionSchulze extends Schulze_Core
{
    public const METHOD_NAME = ['Eurovision Schulze', 'Grand Final'];

    protected function schulzeVariant(int $i, int $j, Election $contest): float
    {
        $nationalVotes = $contest->getVotesManager();
        $nationalMargins = [];
        $iCountry = $contest->getCandidateObjectFromKey($i)->getName();
        $jCountry = $contest->getCandidateObjectFromKey($j)->getName();

        foreach ($contest->votingCountries as $country)
        {
            echo("\n\$country = ".$country."\n\$contest->populations[".$country."] = ".$contest->populations[$country]."\n");

            $filteredPairwise = $contest->getResult(methodOptions: ['%tagFilter' => true, 'withTag' => true, 'tags' => $country])->pairwise;
            var_dump($contest->populations[$country]);
            $nationalMargins[$country] = (($filteredPairwise[$iCountry]['win'][$jCountry] - $filteredPairwise[$jCountry]['win'][$iCountry] ) * $contest->populations[$country] )**(1/3);
        }

        return (float) array_sum($nationalMargins);
    }

    protected function getStats(): array
    {
        return [];
    }
}
