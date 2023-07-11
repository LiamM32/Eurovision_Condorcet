<?php

declare(strict_types=1);

namespace EurovisionVoting\Method;

use CondorcetPHP\Condorcet\Algo\Methods\Schulze\Schulze_Core;
use CondorcetPHP\Condorcet\Election;
use CondorcetPHP\Condorcet\Result;
use EurovisionVoting\Contest;
use EurovisionVoting\Init;

class EurovisionSchulze extends Schulze_Core
{
    public const METHOD_NAME = ['Eurovision Schulze', 'Eurovision Schulze 0', 'Eurovision_Schulze', 'Eurovision_Schulze_0', 'Grand Final'];
    protected array $filteredPairwise;
    protected array $groupCoefficients;
    public bool $testMode;

    public function getResult(int $forceNew = 1, bool $testMode = false): Result
    {
        // Cache
        if ($this->Result !== null AND $forceNew == false) {
            return $this->Result;
        }
        if(!isset($this->filteredPairwise) or $forceNew>=2) {
            $this->getAllPairwise($this->getElection());
        }
        $this->testMode = $testMode;

        //if ($forceNew >= true OR $this->voteCount=null) $this->voteTotal = $this->totalVotes();

        // Format array
        $this->prepareStrongestPath();
        // Strongest Paths calculation
        $this->makeStrongestPaths();
        // Ranking calculation
        $this->makeRanking();

        $this->Result->margins = $this->StrongestPaths;

        // Return
        return $this->Result;
    }
    protected function getAllPairwise(Contest $contest)
    {
        foreach (array_keys($contest->groupBalance) as $group) {
            foreach ($contest->votingCountries as $country) {
                $this->filteredPairwise[$group][$country] = $contest->getExplicitFilteredPairwiseByTags([$group, $country], 2);
            }

            if (count($contest->groupBalance) > 1) {
                $this->groupCoefficients[$group] = $contest->groupBalance[$group] / (array_sum($contest->groupBalance)-$contest->groupBalance[$group]);
            } else {
                $this->groupCoefficients[$group] = 1;
            }

        }
        $this->filteredPairwise['Public']['WLD'] = $contest->getExplicitFilteredPairwiseByTags('WLD');
        if (Init::$options['v'] > 0) echo("Finished getAllPairwise()\n");
    }

    public function cacheOnePairwise(string $group, string $country)
    {
        $this->filteredPairwise[$group][$country] = $this->getElection()->getExplicitFilteredPairwiseByTags([$group,$country], 2);
    }

    public function estimateWeights(Contest $contest, array $countries) {
        $weights = [];
        foreach($countries as $country) {
            $weights[$country] = $this->warpedNationalPublicMargin($contest->countVotes('Public', $country), 0, $contest->populations[$country]) ?? 0;
            $weights[$country] += $this->warpedNationalJuryMargin($contest->countVotes('Jury', $country), 0) ?? 0;
        }
        $weights = sort($weights);
        return $weights;
    }

    protected function totalVotes() {
        $contest = $this->getElection();

        $groupContributions = [];

        foreach ($contest->votingGroups as $group) {
            foreach ($this->filteredPairwise['Public'] as $country => $nationalPairwise) if ($country !== $iCountry AND $country !== $jCountry)
            {
                $votes = $contest->countVotes([$group, $country]);
                if ($group === 'Public') {
                    $groupContributions['Public'] += $this->warpedNationalPublicMargin($votes, 0, $contest->populations[$country]);
                } else {
                    $groupContributions[$group] += $this->warpedNationalJuryMargin($votes, 0, $contest->populations[$country]);
                }
            }
            $groupContributions[$group] *= $contest->groupBalance[$group];
        }
        return array_sum($groupContributions);
    }

    protected function schulzeVariant(int $i, int $j, Election $contest, bool $measuring=false): float
    {
        $groupMargins = []; //['Public'=>0.0, 'Jury'=>0.0];
        $iCountry = $contest->getCandidateObjectFromKey($i)->getName();
        $jCountry = $contest->getCandidateObjectFromKey($j)->getName();

        $groupContributions = [];

        foreach ($this->filteredPairwise as $group=>$groupPairwise) {
            $groupContributions[$group] = 0.0;
            $groupMargins[$group] = 0.0;
            foreach ($groupPairwise as $country => $nationalPairwise) if ($country !== $iCountry and $country !== $jCountry) {
                $iVotes = $nationalPairwise[$iCountry]['win'][$jCountry];
                $jVotes = $nationalPairwise[$jCountry]['win'][$iCountry];
                //echo("\n\$country = ".$country.",  \$iCountry = ".$iCountry.",  \$jCountry = ".$jCountry.",  \$rawMargin = ".$rawMargin.",  Votes = ".($iVotes + $jVotes)."\n");
                if ($iVotes + $jVotes > 0) {
                    if ($iVotes !== $jVotes) {
                        if ($group === 'Public') $groupMargins['Public'] += $this->warpedNationalPublicMargin($iVotes, $jVotes, $contest->populations[$country]);
                        else                        $groupMargins[$group] += $this->warpedNationalJuryMargin($iVotes, $jVotes, $contest->populations[$country]);
                    }
                    if ($group === 'Public') $groupContributions[$group] += $this->warpedNationalPublicMargin($iVotes + $jVotes, 0, $contest->populations[$country]);
                    else                      $groupContributions[$group] += $this->warpedNationalJuryMargin($iVotes + $jVotes, 0, $contest->populations[$country]);
                }
            }
            $groupContributions[$group] *= $contest->groupBalance[$group];
        }

        $one = (int) !$this->testMode; //Typically set to 1
        foreach ($groupMargins as $group=>&$margin) {
            $margin *= ($one+array_sum($groupContributions)-$groupContributions[$group]) * $contest->groupBalance[$group];

            if ($this->testMode) $margin = round($margin, 9);
        }

        return array_sum($groupMargins) / (1 + array_sum($groupContributions)); //$netCombinedMargin;
    }

    protected function warpedNationalPublicMargin($iVotes, $jVotes, $population=1): float
    {
        return ($iVotes - $jVotes) * (1/abs($iVotes-$jVotes))**(1/3);
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
