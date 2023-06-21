<?php

declare(strict_types=1);

use CondorcetPHP\Condorcet\Condorcet;
use CondorcetPHP\Condorcet\Tools\Converters\CondorcetElectionFormat;
use EurovisionVoting\Method\EurovisionSchulze;
use EurovisionVoting\Method\EurovisionSchulze2;
use EurovisionVoting\Method\EurovisionSchulze3;
use EurovisionVoting\Contest;

require_once __DIR__.'/../vendor/autoload.php';

if (!isset($argv[1])) {
    echo ("You must use a .cvotes file as the first argument. \n");
    exit(0);
}

ini_set('memory_limit', '2048M');

Condorcet::addMethod(EurovisionSchulze::class);
Condorcet::addMethod(EurovisionSchulze2::class);
Condorcet::addMethod(EurovisionSchulze3::class);

$votesdata = new CondorcetElectionFormat($argv[1]);
$contest = new Contest;
$contest = $votesdata->setDataToAnElection($contest);
$contest->parsePopulations();
$contest->readData();

$contest->countVotersByCountry();

echo('The entire contest has '.$contest->countVotes()." votes.\n");

$grand_final = $contest->getResult('Eurovision Schulze')->getResultAsString();
echo ("\nResults from the first method:\n");
var_dump($grand_final);
/*$grand_final_2 = $contest->getResult('Eurovision Schulze 2')->getResultAsString();
echo ("\nResults from the second method:\n");
var_dump($grand_final_2);
$grand_final_3 = $contest->getResult('Eurovision Schulze 3')->getResultAsString();
echo ("\nResults from the third method:\n");
var_dump($grand_final_3);*/
$Schulze = $contest->getResult('Schulze Margin')->getResultAsString();
echo ("\nResults from the regular Schulze Method:\n");
var_dump($Schulze);