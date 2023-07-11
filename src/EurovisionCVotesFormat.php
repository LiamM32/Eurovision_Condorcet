<?php

namespace EurovisionVoting;

use CondorcetPHP\Condorcet\Candidate;
use CondorcetPHP\Condorcet\Dev\CondorcetDocumentationGenerator\CondorcetDocAttributes\FunctionParameter;
use CondorcetPHP\Condorcet\Election;
use CondorcetPHP\Condorcet\Tools\Converters\CEF\StandardParameter;
use \CondorcetPHP\Condorcet\Tools\Converters\CEF\CondorcetElectionFormat;
use CondorcetPHP\Condorcet\Utils\CondorcetUtil;

class EurovisionCVotesFormat extends CondorcetElectionFormat
{
    public readonly array $groupBalance;

    public function __construct(\SplFileInfo|string $input)
    {
        parent::__construct($input);
        $this->parseGroupBalance();
    }

    public function setDataToContest(
        Contest $contest = new Contest,
        ?\Closure $callBack = null
    ): Contest {

        $contest = $this->setDataToAnElection($contest, $callback ?? null);
        $contest->groupBalance = $this->groupBalance;
        return $contest;
    }
    protected function parseGroupbalance(): void
    {
        $groupBalance = $this->parameters['Group balance'] ?? $this->parameters['Groups'] ?? $this->parameters['group balance'] ?? '';
        $groupBalance = explode(';', $groupBalance);

        if ($groupBalance !== ['']) foreach ($groupBalance as $key => $string) {
            $pair = explode("=", $string, 3);
            $groupBalance[trim($pair[0])] = floatval($pair[1]);
            unset($groupBalance[$key]);
        } else unset($groupBalance[0]);

        if (!isset($groupBalance['Public'])) {
            if (array_sum($groupBalance) < 1) $groupBalance['Public'] = 1 - array_sum($groupBalance);
            else $groupBalance['Public'] = 1.0;

        }

        $this->groupBalance = $groupBalance;
    }
}