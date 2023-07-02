<?php

declare(strict_types=1);

use CondorcetPHP\Condorcet\Tools\Converters\CondorcetElectionFormat;
use EurovisionVoting\Contest;
use EurovisionVoting\Init;

use EurovisionVoting\resultsNarrative;

require_once __DIR__ .'/../vendor/autoload.php';

ini_set('memory_limit', '2048M');

//const OPTIONS = ['-v', '-n'];
//global $settings = [];

Init::registerMethods();

//public $commandOptions = getopt('v', Condorcet::getAuthMethods);

if (!isset($argv[1])) {
    echo ("You must use a .cvotes file as the first argument. \n");
    exit(0);
}

$votesdata = new CondorcetElectionFormat($argv[1]);
$contest = new Contest;
$votesdata->setDataToAnElection($contest);
$contest->parsePopulations();
$contest->readData();

$contest->countVotersByCountry();

echo('The entire contest has '.$contest->countVotes()." votes.\n");

//new resultsNarrative($contest, 'Eurovision Schulze');
$contest->playResultsNarrative('Eurovision Schulze');

$grand_final = $contest->getResult('Eurovision Schulze')->getResultAsString();
echo ("\nResults from the first method:\n");
var_dump($grand_final);
$grand_final_2 = $contest->getResult('Eurovision Schulze 2')->getResultAsString();
echo ("\nResults from the second method:\n");
var_dump($grand_final_2);
/*$grand_final_3 = $contest->getResult('Eurovision Schulze 3')->getResultAsString();
echo ("\nResults from the third method:\n");
var_dump($grand_final_3);*/
$Schulze = $contest->getResult('Schulze Margin')->getResultAsString();
echo ("\nResults from the regular Schulze Method:\n");
var_dump($Schulze);
