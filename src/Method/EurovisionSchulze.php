<?php

declare(strict_types=1);

namespace EurovisionVoting\Method;

use CondorcetPHP\Condorcet\Algo\Methods\Schulze\Schulze_Core;
use CondorcetPHP\Condorcet\Election;
use EurovisionVoting\Contest;

class EurovisionSchulze extends Schulze_Core
{
    public const METHOD_NAME = ['Eurovision Schulze', 'Grand Final', 'Grand Final 1.5-root'];
    protected array $filteredPairwise;
    
    protected function getAllPairwise(Contest $contest)

    {
        foreach ($contest->votingCountries as $country) {
            $this->filteredPairwise[$country] = $contest->getResult(methodOptions: ['%tagFilter' => true, 'withTag' => true, 'tags' => $country])->pairwise;
        }
        echo("Finished getAllPairwise()\n");
    }

    protected function schulzeVariant(int $i, int $j, Election $contest): float
    {
        if(!isset($this->filteredPairwise)){
            $this->getAllPairwise($contest);
        }
        
        //echo("Starting EurovisionSchulze::schulzeVariant()\n");
        $nationalVotes = $contest->getVotesManager();
        $nationalMargins = [];
        $iCountry = $contest->getCandidateObjectFromKey($i)->getName();
        $jCountry = $contest->getCandidateObjectFromKey($j)->getName();
        //echo("Going to start the foreach loop.\n");
        foreach ($contest->votingCountries as $country)
        {
            $rawMargin = $this->filteredPairwise[$country][$iCountry]['win'][$jCountry]-$this->filteredPairwise[$country][$jCountry]['win'][$iCountry];
            if($contest->votesbyCountry[$country] > 0 AND $rawMargin != 0) {
                $nationalMargins[$country] = $rawMargin * ($contest->populations[$country]/($contest->votesbyCountry[$country]*abs($rawMargin)))**(1/3);
            } else {
                $nationalMargins[$country] = 0;
            }
        }
        echo('Margin for '.$iCountry.' vs '.$jCountry." is ".array_sum($nationalMargins)."\n");
        return array_sum($nationalMargins);
    }

    protected function getStats(): array
    {
        return [];
    }
}
