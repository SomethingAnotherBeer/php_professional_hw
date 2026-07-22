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
    $head = new ListNode();
    $currentListNode = $head;
    $current_val = 0;
    $val_list = [];
    $is_next = true;


    while ($is_next) {
        $list_one_val = (null !== $listOne) ? $listOne->val : null;
        $list_two_val = (null !== $listTwo) ? $listTwo->val : null;

        if (null === $list_one_val && null === $list_two_val) {
            $is_next = false;
            break;
        }

        if (null !== $list_one_val && null !== $list_two_val) {
            if ($list_one_val <= $list_two_val ) {
                $current_val = $list_one_val;
                $listOne = $listOne->next;
            }

            else if ($list_one_val >= $list_two_val) {
                $current_val = $list_two_val;
                $listTwo = $listTwo->next;
            }
        }

        else if (null !== $list_one_val) {
            $current_val = $list_one_val;
            $listOne = $listOne->next;
        }
        else if (null !== $list_two_val) {
            $current_val = $list_two_val;
            $listTwo = $listTwo->next;
        }

        $val_list[] = $current_val;

    }

    for ($i = 0; $i < count($val_list); $i++) {
        $currentListNode->val = $val_list[$i];

        $currentListNode->next = array_key_exists($i + 1, $val_list) ? new ListNode() : null;
        $currentListNode = $currentListNode->next;
    }


    return $head;
}


function mergeTwoListOne(ListNode $listOne, ListNode $listTwo): ListNode {
    
    $list_one_len = 0;
    $list_two_len = 0;
    $lesser_len = 0;
    $greater_len = 0;

    $pointer = null;
    $head = null;
    $restNode = null;

    $pointer = $listOne;
    while (null !== $pointer) {
        $pointer = $pointer->next;
        $list_one_len++;
    }

    $pointer = $listTwo;
    while (null !== $pointer) {
        $pointer = $pointer->next;
        $list_two_len++;
    }

    [$lesser_len, $greater_len] = ($list_one_len < $list_two_len) ? [$list_one_len, $list_two_len] : [$list_two_len, $list_one_len];
    
    $pointer = new ListNode($listOne->val, new ListNode($listTwo->val));
    $head = $pointer;
    $pointer = $pointer->next;
    $listOne = $listOne->next;
    $listTwo = $listTwo->next;

    for ($i = 1; $i < $lesser_len; $i++) {
        $pointer->next = new ListNode($listOne->val, new ListNode($listTwo->val));
        $pointer = $pointer->next->next;
        $listOne = $listOne->next;
        $listTwo = $listTwo->next;
    }

    if ($greater_len > $lesser_len) {
        $restNode = (null !== $listOne) ? $listOne : $listTwo;
        $i = 0;
        while ($i < ($greater_len - $lesser_len) && $restNode) {
            $pointer->next = $restNode;
            $pointer = $pointer->next;
            $restNode = $restNode->next;
            $i++;
        }
    }

    return mergeSortLinked($head);
}

function mergeSortLinked(ListNode $list): ListNode {
    $list_len = 0;

    $pointer = $list;
    while ($pointer) {
        $list_len++;
        $pointer = $pointer->next;
    }
    

    if (1 === $list_len) {
        return $list;
    }

    $left = new ListNode();
    $right = new ListNode();
    $leftPointer = $left;
    $rightPointer = $right;


    $mid = intdiv($list_len, 2);


    for ($i = 0; $i < $mid; $i++) {
        
        $leftPointer->val = $list->val;

        if ($i === $mid - 1) {
            $leftPointer->next = null;
        }
        else {
            $leftPointer->next = new ListNode();
            $leftPointer = $leftPointer->next;
        }
        $list = $list->next;
        
    }

    for ($i = $mid; $i < $list_len; $i++) {
        $rightPointer->val = $list->val;

        if ($i === $list_len - 1) {
            $rightPointer->next = null;
        }
        else {
            $rightPointer->next = new ListNode();
            $rightPointer = $rightPointer->next;
        }

        $list = $list->next;
    }

    return mergeLinked(mergeSortLinked($left), mergeSortLinked($right));


}

function mergeLinked(ListNode $listLeft, ListNode $listRight) {
    $resultLinked = new ListNode();
    $resultHead = $resultLinked;
    while ($listLeft && $listRight) {
        if ($listLeft->val < $listRight->val) {
            $resultLinked->val = $listLeft->val;
            $listLeft = $listLeft->next;
        }
        else {
            $resultLinked->val = $listRight->val;
            $listRight = $listRight->next;
        }
        $resultLinked->next = new ListNode();
        $resultLinked = $resultLinked->next;
    }

    if ($listLeft) {
        while ($listLeft) {
            $resultLinked->val = $listLeft->val;
            if ($listLeft->next) {
                $resultLinked->next = new ListNode();
                $resultLinked = $resultLinked->next;
            }
            else {
                $resultLinked->next = null;
            }
            $listLeft = $listLeft->next;
        }
    }
    else if ($listRight) {
        while ($listRight) {
            $resultLinked->val = $listRight->val;
            if ($listRight->next) {
                $resultLinked->next = new ListNode();
                $resultLinked = $resultLinked->next;
            }   
            else {
                $resultLinked->next = null;
            }
            $listRight = $listRight->next;
        }
    }

    return $resultHead;

}




$list_node_one_args = [1,2,3, 10, 11];
$list_node_two_args = [4,5,6, 3, 9, 6];


$listNodeOne = getListNode($list_node_one_args);
$listNodeTwo = getListNode($list_node_two_args);


$result = mergeTwoListOne($listNodeOne, $listNodeTwo);

print_r($result);