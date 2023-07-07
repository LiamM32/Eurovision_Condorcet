<?php

declare(strict_types=1);

use CondorcetPHP\Condorcet\Tools\Converters\CondorcetElectionFormat;
use EurovisionVoting\Contest;
use EurovisionVoting\ContestNarrative;
use EurovisionVoting\Init;
use EurovisionVoting\tools;

require_once __DIR__ .'/../vendor/autoload.php';

ini_set('memory_limit', '4096M');
$stdin = STDIN;

Init::settings($argv);
Init::registerMethods();
$options = &Init::$options;
$optCount = &Init::$optCount;

//public $commandOptions = getopt('v', Condorcet::getAuthMethods);

if (!isset($argv[1+$optCount])) {
    echo ("You must use a .cvotes file as the first argument. \n");
    exit(0);
}

$votesdata = new CondorcetElectionFormat($argv[1+$optCount]);
if ($options['mode'] == 'Narrative') {
    $contest = new ContestNarrative;
} else {
    $contest = new Contest;
}
$votesdata->setDataToAnElection($contest);
$contest->groupBalance = Init::parseGroupBalance($argv[1+$optCount]);
$contest->parsePopulations();
$contest->readData();
$contest->countVotersByCountry();

if ($options['v'] >= 0) echo('The entire contest has '.$contest->countVotes()." votes.\n");

//new resultsNarrative($contest, 'Eurovision Schulze');
$method = $argv[2+$optCount] ?? null /*?? 'Eurovision Schulze'*/;
if ($options['mode'] == 'Narrative') {
    echo("Doing narrative mode.\n");
    usleep(400000);
    $contest->playResultsNarrative('Eurovision Schulze');
} elseif (isset($method)) {
    $result_string = $contest->getResult($method)->getResultAsString();
    if ($options['v']>=0) echo("\nResults for ".$method."\n");
    echo($result_string."\n");
} else {
    $result_string = $contest->getResult('Eurovision Schulze 0')->getResultAsString();
    echo("\nResults from Eurovision Schulze 0:\n");
    echo($result_string."\n");
    $result_string = $contest->getResult('Eurovision Schulze 1')->getResultAsString();
    echo("\nResults from Eurovision Schulze 1:\n");
    echo($result_string."\n");
    $result_string_2 = $contest->getResult('Eurovision Schulze 2')->getResultAsString();
    echo("\nResults from Eurovision Schulze 2:\n");
    echo($result_string_2."\n");
    $Schulze = $contest->getResult('Schulze Margin')->getResultAsString();
    echo("\nResults from the regular Schulze Margins:\n");
    echo($Schulze."\n");
}