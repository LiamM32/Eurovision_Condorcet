<?php

declare(strict_types=1);

use CondorcetPHP\Condorcet\Tools\Converters\CondorcetElectionFormat;
use EurovisionVoting\Contest;
use EurovisionVoting\Init;
use CondorcetPHP\Condorcet\Tools\Randomizers\VoteRandomizer;

require_once __DIR__ .'/../vendor/autoload.php';

$Methods = ['Eurovision Schulze', 'Eurovision Schulze 1b', 'Eurovision Schulze 1c', 'Eurovision Schulze 2', 'Eurovision Schulze 3'];

Init::registerMethods();

$contest = new Contest;

if (is_numeric($argv[1])) {
    makeRandomVotes($argv[1]);
} else {
    $votesdata = new CondorcetElectionFormat($argv[1]);
}
$votesdata->setDataToAnElection($contest);
$contest->parsePopulations();
$contest->readData();
$contest->countVotersByCountry();

echo('The entire contest has '.$contest->countVotes()." votes.\n");

$cycles = $argv[2] ?? 10;
$timetaken = [];
$resultsList = [];
$resultSets = [];
for ($x = 0; $x < $cycles; $x++) {
    foreach ($Methods as $method) {
        $start_time = microtime(true);
        $result = $contest->getResult($method);
        $timetaken[$method][$x] = (microtime(true)-$start_time);
    }
}

foreach ($Methods as $method) {
    echo ('Method: '.$method.' Average time: '.array_average($timetaken[$method]));
    echo (' Max time: '.max($timetaken[$method]).' RMS: '.rms($timetaken[$method])."\n");
}

//end.

function array_average($array) {
    $array = array_filter($array);
    //echo('first = '.$array[0].', sum = '.array_sum($array).', count = '.count($array)."\n");
    if(count($array)) {
        return array_sum($array)/count($array);
    }
}

function rms($array) {
    $products = [];
    foreach($array as $key=>$entry) {
        $products[$key] = $entry**2;
    }
    return array_sum($products)/array_sum($array);
}

function makeRandomVotes($q) {
    $contestants = json_decode(fread(fopen(__DIR__."/../finalists.json", "r"), 512), true);
    
    $randomizer = new VoteRandomizer($contestants);
    $randomizer->maxCandidatesRanked = 25;
    $randomizer->maxRanksCount = 20;
    $randomizer->tiesProbability = 0.25;
    
    $votesAdded = 0;
    while ($votesAdded < $q) {
        $newVote = $randomizer->getNewVote();
        $contest->addVote($newVote, $contestants[array_rand($contestants)]);
        $votesAdded++;
    }
}
