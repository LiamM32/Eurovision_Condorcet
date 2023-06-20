<?php

declare(strict_types=1);

namespace EurovisionVoting;

use CondorcetPHP\Condorcet\Condorcet;
use EurovisionVoting\Method\EurovisionSchulze;
use EurovisionVoting\Method\EurovisionSchulze2;
use EurovisionVoting\Method\EurovisionSchulze3;

class Init {
    public static function registerMethods(): void {
        Condorcet::addMethod(EurovisionSchulze::class);
        Condorcet::addMethod(EurovisionSchulze2::class);
        // Condorcet::addMethod(EurovisionSchulze3::class);
    }
}

