<?php

declare(strict_types=1);

namespace EurovisionVoting\Method;

use CondorcetPHP\Condorcet\Algo\Methods\Schulze\Schulze_Core;
use CondorcetPHP\Condorcet\Election;
use EurovisionVoting\Contest;

class EurovisionSchulze3 extends EurovisionSchulze
{
    public const METHOD_NAME = ['Eurovision Schulze 3', 'Grand Final 1.6 root'];

    protected function schulzeVariant(int $i, int $j, Election $contest): float
    {
        if(!isset($this->filteredPairwise)){
            $this->getAllPairwise($contest);
        }
        
        $nationalVotes = $contest->getVotesManager();
        $nationalMargins = [];
        $iCountry = $contest->getCandidateObjectFromKey($i)->getName();
        $jCountry = $contest->getCandidateObjectFromKey($j)->getName();
        
        foreach ($contest->votingCountries as $country)
        {
            $iVotes = $this->filteredPairwise[$country][$iCountry]['win'][$jCountry];
            $jVotes = $this->filteredPairwise[$country][$jCountry]['win'][$iCountry];
            if($iVotes+$jVotes > 0) {
                $nationalMargins[$country] = ($iVotes-$jVotes) / ($contest->populations[$country]/((abs($iVotes-$jVotes)*($iVotes+$jVotes))**(1/4)));
            } else {
                $nationalMargins[$country] = 0;
            }
        }
        
        return array_sum($nationalMargins);
    }

}
