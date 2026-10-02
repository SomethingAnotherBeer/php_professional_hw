<?php
declare(strict_types=1);
namespace App\Trait\Event;
use App\Model\Event\EventCondition\EventConditionList;
use App\Model\Event\EventValue\EventValueList;
use App\Model\Event\EventCondition\EventConditionInterface;
use App\Model\Event\EventValue\EventValueInterface;


trait EventCompareTrait
{
    protected function compareEventConditionListsIsEquals(EventConditionList $firstEventConditionList, EventConditionList $secondEventConditionList): bool
    {
        $first_list = $firstEventConditionList->all();
        $second_list = $secondEventConditionList->all();
        $is_equals = true;

        if (count($first_list) !== count($second_list)) {
            $is_equals = false;
            return $is_equals;
        }

        usort($first_list, fn(EventConditionInterface $a, EventConditionInterface $b) => $a->getKey() <=> $b->getKey());
        usort($second_list, fn(EventConditionInterface $a, EventConditionInterface $b) => $a->getKey() <=> $b->getKey());

        for ($i = 0; $i < count($first_list); $i++) {
            if ($first_list[$i]->getKey() !== $second_list[$i]->getKey() 
                || $first_list[$i]->getValue() !== $second_list[$i]->getValue()) {
                $is_equals = false;
                break;
            }
        }

        return $is_equals;

    }


    protected function compareEventValueListsIsEquals(EventValueList $firstEventValueList, EventValueList $secondEventValueList): bool
    {
        $first_list = $firstEventValueList->all();
        $second_list = $secondEventValueList->all();
        $is_equals = true;

         if (count($first_list) !== count($second_list)) {
            $is_equals = false;
            return $is_equals;
        }

        usort($first_list, fn(EventValueInterface $a, EventValueInterface $b) => $a->getKey() <=> $b->getKey());
        usort($second_list, fn(EventValueInterface $a, EventValueInterface $b) => $a->getKey() <=> $b->getKey());

        for ($i = 0; $i < count($first_list); $i++) {
            if ($first_list[$i]->getKey() !== $second_list[$i]->getKey() 
                || $first_list[$i]->getValue() !== $second_list[$i]->getValue()) {
                $is_equals = false;
                break;
            }
        }

        return $is_equals;


    }




}