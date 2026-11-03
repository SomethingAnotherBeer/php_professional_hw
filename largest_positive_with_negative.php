<?php
declare(strict_types=1);

/**
 * @param int[] $nums
 * @return int
 */
function findMaxK(array $nums): int
{
    $negative_hash = [];
    foreach ($nums as $num) {
        if ($num < 0) {
            $negative_hash[$num] = true;
        }
    }

    $max = -1;

    for ($i = 1; $i < count($nums); $i++) {
        if ($nums[$i] > 0 && isset($negative_hash[-$nums[$i]])) {
            if ($nums[$i] > $max) {
                $max = $nums[$i];
            }
        }    
    }

    return $max;

}

$nums = [-1,2,-3,3];
$max = findMaxK($nums);

echo $max . "\n";

$nums = [-1,10,6,7,-7,1];
$max = findMaxK($nums);

echo $max . "\n";

$nums = [-10,8,6,7,-2,-3];
$max = findMaxK($nums);

echo $max . "\n";