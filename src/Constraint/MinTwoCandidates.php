<?php
/*
    Condorcet PHP - Election manager and results calculator.
    Designed for the Condorcet method. Integrating a large number of algorithms extending Condorcet. Expandable for all types of voting systems.
    By Julien Boudry and contributors - MIT LICENSE (Please read LICENSE.txt)
    https://github.com/julien-boudry/Condorcet
*/

declare(strict_types=1);

namespace CondorcetPHP\ModulesSkeletons\Constraint;

use CondorcetPHP\Condorcet\{Election, Vote, VoteConstraintInterface};

class MinTwoCandidates implements VoteConstraintInterface
{
    public static function isVoteAllow(Election $election, Vote $vote): bool
    {
        return count($vote->getAllCandidates($election)) >= 2;
    }
}
