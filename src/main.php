<?php

declare(strict_types=1);

use CondorcetPHP\Condorcet\Condorcet;
use CondorcetPHP\Condorcet\Election;
use CondorcetPHP\Condorcet\Tools\Converters\CondorcetElectionFormat;
use EurovisionVoting\Method\EurovisionSchulze;
use EurovisionVoting\Contest;

require_once 'vendor/autoload.php';

if ($argv[1]===NULL) {
    echo ("You must use a .cvotes file as the first argument. \n");
    exit(0);
}

Condorcet::addMethod(EurovisionSchulze::class);

$votesdata = new CondorcetElectionFormat($argv[1]);
$contest = new Contest;
$contest = $votesdata->setDataToAnElection($contest);
$contest->getPopulations();
$contest->readData();

/*$contestants = json_decode(fread(fopen("finalists.json", "r"), 512), true);
foreach ($contestants as $country) {
    $contest->addCandidate($country);
}*/

/*for ($v=0; $v<2048; $v++) {
    $vote = $contest->addVote($randomizer->getNewVote());
    $vote->addTags($contest->votingCountries[array_rand($contest->votingCountries, 1)]);
}*/

var_dump($contest->getResult('Eurovision Schulze')->getResultAsString());