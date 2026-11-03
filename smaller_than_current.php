<?php
declare(strict_types=1);


/**
 * @param int[] $nums
 * @return int
 */
function smallerNumberThanCurrent(array $nums)
{   
    $inner_nums = $nums;
    usort($inner_nums, fn(int $a, int $b) => ($a > $b) ? -1 : 1);

    $prepared = [];
    
    $greater_than_count = 0;
    $iteration_count = 0;
    for ($i = count($inner_nums) - 1; $i >= 0; $i--) {
        if (isset($inner_nums[$i + 1]) && $inner_nums[$i] > $inner_nums[$i + 1]) {

            $greater_than_count+= $iteration_count;
            $iteration_count = 1;
        }
        else {
            $iteration_count+= 1;
        }

        $prepared[$inner_nums[$i]]['greater_than_count'] = $greater_than_count;
        if (isset($prepared[$inner_nums[$i]]['repeat_count'])) {
            $prepared[$inner_nums[$i]]['repeat_count']+=1;
        }
        else {
            $prepared[$inner_nums[$i]]['repeat_count'] = 1;
        }
        
    }
    
    $sorted_prepared = [];

    foreach ($nums as $num) {
        $sorted_prepared[] = $prepared[$num]['greater_than_count'];
    }


    return $sorted_prepared;

}


$nums = [8,1,2,2,3];

$prepared = smallerNumberThanCurrent($nums);
print_r($prepared);

$nums = [6,5,4,8];

$prepared = smallerNumberThanCurrent($nums);
print_r($prepared);

$nums = [7,7,7,7];
$prepared = smallerNumberThanCurrent($nums);
print_r($prepared);

