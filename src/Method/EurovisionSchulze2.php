<?php

declare(strict_types=1);

namespace EurovisionVoting\Method;

use CondorcetPHP\Condorcet\Algo\Methods\Schulze\Schulze_Core;
use CondorcetPHP\Condorcet\Election;
use EurovisionVoting\Contest;

class EurovisionSchulze2 extends EurovisionSchulze
{
    public const METHOD_NAME = ['Eurovision Schulze 2', 'Grand Final square root'];

    protected function warpedNationalPublicMargin($iVotes, $jVotes, $country=NULL): float
    {
        return ($iVotes-$jVotes) / sqrt($iVotes+$jVotes);
    }
    protected function warpedNationalJuryMargin($iVotes, $jVotes, $country=NULL): float
    {
        return ($iVotes-$jVotes) / sqrt($iVotes+$jVotes);
    }
}
