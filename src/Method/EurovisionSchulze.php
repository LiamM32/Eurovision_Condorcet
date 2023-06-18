<?php

declare(strict_types=1);

namespace EurovisionVoting\Method;

use CondorcetPHP\Condorcet\Algo\Methods\Schulze\Schulze_Core;
use CondorcetPHP\Condorcet\Election;
use EurovisionVoting\Contest;

class EurovisionSchulze extends Schulze_Core
{
    public const METHOD_NAME = ['Eurovision Schulze', 'Grand Final', 'Grand Final 1.5-root'];

    protected function schulzeVariant(int $i, int $j, Election $contest): int
    {
        echo("Starting EurovisionSchulze::schulzeVariant()\n");
        $nationalVotes = $contest->getVotesManager();
        $nationalMargins = [];
        $iCountry = $contest->getCandidateObjectFromKey($i)->getName();
        $jCountry = $contest->getCandidateObjectFromKey($j)->getName();
        
        foreach ($contest->votingCountries as $country)
        {
            //echo("\n\$country = ".$country."\n\$contest->populations[".$country."] = ".$contest->populations[$country]."\n");
            
            if($contest->votesbyCountry[$country] > 0) {
                $filteredPairwise = $contest->getResult(methodOptions: ['%tagFilter' => true, 'withTag' => true, 'tags' => $country])->pairwise;
            }
            $nationalMargins[$country] = (($filteredPairwise[$iCountry]['win'][$jCountry] - $filteredPairwise[$jCountry]['win'][$iCountry] ) * $contest->populations[$country] )^(1/3);
        }
        echo('Compared '.$iCountry.' to '.$jCountry."\n");
        return array_sum($nationalMargins);
    }

    protected function getStats(): array
    {
        return [];
    }
}
