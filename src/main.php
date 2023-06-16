<?php

declare(strict_types=1);

use CondorcetPHP\Condorcet\Condorcet;
use CondorcetPHP\Condorcet\Election;
use CondorcetPHP\ModulesSkeletons\Constraint\MinTwoCandidates;
use CondorcetPHP\ModulesSkeletons\Method\MyVerySimpleMethod;

require_once __DIR__ . '/../vendor/autoload.php';

# Register the new method
Condorcet::addMethod(MyVerySimpleMethod::class);

$election = new Election();

# Register the new constraint
$election->addConstraint(MinTwoCandidates::class);

# Register the candidates
$election->addCandidate('A');
$election->addCandidate('B');
$election->addCandidate('C');

# register the votes
$election->addVote('A>B>C');

# Dump results
var_dump(MyVerySimpleMethod::METHOD_NAME[0]);
var_dump(
    $election->getResult('My Very Simple Method')->getResultAsString()
);
