<?php
declare(strict_types=1);

/**
 * @param int[] $nums
 * @return int[]
 */
function frequencySort(array $nums)
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

    $count_nums = [];
    foreach ($nums_count as $num => $count) {
        $count_nums[$count] = $num;
    }

    uasort($nums_count, function (int $a, int $b) use ($count_nums) {
        if ($a > $b) {
            return 1;
        }
        else if ($a < $b){
            return -1;
        }
        else {
            if ($count_nums[$a] > $count_nums[$b]) {
                return -1;
            }
            else if ($count_nums[$a] < $count_nums[$b]) {
                return 1;
            }
            else {
                return 0;
            }
        }
       
    });


    $prepared_nums = [];
    foreach ($nums_count as $num => $count) {
        for ($i = 0; $i < $count; $i++) {
            $prepared_nums[] = $num;
        }
    }

    return $prepared_nums;

}


$nums = [1,1,2,2,2,3];
$prepared = frequencySort($nums);
print_r($prepared);



$nums = [2,3,1,3,2];
$prepared = frequencySort($nums);
print_r($prepared);


$nums = [-1,1,-6,4,5,-6,1,4,1];
$prepared = frequencySort($nums);
print_r($prepared);