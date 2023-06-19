<?php

declare(strict_types=1);

use CondorcetPHP\Condorcet\Condorcet;
use CondorcetPHP\Condorcet\Election;
use CondorcetPHP\Condorcet\Tools\Converters\CondorcetElectionFormat;
use EurovisionVoting\Method\EurovisionSchulze;
use EurovisionVoting\Method\EurovisionSchulze2;
use EurovisionVoting\Contest;

require_once 'vendor/autoload.php';

if (!isset($argv[1])) {
    echo ("You must use a .cvotes file as the first argument. \n");
    exit(0);
}

ini_set('memory_limit', '2048M');

Condorcet::addMethod(EurovisionSchulze::class);
Condorcet::addMethod(EurovisionSchulze2::class);

$votesdata = new CondorcetElectionFormat($argv[1]);
$contest = new Contest;
$contest = $votesdata->setDataToAnElection($contest);
$contest->getPopulations();
$contest->readData();

$contest->countVotersByCountry();

echo('The entire contest has '.$contest->countVotes()." votes.\n");

$grand_final = $contest->getResult('Eurovision Schulze')->getResultAsString();
echo ("\nResults from the first method:\n");
var_dump($grand_final);
$grand_final_2 = $contest->getResult('Eurovision Schulze')->getResultAsString();
echo ("\nResults from the second method:\n");
var_dump($grand_final_2);