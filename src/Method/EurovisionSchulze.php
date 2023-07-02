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
            $this->filteredPairwise['Public'][$country] = $contest->getExplicitFilteredPairwiseByTags(['Public',$country]);
            $this->filteredPairwise['Jury'][$country] = $contest->getExplicitFilteredPairwiseByTags(['Jury',$country]);
        }
        $this->filteredPairwise['Public']['WLD'] = $contest->getExplicitFilteredPairwiseByTags('WLD');
        echo("Finished getAllPairwise()\n");
    }

    public function cacheOnePairwise(string $group, string $country)
    {
        $this->filteredPairwise[$group][$country] = $this->getElection()->getExplicitFilteredPairwiseByTags([$group,$country]);
    }

    public function estimateWeights(Contest $contest, array $countries) {
        $weights = [];
        foreach($countries as $country) {
            $weights[$country] = $this->warpedNationalPublicMargin($contest->countVotes('Public', $country), 0, $contest->populations[$country]);
            $weights[$country] += $this->warpedNationalJuryMargin($contest->countVotes('Jury', $country), 0);
        }
        $weights = sort($weights);
        return $weights;
    }

    protected function schulzeVariant(int $i, int $j, Election $contest, bool $getAll=true): float
    {
        if(!isset($this->filteredPairwise) && $getAll){
            $this->getAllPairwise($contest);
        }
        
        //echo("Starting EurovisionSchulze::schulzeVariant()\n");
        $nationalVotes = $contest->getVotesManager();
        $nationalMargins = [];
        $iCountry = $contest->getCandidateObjectFromKey($i)->getName();
        $jCountry = $contest->getCandidateObjectFromKey($j)->getName();

        $publicTotal = 0;
        $juryTotal = 0;
        //echo("Going to start the foreach loop.\n");
        foreach ($this->filteredPairwise['Public'] as $country => $nationalPairwise) if ($country !== $iCountry AND $country !== $jCountry)
        {
            $iVotes = $nationalPairwise[$iCountry]['win'][$jCountry];
            $jVotes = $nationalPairwise[$jCountry]['win'][$iCountry];
            //echo("\n\$country = ".$country.",  \$iCountry = ".$iCountry.",  \$jCountry = ".$jCountry.",  \$rawMargin = ".$rawMargin.",  Votes = ".($iVotes + $jVotes)."\n");
            if ($iVotes === $jVotes) {
                $nationalMargins['Public'][$country] = 0;
            } elseif ($iVotes + $jVotes > 0) {
                $nationalMargins['Public'][$country] = $this->warpedNationalPublicMargin($iVotes, $jVotes, $contest->populations[$country]);
            }
            if ($iVotes + $jVotes > 0) {
                $publicTotal += $this->warpedNationalPublicMargin($iVotes+$jVotes, 0, $contest->populations[$country]);
            }
        }

        foreach ($this->filteredPairwise['Jury'] as $country => $nationalPairwise) if ($country !== $iCountry AND $country !== $jCountry)
        {
            $iVotes = $nationalPairwise[$iCountry]['win'][$jCountry];
            $jVotes = $nationalPairwise[$jCountry]['win'][$iCountry];
            //echo("\n\$country = ".$country.",  \$iCountry = ".$iCountry.",  \$jCountry = ".$jCountry.",  \$rawMargin = ".$rawMargin.",  Votes = ".($iVotes + $jVotes)."\n");
            if ($iVotes === $jVotes) {
                $nationalMargins['Jury'][$country] = 0;
            } elseif ($iVotes + $jVotes > 0) {
                $nationalMargins['Jury'][$country] = $this->warpedNationalJuryMargin($iVotes, $jVotes, $contest->populations[$country]);
            }
            if ($iVotes + $jVotes > 0) {
                $juryTotal += $this->warpedNationalJuryMargin($iVotes+$jVotes, 0, $contest->populations[$country]);
            }
        }
        
        //echo('Margin for '.$iCountry.' vs '.$jCountry." is ".array_sum($nationalMargins)."\n");
        $combinedPublic = array_sum($nationalMargins['Public']) * $contest->groupBalance['Public'] / ($publicTotal+0.001);
        $combinedJury = array_sum($nationalMargins['Jury']) * $contest->groupBalance['Jury'] / ($juryTotal+0.001);
        return $combinedPublic + $combinedJury;
    }

    protected function warpedNationalPublicMargin($iVotes, $jVotes, $population): float
    {
        return ($iVotes - $jVotes) * ($population/(($iVotes+$jVotes)*abs($iVotes-$jVotes)))**(1/3);
    }
    protected function warpedNationalJuryMargin($iVotes, $jVotes, $country=NULL): float
    {
        return ($iVotes - $jVotes) * (1/abs($iVotes-$jVotes))**(1/3);
    }

    // Calculate the Strongest Paths
    protected function makeStrongestPaths(): void
    {
        $contest = $this->getElection();
        $CandidatesKeys = array_keys($contest->getCandidatesList());

        foreach ($CandidatesKeys as $i) {
            foreach ($CandidatesKeys as $j) {
                if ($i > $j) {
                    $this->StrongestPaths[$i][$j] = (-1) * $this->StrongestPaths[$j][$i];
                } elseif ($i != $j) {
                    $this->StrongestPaths[$i][$j] = $this->schulzeVariant($i, $j, $contest);
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
