<?php

declare(strict_types=1);

use CondorcetPHP\Condorcet\Condorcet;
use CondorcetPHP\Condorcet\Election;
use FancyVoting\Method\Fancy_Method;

require_once 'vendor/autoload.php';

Condorcet::addMethod(Fancy_Method::class);

$election = new Election();

$election->addCandidate('A');
$election->addCandidate('B');
$election->addCandidate('C');

$election->addVote('A>B>C');

var_dump(Fancy_Method::METHOD_NAME[0]);
var_dump(
    $election->getResult('My Very Simple Method')->getResultAsString()
);
