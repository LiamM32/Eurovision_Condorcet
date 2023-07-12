<?php

declare(strict_types=1);

namespace EurovisionVoting\Method;

use CondorcetPHP\Condorcet\Algo\Methods\Schulze\Schulze_Core;
use CondorcetPHP\Condorcet\Election;
use EurovisionVoting\Contest;

class EurovisionSchulze1 extends EurovisionSchulze
{
    public const METHOD_NAME = ['Eurovision Schulze 1', 'Eurovision_Schulze_1', 'Grand Final 1'];
    protected array $populations;

    protected function warpedNationalPublicMargin($iVotes, $jVotes, $country=NULL): float
    {
        $population = $this->populations[$country];
        return ($iVotes - $jVotes) * ($population/(($iVotes+$jVotes)*abs($iVotes-$jVotes)))**(1/3);
    }
    protected function warpedNationalJuryMargin($iVotes, $jVotes, $country=NULL): float
    {
        return ($iVotes - $jVotes) * (1/abs($iVotes-$jVotes))**(1/3);
    }

    public function loadData(): void
    {
        $contest = $this->getElection();
        $this->populations = &$contest->populations;
    }
}
