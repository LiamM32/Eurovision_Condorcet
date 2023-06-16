<?php

declare(strict_types=1);

use CondorcetPHP\Condorcet\Condorcet;
use CondorcetPHP\Condorcet\Election;
use CondorcetPHP\ModulesSkeletons\Constraint\MinTwoCandidates;
use CondorcetPHP\ModulesSkeletons\Method\MyVerySimpleMethod;
use PHPUnit\Framework\TestCase;

final class MinTwoCandidatesTest extends TestCase
{
    private readonly Election $election;

    protected function setUp(): void
    {
        Condorcet::addMethod(MyVerySimpleMethod::class);

        $this->election = new Election();
        $this->election->addConstraint(MinTwoCandidates::class);
        $this->election->setImplicitRanking(false);
    }

    public function testMyConstraint(): void
    {
        $this->election->addCandidate('A');
        $this->election->addCandidate('B');
        $this->election->addCandidate('C');

        $this->election->addVote('A'); // Rank only one candidate

        self::assertSame(
            expected: 'A = B = C',
            actual: $this->election->getResult(MyVerySimpleMethod::class)->getResultAsString()
        );
    }
}
