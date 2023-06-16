<?php

declare(strict_types=1);

namespace FancyVoting\Method;

use CondorcetPHP\Condorcet\Algo\Method;
use CondorcetPHP\Condorcet\Algo\MethodInterface;

class Fancy_Method extends Method implements MethodInterface
{
    public const METHOD_NAME = [
        'Fancy Method'
    ];

    protected function compute(): void
    {
        # Ranking needs an ordered array of internal candidate keys
        # For a rank tie: create a subarray with candidate on the rank
        # Here we just get the candidates keys, randomize it, and serve it as a result
        $ranking = array_keys($this->getElection()->getCandidatesList());
        $ranking = (new \Random\Randomizer())->shuffleArray($ranking);

        $this->Result = $this->createResult($ranking);
    }

    protected function getStats(): array
    {
        return [];
    }
}
