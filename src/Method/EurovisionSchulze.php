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
        $this->filteredPairwise['WLD'] = $contest->getResult(methodOptions: ['%tagFilter' => true, 'withTag' => false, 'tags' => $country])->pairwise;
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
            $iVotes = $this->filteredPairwise[$country][$iCountry]['win'][$jCountry];
            $jVotes = $this->filteredPairwise[$country][$jCountry]['win'][$iCountry];
            $rawMargin = $iVotes - $jVotes;
            if($contest->votesbyCountry[$country] > 0 AND $rawMargin != 0) {
                $nationalMargins[$country] = $rawMargin * ($contest->populations[$country]/(($iVotes+$jVotes)*abs($rawMargin)))**(1/3);
            } else {
                $nationalMargins[$country] = 0;
            }
        }
        $rawMargin_WLD = $this->filteredPairwise['WLD'][$iCountry]['win'][$jCountry]-$this->filteredPairwise['WLD'][$jCountry]['win'][$iCountry];
        if ($contest->votesbyCountry['WLD'] > 0 AND $rawMargin_WLD != 0) {
            $nationalMargins['WLD'] = $rawMargin * ($contest->populations['WLD']/($contest->votesbyCountry['WLD']*abs($rawMargin_WLD)))**(1/3);
        }
        
        echo('Margin for '.$iCountry.' vs '.$jCountry." is ".array_sum($nationalMargins)."\n");
        return array_sum($nationalMargins);
    }

}
