<?php
declare(strict_types=1);



/**
 * @param int[] $nums
 * @param int $target
 */
function getIndicies(array $nums, int $target): array
{
    $nums_by_values = [];
    foreach ($nums as $index => $num ) {
        $nums_by_values[$num]['num'][] = $num;
        $nums_by_values[$num]['index'][] = $index;
    }
    $indicies = [];
    
    foreach ($nums_by_values as $num => $params) {
        if (count($params['num']) > 1 && $params['num'][0] + $params['num'][1] === $target) {
            $indicies = $params['index'];
            break;
        } 

        if (isset($nums_by_values[$target - $params['num'][0]])) {
            $indicies =
            [
                $nums_by_values[$target - $params['num'][0]]['index'][0],
                $params['index'][0],
            ];
        }
    }


    return $indicies;

}


$nums = [2,7,11,15]; 
$target = 9;

$indicies = getIndicies($nums, $target);
print_r($indicies);


$nums = [3,2,4];
$target = 6;

$indicies = getIndicies($nums, $target);
print_r($indicies);


$nums = [3,3];
$target = 6;

$indicies = getIndicies($nums, $target);
print_r($indicies);