<?php

declare(strict_types=1);

use CondorcetPHP\Condorcet\Tools\Randomizers\VoteRandomizer;
use EurovisionVoting\Contest;
use EurovisionVoting\Init;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertTrue;

final class RandomTest extends TestCase
{
    private readonly Contest $contest;

    protected function setUp(): void
    {
        Init::registerMethods();

        $this->contest = new Contest();
    }

    public function testRandomResults(): void
    {
        $this->contest->parsePopulations();
        $this->contest->readData();

        $contestants = json_decode(fread(fopen("finalists.json", "r"), 512), true);
        foreach ($contestants as $country) {
            $this->contest->addCandidate($country);
        }


        $votingCountries = json_decode(fread(fopen("voting-countries.json", "r"), 512), true);

        $randomizer = new VoteRandomizer($contestants);
        $randomizer->maxCandidatesRanked = 25;
        $randomizer->maxRanksCount = 20;
        $randomizer->tiesProbability = 0.25;

        var_dump($randomizer->candidates);
        self::assertSame([], $randomizer->candidates);


        $votesAdded = 0;
        while ($votesAdded < $argv[1]) {
            $newVote = $randomizer->getNewVote();
            //$newVote->addTags($contestants[array_rand($contestants)]);
            //echo ($newVote->getSimpleRanking());
            $this->contest->addVote($newVote, $contestants[array_rand($contestants)]);
            $votesAdded++;
        }
        $this->contest->addVote($randomizer->getNewVote());
        $this->contest->addVote($randomizer->getNewVote());
        $this->contest->addVote($randomizer->getNewVote());

        echo("Printing votes:\n");
        echo($this->contest->getVotesListAsString());

        $result = $this->contest->getResult('Eurovision Schulze')->getResultAsString();

        self::assertSame('', $result);
    }
}
