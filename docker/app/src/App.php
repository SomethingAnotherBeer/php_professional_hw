<?php
declare(strict_types=1);
namespace App;
use Somethinganotherbeer\Methodborrow\Factory\ConfFactory;
use Somethinganotherbeer\Methodborrow\Context;


class App
{
    public static function makeInstance(): App
    {
        return new App();
    }

    public function run()
    {
        $configuration = 
        [
            'App\One' => 
            [
                'implementation_list' => 
                [
                    [
                        'implementation_name' => 'App\SomeInterface',
                        'implementation_value' => 'App\Two'
                    ],
                ],
                'replacement_list' =>
                [
                    [
                        'replacement_name' => 'someValue',
                        'replacement_value' => 5,
                    ],
                ], 
            ],
        ];


        $confFactory = ConfFactory::makeInstance();
        $classConfList = $confFactory->makeClassConfList($configuration);
        $context = Context::makeInstance($classConfList);

        $method = $context->borrowMethodFromClass(One::class, 'doSomething');
        $res = $method(5);
        echo "Вывод значения с передачей аргумента напрямую: $res<br/>";

        $method->bind(9);
        $res = $method();
        echo "Вывод значения с забинженным аргументом $res<br/>"; 


}

}