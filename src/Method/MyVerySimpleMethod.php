<?php

declare(strict_types=1);

namespace CondorcetPHP\ModulesSkeletons\Method;

use CondorcetPHP\Condorcet\Algo\Method;
use CondorcetPHP\Condorcet\Algo\MethodInterface;
use CondorcetPHP\Condorcet\Vote;

class MyVerySimpleMethod extends Method implements MethodInterface
{
    public const METHOD_NAME = [
        'My Very Simple Method',
        'My Very Simple Method first alias',
        'My Very Simple Method Main another alias'
    ];

    protected function compute(): void
    {
        # Ranking needs an ordered array of internal candidate keys
        # For a rank tie: create a subarray with candidate on the rank
        # Here this method just select the ranking from the first vote

        foreach($this->getElection()->getVotesValidUnderConstraintGenerator() as $oneVote) {
            $ranking = $oneVote->getContextualRankingWithoutSort($this->getElection());

            foreach($ranking as &$oneRank) {
                foreach($oneRank as &$candidateInrank) {
                    $candidateInrank = $this->getElection()->getCandidateKey($candidateInrank);
                }
            }

            continue;
        }

        $this->Result = $this->createResult($ranking);
    }

    protected function getStats(): array
    {
        return ['explanation' => "First win, that's it."];
    }
}
