<?php

namespace EurovisionVoting;

class tools
{
    // Get's the n'th largest value in an array.
    public static function large(array $array, int $rank)
    {
        sort($array);
        return $array[sizeof[$array]-$rank];
    }

   // Copied from here: https://stackoverflow.com/a/11872928/6157763
    public static function getRandomWeightedElement(array $weightedValues) {
        $rand = rand(1, (int) array_sum($weightedValues));

        foreach ($weightedValues as $key => $value) {
            $rand -= $value;
            if ($rand <= 0) {
                return $key;
            }
        }
    }

    public static function array_reciprocal (array $array, $addend = 0) {
        foreach ($array as &$element) {
            $element = 1 / ($element + $addend);
        }
        return $array;
    }

    // From https://stackoverflow.com/a/1320156/6157763
    public static function flatten(array $array) {
        $return = array();
        array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
        return $return;
    }

    // Like the standard count() but doesn't throw errors. Returns 0 for null and empty arrays, and 1 for non-array data types.
    public static function count ($input) {
        if ($input = NULL OR []) {
            return 0;
        } elseif (gettype($input) == 'array') {
            return count($input);
        } else {
            return 1;
        }
    }

    // Like array_rand(), but returns a value instead of a key.
    public static function randSelect (array $array) {
        $key = array_rand($array);
        return $array[$key];
    }

    // Multiplies the values in $array_1 with values of the same keys in $array_2 to get the resulting array, or squares the values of $array_1 if there is no $array_2.
    public static function array_multiply (array $array_1, array $array_2 = null, array $filter = null) {
        $array_result = [];
        $array_2 = $array_2 ?? $array_1;
        foreach ($filter ?? array_keys($array_1) as $key) {
            $array_result[$key] = $array_1[$key] * $array_2[$key];
        }
        return $array_result;
    }
}

