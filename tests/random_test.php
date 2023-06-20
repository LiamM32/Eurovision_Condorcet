<?php

declare(strict_types=1);

use CondorcetPHP\Condorcet\Condorcet;
use CondorcetPHP\Condorcet\Tools\Randomizers\VoteRandomizer;
use EurovisionVoting\Method\EurovisionSchulze;
use EurovisionVoting\Contest;

require_once 'vendor/autoload.php';

Condorcet::addMethod(EurovisionSchulze::class);

$contest = new Contest;
$contest->parsePopulations();
$contest->readData();

$contestants = json_decode(fread(fopen("finalists.json", "r"), 512), true);
foreach ($contestants as $country) {
    $contest->addCandidate($country);
}


$votingCountries = json_decode(fread(fopen("voting-countries.json", "r"), 512), true);

$randomizer = new VoteRandomizer($contestants);
$randomizer->maxCandidatesRanked = 25;
$randomizer->maxRanksCount = 20;
$randomizer->tiesProbability = 0.25;

var_dump($randomizer->candidates);

$votesAdded = 0;
while ($votesAdded < $argv[1]) {
    $newVote = $randomizer->getNewVote();
    //$newVote->addTags($contestants[array_rand($contestants)]);
    //echo ($newVote->getSimpleRanking());
    $contest->addVote($newVote, $contestants[array_rand($contestants)]);
    $votesAdded++;
}
$contest->addVote($randomizer->getNewVote());
$contest->addVote($randomizer->getNewVote());
$contest->addVote($randomizer->getNewVote());

echo("Made it here!\n");
$contest->getVotesListAsString();

var_dump($contest->getResult('Eurovision Schulze')->getResultAsString());