<?php

declare(strict_types=1);

namespace EurovisionVoting;
//use EurovisionVoting\Method\minor_variants;
use CondorcetPHP\Condorcet\Condorcet;
use EurovisionVoting\Method\EurovisionSchulze;
use EurovisionVoting\Method\EurovisionSchulze2;
use EurovisionVoting\Method\EurovisionSchulze3;
use EurovisionVoting\Method\EurovisionSchulze1b;
use EurovisionVoting\Method\EurovisionSchulze1c;
use EurovisionVoting\Method\EurovisionSchulze1d;

require_once __DIR__ . '/Method/minor_variants.php';

class Init {
    public static function registerMethods(): void {
        Condorcet::addMethod(EurovisionSchulze::class);
        Condorcet::addMethod(EurovisionSchulze2::class);
        Condorcet::addMethod(EurovisionSchulze3::class);
        Condorcet::addMethod(EurovisionSchulze1b::class);
        Condorcet::addMethod(EurovisionSchulze1c::class);
        Condorcet::addMethod(EurovisionSchulze1d::class);
    }
}

