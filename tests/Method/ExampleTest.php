<?php

declare(strict_types=1);

use CondorcetPHP\Condorcet\Tools\Randomizers\VoteRandomizer;
use EurovisionVoting\Contest;
use EurovisionVoting\Init;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertTrue;

final class ExampleTest extends TestCase
{
    private readonly Contest $contest;

    protected function setUp(): void
    {
        Init::registerMethods();

        $this->contest = new Contest();
    }

    public function testExample(): void
    {
        self::assertTrue(true);
    }
}
