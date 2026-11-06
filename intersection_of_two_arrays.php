<?php
declare(strict_types=1);


/**
 * @param int[] $nums1
 * @param int[] $nums2
 * @return int[]
 */
function intersection(array $nums1, array $nums2): array {

    
    $nums_2_hash = [];
    foreach ($nums2 as $num) {
        $nums_2_hash[$num] = true;
    }

    $intersection_hash = [];

    foreach ($nums1 as $num) {
        if (isset($nums_2_hash[$num])) {
            $intersection_hash[$num] = true;
        }
    }

    return array_keys($intersection_hash);

}

$nums1 = [1,2,2,1];
$nums2 = [2,2];

$intersection = intersection($nums1, $nums2);
print_r($intersection);

$nums1 = [4,9,5];
$nums2 = [9,4,9,8,4];

$intersection = intersection($nums1, $nums2);
print_r($intersection);



