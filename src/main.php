<?php

declare(strict_types=1);

use CondorcetPHP\Condorcet\Condorcet;
use CondorcetPHP\Condorcet\Election;
use CondorcetPHP\ModulesSkeletons\Method\MyVerySimpleMethod;

require_once 'vendor/autoload.php';

Condorcet::addMethod(MyVerySimpleMethod::class);

$election = new Election();

$election->addCandidate('A');
$election->addCandidate('B');
$election->addCandidate('C');

$election->addVote('A>B>C');

var_dump(MyVerySimpleMethod::METHOD_NAME[0]);
var_dump(
    $election->getResult('My Very Simple Method')->getResultAsString()
);
