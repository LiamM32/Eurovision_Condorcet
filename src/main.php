<?php

declare(strict_types=1);

use CondorcetPHP\Condorcet\Tools\Converters\CondorcetElectionFormat;
use EurovisionVoting\Contest;
use EurovisionVoting\ContestNarrative;
use EurovisionVoting\Init;
use EurovisionVoting\tools;

use SebastianBergmann\CliParser\Parser;

require_once __DIR__ .'/../vendor/autoload.php';

ini_set('memory_limit', '2048M');
$stdin = STDIN;
//const OPTIONS = ['-v', '-n', '-N'];
$options = ['verbose'=>0, 'Mode'=>'default'];
$optCount = 0;
Init::settings($argv);
Init::registerMethods();

//public $commandOptions = getopt('v', Condorcet::getAuthMethods);

if (!isset($argv[1+$optCount])) {
    echo ("You must use a .cvotes file as the first argument. \n");
    exit(0);
}

$votesdata = new CondorcetElectionFormat($argv[1+$optCount]);
if ($options['Mode'] == 'Narrative') {
    $contest = new ContestNarrative;
} else {
    $contest = new Contest;
}
$votesdata->setDataToAnElection($contest);
$contest->parsePopulations();
$contest->readData();

$contest->countVotersByCountry();

echo('The entire contest has '.$contest->countVotes()." votes.\n");

//new resultsNarrative($contest, 'Eurovision Schulze');
$method = $argv[2 + $optCount] ?? null /*?? 'Eurovision Schulze'*/;
if ($options['Mode'] == 'Narrative') {
    echo("Doing narrative mode.\n");
    usleep(400000);
    $contest->playResultsNarrative('Eurovision Schulze');
} elseif (isset($method)) {
    $grand_final = $contest->getResult($method)->getResultAsString();
    var_dump($grand_final);
} else {
    $grand_final = $contest->getResult('Eurovision Schulze')->getResultAsString();
    echo("\nResults from the first method:\n");
    var_dump($grand_final);
    $grand_final_2 = $contest->getResult('Eurovision Schulze 2')->getResultAsString();
    echo("\nResults from the second method:\n");
    var_dump($grand_final_2);
    $Schulze = $contest->getResult('Schulze Margin')->getResultAsString();
    echo("\nResults from the regular Schulze Method:\n");
    var_dump($Schulze);
}