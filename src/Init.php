<?php

declare(strict_types=1);

namespace EurovisionVoting;

use CondorcetPHP\Condorcet\Condorcet;
use EurovisionVoting\Method\EurovisionSchulze;
use EurovisionVoting\Method\EurovisionSchulze2;
use EurovisionVoting\Method\EurovisionSchulze3;

class Init {
    public static function settings($argv) {
        global $argv, $options, $optCount;
        if (count(array_intersect(['-n','-N'], $argv)) > 0) {
            $options['Mode'] = 'Narrative';
            $options['verbose'] = -1;
            $optCount++;
        }
        if (array_search('-v', $argv) > 0) {
            $options['verbose'] = 1;
            $optCount++;
        } elseif (array_search('-q', $argv) > 0) {
            $options['verbose'] = -1;
            $optCount++;
        }
    }
    public static function registerMethods(): void {
        Condorcet::addMethod(EurovisionSchulze::class);
        Condorcet::addMethod(EurovisionSchulze2::class);
        // Condorcet::addMethod(EurovisionSchulze3::class);
    }
}

