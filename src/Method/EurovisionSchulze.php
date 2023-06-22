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
            //echo("\n\$country = ".$country.",  \$iCountry = ".$iCountry.",  \$jCountry = ".$jCountry.",  \$rawMargin = ".$rawMargin.",  Votes = ".($iVotes + $jVotes)."\n");
            if($contest->votesbyCountry[$country] > 0 AND $rawMargin != 0) {
                $nationalMargins[$country] = $rawMargin * ($contest->populations[$country]/(($iVotes+$jVotes)*abs($rawMargin)))**(1/3);
            } else {
                $nationalMargins[$country] = 0;
            }
        }
        $rawMargin_WLD = $this->filteredPairwise['WLD'][$iCountry]['win'][$jCountry]-$this->filteredPairwise['WLD'][$jCountry]['win'][$iCountry];
        if ($contest->votesbyCountry['WLD'] > 0 AND $rawMargin_WLD != 0) {
            //echo("\n\World vote".",  \$iCountry = ".$iCountry.",  \$jCountry = ".$jCountry.",  \$rawMargin = ".$rawMargin_WLD.",  Votes = ".$contest->votesbyCountry['WLD']."\n");
            $nationalMargins['WLD'] = $rawMargin * ($contest->populations['WLD']/($contest->votesbyCountry['WLD']*abs($rawMargin_WLD)))**(1/3);
        }
        
        echo('Margin for '.$iCountry.' vs '.$jCountry." is ".array_sum($nationalMargins)."\n");
        return array_sum($nationalMargins);
    }

    // Calculate the Strongest Paths
    protected function makeStrongestPaths(): void
    {
        $contest = $this->getElection();
        $CandidatesKeys = array_keys($contest->getCandidatesList());

        foreach ($CandidatesKeys as $i) {
            foreach ($CandidatesKeys as $j) {
                if ([$i] > [$j]) {
                    $this->StrongestPaths[$i][$j] = (-1) * $this->StrongestPaths[$j][$i];
                    echo("Inverted an already-calculated margin.\n");
                } elseif ($i != $j) {
                    $this->StrongestPaths[$i][$j] = $this->schulzeVariant($i, $j, $contest);
                    echo("\$StrongestPaths[".$i."][".$j."] now set to ".$this->StrongestPaths[$i][$j]."\n");
                }
            }
        }

        foreach ($CandidatesKeys as $i) {
            foreach ($CandidatesKeys as $j) {
                if ($i !== $j) {
                    foreach ($CandidatesKeys as $k) {
                        if ($i !== $k && $j !== $k) {
                            $this->StrongestPaths[$j][$k] =
                                max(
                                    $this->StrongestPaths[$j][$k],
                                    min($this->StrongestPaths[$j][$i], $this->StrongestPaths[$i][$k])
                                );
                        }
                    }
                }
            }
        }
    }
}
