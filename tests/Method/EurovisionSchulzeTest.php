<?php

declare(strict_types=1);

namespace EurovisionVoting\Tests\Method;

//use CondorcetPHP\Condorcet\Tools\Randomizers\VoteRandomizer;
use EurovisionVoting\Contest;
use EurovisionVoting\Init;
use EurovisionVoting\Method\EurovisionSchulze;
use PHPUnit\Framework\TestCase;

const BACKSPACE6 = "\b\b\b\b\b\b";

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
        ');
        $method = new EurovisionSchulze($this->contest);

        $result = $this->contest->getResult('Eurovision Schulze 0', ['testMode'=>true]);
        self::assertSame('FIN = SWE > CHE', $result->getResultAsString());

        $result = $this->contest->getResult('Eurovision Schulze 0', ['testMode'=>false]);
        self::assertSame('FIN > SWE > CHE', $result->getResultAsString());

        $this->contest->parseVotes('Public, DEU || SWE > FIN > CHE *24');
        $result = $this->contest->getResult('Eurovision Schulze 0', ['testMode'=>true]);
        self::assertSame('SWE > FIN > CHE', $result->getResultAsString());

    }
}
