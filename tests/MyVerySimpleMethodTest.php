<?php

declare(strict_types=1);

use CondorcetPHP\Condorcet\Condorcet;
use CondorcetPHP\Condorcet\Election;
use CondorcetPHP\ModulesSkeletons\Method\MyVerySimpleMethod;
use PHPUnit\Framework\TestCase;

final class MyVerySimpleMethodTest extends TestCase
{
    private readonly Election $election;

    protected function setUp(): void
    {
        Condorcet::addMethod(MyVerySimpleMethod::class);

        $this->election = new Election();
    }

    public function testMyVerySimpleMethod(): void
    {
        $this->election->addCandidate('A');
        $this->election->addCandidate('B');
        $this->election->addCandidate('C');

        $this->election->addVote('A>B>C');

        self::assertSame(
            expected: 'A > B > C',
            actual: $this->election->getResult(MyVerySimpleMethod::class)->getResultAsString()
        );

        self::assertSame(
            expected: ['explanation' => "First win, that's it."],
            actual: $this->election->getResult(MyVerySimpleMethod::class)->getStats()
        );
    }
}
