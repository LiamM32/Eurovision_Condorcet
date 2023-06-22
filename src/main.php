<?php

declare(strict_types=1);

use CondorcetPHP\Condorcet\Tools\Converters\CondorcetElectionFormat;
use EurovisionVoting\Contest;
use EurovisionVoting\Init;

require_once __DIR__ .'/../vendor/autoload.php';

if (!isset($argv[1])) {
    echo ("You must use a .cvotes file as the first argument. \n");
    exit(0);
}

ini_set('memory_limit', '2048M');

Init::registerMethods();

$votesdata = new CondorcetElectionFormat($argv[1]);
$contest = new Contest;
$votesdata->setDataToAnElection($contest);
$contest->parsePopulations();
$contest->readData();

$contest->countVotersByCountry();

echo('The entire contest has '.$contest->countVotes()." votes.\n");

$grand_final = $contest->getResult('Eurovision Schulze')->getResultAsString();
echo ("\nResults from the first method:\n");
var_dump($grand_final);
$grand_final_2 = $contest->getResult('Eurovision Schulze 2')->getResultAsString();
echo ("\nResults from the second method:\n");
var_dump($grand_final_2);
$Schulze = $contest->getResult('Schulze Margin')->getResultAsString();
echo ("\nResults from the regular Schulze Method:\n");
var_dump($Schulze);
