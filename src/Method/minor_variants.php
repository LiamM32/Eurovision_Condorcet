<?php

declare(strict_types=1);

namespace EurovisionVoting\Method;

use CondorcetPHP\Condorcet\Algo\Methods\Schulze\Schulze_Core;
use CondorcetPHP\Condorcet\Election;
use EurovisionVoting\Contest;

class EurovisionSchulze1b extends EurovisionSchulze
{
    public const METHOD_NAME = ['Eurovision Schulze 1b'];
    use pairwisefromMinimax;
}

class EurovisionSchulze1c extends EurovisionSchulze
{
    public const METHOD_NAME = ['Eurovision Schulze 1c'];
    use optimizedPaths;
}

class EurovisionSchulze1d extends EurovisionSchulze
{
    public const METHOD_NAME = ['Eurovision Schulze 1d'];
    use optimizedPaths;
    use pairwisefromMinimax;
}

trait pairwisefromMinimax
{
    protected function getAllPairwise(Contest $contest)

    {
        foreach ($contest->votingCountries as $country) {
            $this->filteredPairwise[$country] = $contest->getResult(method: 'Minimax Winning', methodOptions: ['%tagFilter' => true, 'withTag' => true, 'tags' => $country])->pairwise;
        }
        $this->filteredPairwise['WLD'] = $contest->getResult(method: 'Minimax Winning', methodOptions: ['%tagFilter' => true, 'withTag' => false, 'tags' => $country])->pairwise;
    }
}

trait optimizedPaths
{
    // Calculate the direct paths
    protected function makeStrongestPaths(): void
    {
        $contest = $this->getElection();
        $CandidatesKeys = array_keys($contest->getCandidatesList());

        foreach ($CandidatesKeys as $i) {
            foreach ($CandidatesKeys as $j) {
                if ([$i] > [$j]) {
                    $this->StrongestPaths[$i][$j] = (-1) * $this->StrongestPaths[$j][$i];
                } elseif ($i != $j) {
                    $this->StrongestPaths[$i][$j] = $this->schulzeVariant($i, $j, $contest);
                }
            }
        }
        $this->finaliseStrongestPaths($contest, $CandidatesKeys);
    }
}