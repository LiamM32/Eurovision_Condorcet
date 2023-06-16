<?php

declare(strict_types=1);

use CondorcetPHP\Condorcet\Condorcet;
use CondorcetPHP\Condorcet\Election;
use CondorcetPHP\Condorcet\Tools\Randomizers\VoteRandomizer;
use EurovisionVoting\Method\EurovisionSchulze;
use EurovisionVoting\Contest;

require_once 'vendor/autoload.php';

Condorcet::addMethod(EurovisionSchulze::class);

$contest = new election;
$contest = new Contest;
$contest->getPopulations();
$contest->readData();

$contestants = json_decode(fread(fopen("finalists.json", "r"), 512), true);
foreach ($contestants as $country) {
    $contest->addCandidate($country);
}
$votingCountries = json_decode(fread(fopen("countries.json", "r"), 512), true);

$randomizer = new VoteRandomizer($contestants);
$randomizer->maxCandidatesRanked = 25;
$randomizer->maxRanksCount = 20;
$randomizer->tiesProbability = 0.25;

var_dump($contest->getResult('Eurovision Schulze')->getResultAsString());