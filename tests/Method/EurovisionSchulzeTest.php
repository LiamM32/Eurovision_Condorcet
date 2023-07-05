<?php

declare(strict_types=1);

namespace EurovisionVoting\Tests\Method;

//use CondorcetPHP\Condorcet\Tools\Randomizers\VoteRandomizer;
use EurovisionVoting\Contest;
use EurovisionVoting\Init;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertTrue;

final class EurovisionSchulzeTest extends TestCase
{
    private readonly Contest $contest;

    protected function setUp(): void
    {
        Init::registerMethods();
        $this->contest = new Contest();

        $this->contest->addCandidate('SWE');
        $this->contest->addCandidate('FIN');
        $this->contest->addCandidate('CHE');
        $this->contest->votingCountries = ['DEU', 'FRA'];
    }

    public function testGroupBalance(): void
    {
        array_push($this->contest->votingCountries, 'EST');
        $this->contest->groupBalance = ['Public'=>0.5, 'Jury'=>0.5];

        $this->contest->parseVotes('
            Jury, DEU   || SWE > FIN > CHE *2
            Jury, FRA   || SWE > FIN > CHE *2
            Public, DEU || FIN > SWE > CHE *64
            Public, FRA || FIN > SWE > CHE *64
            Public, EST || FIN > SWE > CHE *2
        ');
        $result = $this->contest->getResult('Eurovision Schulze 0');
        self::assertSame('FIN > SWE > CHE', $result->getResultAsString());
    }
}
