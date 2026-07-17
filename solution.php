<?php
declare(strict_types=1);

class ListNode
{
    public int $val = 0;
    public ?ListNode $next = null;

    public function __construct(int $val = 0, ?ListNode $listNode = null)
    {
        $this->val = $val;
        $this->next = $listNode;
    }
}

/**
 * @param int[] $args
 * @return ListNode
 */
function getListNode(array $args): ListNode {
    $listNode = new ListNode();
    $current = $listNode;
    $is_fillable = true;
    $i = 0;

    while ($is_fillable) {
        $current->val = $args[$i];
        $current->next = ($i + 1 < count($args)) ? new ListNode() : null;
        $current = $current->next;

        if (null === $current) {
            $is_fillable = false;
        }
        
        $i++;
    }

    return $listNode;
}

function mergeTwoList(ListNode $listOne, ListNode $listTwo): ListNode {

    [$less, $greater] = ($listOne->val < $listTwo->val) ? [$listOne->val, $listTwo->val] : [$listTwo->val, $listOne->val];
    
    $mergedListNode = new ListNode($less, new ListNode($greater));
    
    $current = $mergedListNode->next;
    $listOne = $listOne->next;
    $listTwo = $listTwo->next;

    $is_fillable = true;


    while ($is_fillable) {
        [$less, $greater] = ($listOne->val < $listTwo->val) ? [$listOne->val, $listTwo->val] : [$listTwo->val, $listOne->val];


        $current->next = new ListNode($less, new ListNode($greater));
        $current = $current->next->next;

        $listOne = $listOne->next;
        $listTwo = $listTwo->next;

        if (null === $listOne || null === $listTwo) {
            $is_fillable = false;
        }

    }

    return $mergedListNode;

}

function mergeTwoListWithDifferentLength(ListNode $listOne, ListNode $listTwo): ListNode {
    [$less, $greater] = ($listOne->val < $listTwo->val) ? [$listOne->val, $listTwo->val] : [$listTwo->val, $listOne->val];
    $rest = null;
    $mergedListNode = new ListNode($less, new ListNode($greater));
    
    $current = $mergedListNode->next;
    $listOne = $listOne->next;
    $listTwo = $listTwo->next;

    $is_fillable = true;

    while ($is_fillable) {
        if ((null === $listOne && null !== $listTwo) || (null !== $listOne && null === $listTwo)) {
            $is_fillable = false;
            $rest = (null !== $listOne) ? $listOne : $listTwo;
            

            while (null !== $rest) {
                $current->next = new ListNode($rest->val);
                $current = $current->next;
                $rest = $rest->next;
            }
        }

        if ($is_fillable) {
             [$less, $greater] = ($listOne->val < $listTwo->val) ? [$listOne->val, $listTwo->val] : [$listTwo->val, $listOne->val];


             $current->next = new ListNode($less, new ListNode($greater));
             $current = $current->next->next;

             $listOne = $listOne->next;
             $listTwo = $listTwo->next;

             if (null === $listOne && null === $listTwo) {
                 $is_fillable = false;
             }
        }
    }

    return $mergedListNode;

}


function mergeTwoListRecursive(ListNode $listOne, ListNode $listTwo): ?ListNode {
    [$less, $greater] = ($listOne->val < $listTwo->val) ? [$listOne->val, $listTwo->val] : [$listTwo->val, $listOne->val];

    if (null === $listOne->next || null === $listTwo->next) {
        return new ListNode($less, new ListNode($greater));
    }
    else {
        return new ListNode($less, new ListNode($greater, mergeTwoListRecursive($listOne->next, $listTwo->next)));
    }

}



$list_node_one_args = [1,2,4];
$list_node_two_args = [1,3,4];


$listNodeOne = getListNode($list_node_one_args);
$listNodeTwo = getListNode($list_node_two_args);



$mergedListNode = mergeTwoListRecursive($listNodeOne, $listNodeTwo);