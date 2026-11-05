<?php
declare(strict_types=1);



/**
 * @param int[] $nums
 * @param int $target
 * @return int[]
 */
function getIndicies(array $nums, int $target)
{
    $nums_by_values = [];
    $indicies = [];

    $is_finded = false;
    foreach ($nums as $index => $num) {
        if (isset($nums_by_values[$target - $num]) && $nums_by_values[$target - $num] !== $index) {
            $indicies = 
            [
                $nums_by_values[$target - $num],
                $index,

            ];
            $is_finded = true;
            
        }

        if ($is_finded) {
            break;
        }

        $nums_by_values[$num] = $index;


    }

    return $indicies;


}

function assertTest(array $expected, array $actual): void
{
    $intersection = array_intersect($actual, $expected);
    if (count($intersection) !== count($expected)) {
        $expected_msg = implode(', ', $expected);
        $actual_msg = implode(', ', $actual);
        throw new \Exception("expected: $expected_msg\nactual: $actual_msg");
    }

}




try {
    $nums = [2,7,11,15]; 
    $target = 9;
    assertTest([0, 1], getIndicies($nums, $target));


    $nums = [3,2,4];
    $target = 6;
    assertTest([1,2], getIndicies($nums, $target));
    


    $nums = [3,3];
    $target = 6;
    assertTest([0, 1], getIndicies($nums, $target));
    

    $nums = [2, 4, 3];
    $target = 6;
    assertTest([0, 1], getIndicies($nums, $target));

    echo "All tests successfull!!!\n";

}
catch(\Exception $e) {
    echo $e->getMessage();
}