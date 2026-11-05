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

    for ($i = 0; $i < count($nums); $i++) {
        if ($nums[$i] > 0 && isset($negative_hash[-$nums[$i]])) {
            if ($nums[$i] > $max) {
                $max = $nums[$i];
            }
        }    
    }

    return $max;

}


function assertTest(int $expected, int $actual): void
{
    if ($actual !== $expected) {
        throw new \Exception("expected: $expected\nactual: $actual");
    }

}



try {
    $nums = [-1,2,-3,3];
    assertTest(3, findMaxK($nums));

    $nums = [-1,10,6,7,-7,1];
    assertTest(7, findMaxK($nums));

    $nums = [-10,8,6,7,-2,-3];
    assertTest(-1, findMaxK($nums));

    $nums = [3, -3];
    assertTest(3, findMaxK($nums));
    

    echo "All tests successfull!!!\n";
}
catch(\Exception $e) {
    echo $e->getMessage() . "\n";
}