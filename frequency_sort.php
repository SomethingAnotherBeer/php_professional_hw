<?php
declare(strict_types=1);

/**
 * @param int[] $nums
 * @return int[]
 */
function frequencySort(array $nums): array
{
    $nums_count = [];
    foreach ($nums as $num) {
        if (isset($nums_count[$num])) {
            $nums_count[$num]+=1;
        }
        else {
            $nums_count[$num] = 1;
        }
    }

    uasort($nums_count, function (int $a, int $b) {
        if ($a > $b) {
            return 1;
        }
        else if ($a < $b) {
            return -1;
        }
        else {
            return 0;
        }
    });
    uksort($nums_count, function (int $a, int $b) use($nums_count) {
        if ($nums_count[$a] === $nums_count[$b]) {
            if ($a > $b) {
                return -1;
            }
            else if ($a < $b) {
                return 1;
            }
            else {
                return 0;
            }
        }
        return 0;
    });

    $prepared_nums = [];
    foreach ($nums_count as $num => $count) {
        for ($i = 0; $i < $count; $i++) {
            $prepared_nums[] = $num;
        }
    }
    

    return $prepared_nums;




}

function assertTest(array $expected, array $actual): void
{
    if ($actual !== $expected) {
        $expected_msg = implode(', ', $expected);
        $actual_msg = implode(', ', $actual);
        throw new \Exception("expected: $expected_msg\nactual: $actual_msg");
    }

}



try {
    $nums = [1,1,2,2,2,3];
    assertTest([3,1,1,2,2,2], frequencySort($nums));

    $nums = [2,3,1,3,2];
    assertTest([1,3,3,2,2], frequencySort($nums));


    $nums = [1, 2];
    $prepared = frequencySort($nums);
    assertTest([2,1], frequencySort($nums));

    $nums = [-1,1,-6,4,5,-6,1,4,1];
    assertTest([5,-1,4,4,-6,-6,1,1,1], frequencySort($nums));

    echo "All tests successfull!!!\n";

}
catch(\Exception $e) {
    echo $e->getMessage() . "\n";
}