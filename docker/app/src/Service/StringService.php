<?php
declare(strict_types=1);
namespace App\Service;

use App\Exception\Service\IncorrectBracketsException;
use App\Exception\Service\IncorrectClosedBracketException;
use App\Exception\Service\IncorrectOpenBracketException;
use App\Exception\Service\StringIsEmptyException;
use App\Interface\Service\IsFactoryInterface;

class StringService implements IsFactoryInterface
{
    public static function makeInstance(): static
    {
        return new StringService();
    }

    public function execute(string $string): string
    {
        if (mb_strlen($string) === 0) {
            throw new StringIsEmptyException("Строка пустая");
        }

        $bracket_open_list = [];
        $bracket_close_list = [];

        for ($i = 0; $i < mb_strlen($string); $i++) {
            if ("(" === $string[$i]) {
                $bracket_open_list[] = $i;
            }
            else if (")" === $string[$i]) {
                $bracket_close_list[] = $i;
            }
        }

        $for_open_errors = $this->checkForOpen($bracket_open_list, $bracket_close_list);
        $for_close_errors = $this->checkForClosed($bracket_open_list, $bracket_close_list);

        if (count($for_open_errors) > 0) {
            $for_open_errors_str = implode("\n", $for_open_errors);
            throw new IncorrectClosedBracketException($for_open_errors_str);
        }

        if (count($for_close_errors) > 0) {
            $for_close_errors_str = implode("\n", $for_close_errors);
            throw new IncorrectOpenBracketException($for_close_errors_str);
        }

        return "Строка корректна";


    }

    private function checkForOpen(array $bracket_open_list, array $bracket_close_list): array
    {   
        $errors = [];
        for ($i = 0; $i < count($bracket_open_list); $i++) {
            if (!array_key_exists($i, $bracket_close_list) || $bracket_close_list[$i] < $bracket_open_list[$i]) {
                $errors[] = "Не найдено закрывающей скобки для открывающей на позиции {$bracket_open_list[$i]}";
            }
        }

        return $errors;
    }

    private function checkForClosed(array $bracket_open_list, array $bracket_close_list): array
    {
        $errors = [];
        for ($i = 0; $i < count($bracket_close_list); $i++) {
            if (!array_key_exists($i, $bracket_open_list) || $bracket_close_list[$i] < $bracket_open_list[$i]) {
                $errors[] = "Не найдено открывающей скобки для закрывающей на позиции {$bracket_close_list[$i]}";
            }
        }

        return $errors;
    }


}