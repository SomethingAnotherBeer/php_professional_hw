# Описание тестовых кейсов:

Поскольку на бэк к нам прилетает JSON объект, должный содержать требуемые в рамках задачи поля, то при описании стоит выделить также такие кейсы валидации, 
когда требуемое поле отсутствует, при этом не стоит осуществлять отдельную проверку на пустоту входящего списка в целом, так как каждый кейс проверки поля на
отсутствие оного, целиком покрывает данную проверку. Так же в рамках обеспечения user experience, можно изменить проверку наличия пробела в поле на проверку,
где будем искать пробел в конкретном месте значения поля, ибо пользовать может случайно/по привычке нажать пробел до/после значения поля, и тем более на бэке
все равно будем делать проверки на пустоту по типу trim($field_name) !== ''. Далее, при указании длины поля в 0 символов, я буду исходить из предположения, что на бэке всегда будем делать операцию trim перед валидацией данных.

# Модульное тестирование:

    Тестируемый класс - PaymentController, метод -processPayment(array $payment_params); Моки - PaymentRepository, PaymentService. Моки метода - PaymentService::sendPayment(string json_payment_params) - возвращает "{"process":true, "transcation_id": "transcation_id", "code"; 200}";


    1) Валидационные кейсы
    1.a) Некорректный номер карты
    Мок PaymentRepository
    Мок PaymentService
    Мок PaymentProcessingStorage
    Мок PaymentWorker
    Мок PaymentService
    Мок PaymentWorker
    
    1.b) Проверка входящих параметров

    1) card_number:
        Требования:

        card_number должен быть строкой, обязательно содержащейся в списке параметров и не может быть пустым и должен содержать только цифры и длина card_number должна быть равна 16. Примеры валидных данных - '1111222233334444'. Примеры невалидных данных - '11122', '11112222333344445555', '111122223333444455qq'.

        Входящие данные:
        [
            'card_number': '111122223333qqqq',
            'card_holder': 'Ivan Ivanov',
            'card_expiration': '11/30',
            'cvv': '325',
            'order_number': '1g-x2f-q112-op32',
            'sum': '25,425'  
        ],
        Метод PaymentController::processPayment($payment_params) -> обнаружение невалидности card_number, card_number содержит отличные от цифр символы -> исключение InvalidPaymentParamsException
        Ответ "Номер карты содержит отличные от цифр символы" с кодом 400

        Входящие данные:
         [
            'card_number': '',
            'card_holder': 'Ivan Ivanov',
            'card_expiration': '11/30',
            'cvv': '325',
            'order_number': '1g-x2f-q112-op32',
            'sum': '25,425'
        ],
        Метод PaymentController::processPayment($payment_params) -> обнаружение, что card_number является пустой строкой -> исключение InvalidPaymentParamsException

        Ответ "Номер карты не может быть пустым" с кодом 400

        Входящие данные:
        [
            'card_number': 222,
            'card_holder': 'Ivan Ivanov',
            'card_expiration': '11/30',
            'cvv': '325',
            'order_number': '1g-x2f-q112-op32',
            'sum': '25,425'  
        ],
        Метод PaymentController::processPayment($payment_params) -> обнаружение, что тип card_number отличен от строки -> исключение InvalidPaymentParamsException

        Ответ "Номер карты должен быть строкой" с кодом 400

        Входящие данные
        [
            'card_holder': 'Ivan Ivanov',
            'card_expiration': '11/30',
            'cvv': '325',
            'order_number': '1g-x2f-q112-op32',
            'sum': '25,425'  
        ],
        Метод PaymentController::processPayment($payment_params) -> обнаружение, что параметр card_number не содержится в списке параметров -> исключение InvalidPaymentParamsException

        Ответ "Номер карты не найден в списке входящих параметров" с кодом 400

        Входящие данные
        [   
            'card_number': '111222'
            'card_holder': 'Ivan Ivanov',
            'card_expiration': '11/30',
            'cvv': '325',
            'order_number': '1g-x2f-q112-op32',
            'sum': '25,425'  
        ],
        Метод PaymentController::processPayment($payment_params) -> обнаружение, что длина параметр card_number не равна 16 -> исключение InvalidPaymentParamsException
        Ответ "Длина номера карты должна составлять 16 символов" с кодом 400



    2) card_holder
        Требования:
        card_holder должен содержатся в списке параметров и быть строкой, которая не является пустой и содержит символы латинского алфавита, которая допускает символы дефиса и пробела, каждый из которых может встретится только один раз в любом месте строки, за исключением начала строки и ее конца, и один из них обязательно должен один раз встретится в строке. Примеры валидного формата - "Anna Ivanova", "Anna Maria-Ivanova", "Anna-Maria Ivanova".

        Входящие данные:
        [
            'card_number': '2918231098324571',
            'card_holder': 'Ivan111',
            'card_expiration': '11/30',
            'cvv': '325',
            'order_number': '1g-x2f-q112-op32',
            'sum': '25,425'  
        ],
        Метод PaymentController::processPayment($payment_params) -> обнаружение, что параметр card_holder содержит недопустимые символы -> исключение InvalidPaymentParamsException
        Ответ "Владелец карты содержит некорректные символы" с кодом 400

        Входящие данные:
        [
            'card_number': '2918231098324571',
            'card_holder': '',
            'card_expiration': '11/30',
            'cvv': '325',
            'order_number': '1g-x2f-q112-op32',
            'sum': '25,425'  
        ],
         Метод PaymentController::processPayment($payment_params) -> обнаружение, что параметр card_holder является пустым -> исключение InvalidPaymentParamsException
         Ответ "Владелец карты не может быть пустым" с кодом 400

        Входящие данные:
        [
            'card_number': '2918231098324571',
            'card_expiration': '11/30',
            'cvv': '325',
            'order_number': '1g-x2f-q112-op32',
            'sum': '25,425'  
        ],
         Метод PaymentController::processPayment($payment_params) -> обнаружение, что параметр card_holder не содержится в списке параметров -> исключение InvalidPaymentParamsException
         Ответ "Владелец карты не обнаружен в списке входящих параметров" с кодом 400

        Входящие данные:
        [
            'card_number': '2918231098324571',
            'card_holder': 'Anna'
            'card_expiration': '11/30',
            'cvv': '325',
            'order_number': '1g-x2f-q112-op32',
            'sum': '25,425'  
        ],
         Метод PaymentController::processPayment($payment_params) -> обнаружение, что параметр card_holder не содержит фамилию -> исключение InvalidPaymentParamsException
         Ответ "Владелец карты не содержит фамилию" с кодом 400

        Входящие данные:
        [
            'card_number': '2918231098324571',
            'card_holder': 'Anna Maria Ii'
            'card_expiration': '11/30',
            'cvv': '325',
            'order_number': '1g-x2f-q112-op32',
            'sum': '25,425'  
        ],
         Метод PaymentController::processPayment($payment_params) -> обнаружение, что параметр card_holder содержит более чем одного пробела -> исключение InvalidPaymentParamsException
         Ответ "Владелец карты не является корректным" с кодом 400

        Входящие данные:
        [
            'card_number': '2918231098324571',
            'card_holder': 'Anna-Maria-Ii'
            'card_expiration': '11/30',
            'cvv': '325',
            'order_number': '1g-x2f-q112-op32',
            'sum': '25,425'  
        ],
         Метод PaymentController::processPayment($payment_params) -> обнаружение, что параметр card_holder содержит более чем одного дефиса -> исключение InvalidPaymentParamsException
         Ответ "Владелец карты не является корректным" с кодом 400


    
    3) card_expiration
        Требования:
        card_expiration должен присутствовать в списке параметров, быть строкой, которая не является пустой и соответствовать формату mm/yy, где mm - пара цифр от 01 до 12, а yy - пара цифыр от 00 до 99. Примеры корректного формата - 08/30, 02/27. Примеры некорректного формата - 00/13, 15/25.

        Входящие данные:
        [
            'card_number': '2918231098324571',
            'card_holder': 'Ivan Ivanov',
            'card_expiration': '15/30',
            'cvv': '325',
            'order_number': '1g-x2f-q112-op32',
            'sum': '25,425'  
        ],
        Метод PaymentController::processPayment($payment_params) -> обнаружение, что параметр card_expiration содержит некорректный месяц -> исключение InvalidPaymentParamsException
        Ответ "Срок действия содержит некорректный месяц" с кодом ответа 400


        Входящие данные:
        [
            'card_number': '2918231098324571',
            'card_holder': 'Ivan Ivanov',
            'card_expiration': '12/100',
            'cvv': '325',
            'order_number': '1g-x2f-q112-op32',
            'sum': '25,425'  
        ],
        Метод PaymentController::processPayment($payment_params) -> обнаружение, что параметр card_expiration содержит некорректный год -> исключение InvalidPaymentParamsException
        Ответ "Срок действия содержит некорректный год" с кодом ответа 400


        Входящие данные:
        [
            'card_number': '2918231098324571',
            'card_holder': 'Ivan Ivanov',
            'card_expiration': '12-25',
            'cvv': '325',
            'order_number': '1g-x2f-q112-op32',
            'sum': '25,425'  
        ],
        Метод PaymentController::processPayment($payment_params) -> обнаружение, что параметр card_expiration содержит некорректный разделитель -> исключение InvalidPaymentParamsException
        Ответ "Срок действия содержит некорректный разделитель" с кодом ответа 400



        Входящие данные:
        [
            'card_number': '2918231098324571',
            'card_holder': 'Ivan Ivanov',
            'card_expiration': '',
            'cvv': '325',
            'order_number': '1g-x2f-q112-op32',
            'sum': '25,425'  
        ],
        Метод PaymentController::processPayment($payment_params) -> обнаружение, что параметр card_expiration является пустым -> исключение InvalidPaymentParamsException
        Ответ "Срок действия не может быть пустым" с кодом ответа 400


        Входящие данные:
        [
            'card_number': '2918231098324571',
            'card_holder': 'Ivan Ivanov',
            'cvv': '325',
            'order_number': '1g-x2f-q112-op32',
            'sum': '25,425'  
        ],
        Метод PaymentController::processPayment($payment_params) -> обнаружение, что параметр card_expiration отсутствует в списке параметров -> исключение InvalidPaymentParamsException
        Ответ "Срок действия не обнаружен в списке входящих параметров" с кодом ответа 400


    
    4) cvv
        Требования:
        Поле cvv должно присутствовать в списке входящих параметров, быть строкой, не являющейся пустой и состоящей только из цифр, длина которой равна 3. Примеры корректных данных - '221', '555'. Примеры некорретных данных - '9q1', '55555'.

        Входящие данные:
        [
            'card_number': '2918231098324571',
            'card_holder': 'Ivan Ivanov',
            'card_expiration': '11/30',
            'cvv': '32q',
            'order_number': '1g-x2f-q112-op32',
            'sum': '25,425'  
        ],
        Метод PaymentController::processPayment($payment_params) -> обнаружение, что параметр cvv содержит отличные от цифр символы -> Исключение InvalidPaymentParamsException
        Ответ "cvv содержит отличные от цифр символы" с кодом ответа 400

        Входящие данные:
        [
            'card_number': '2918231098324571',
            'card_holder': 'Ivan Ivanov',
            'card_expiration': '11/30',
            'cvv': '12',
            'order_number': '1g-x2f-q112-op32',
            'sum': '25,425'  
        ],
        Метод PaymentController::processPayment($payment_params) -> обнаружение, что длина параметр cvv не равна 3  -> Исключение InvalidPaymentParamsException
        Ответ "Длина cvv не равна 3" с кодом ответа 400


        Входящие данные:
        [
            'card_number': '2918231098324571',
            'card_holder': 'Ivan Ivanov',
            'card_expiration': '11/30',
            'cvv': '1234',
            'order_number': '1g-x2f-q112-op32',
            'sum': '25,425'  
        ],
        Метод PaymentController::processPayment($payment_params) -> обнаружение, что длина параметр cvv не равна 3  -> Исключение InvalidPaymentParamsException
        Ответ "Длина cvv не равна 3" с кодом ответа 400


        Входящие данные:
        [
            'card_number': '2918231098324571',
            'card_holder': 'Ivan Ivanov',
            'card_expiration': '11/30',
            'cvv': '',
            'order_number': '1g-x2f-q112-op32',
            'sum': '25,425'  
        ],
        Метод PaymentController::processPayment($payment_params) -> обнаружение, что параметр cvv является пустым -> исключение InvalidPaymentParamsException
        Ответ "cvv не может быть пустым" с кодом 400


        Входящие данные:
        [
            'card_number': '2918231098324571',
            'card_holder': 'Ivan Ivanov',
            'card_expiration': '11/30',
            'cvv': 222,
            'order_number': '1g-x2f-q112-op32',
            'sum': '25,425'  
        ],
        Метод PaymentController::processPayment($payment_params) -> обнаружение, что параметр cvv содержит отличный от строки тип -> исключение InvalidPaymentParamsException
        Ответ "cvv должен быть строкой" с кодом 400

        Входящие данные:
         [
            'card_number': '2918231098324571',
            'card_holder': 'Ivan Ivanov',
            'card_expiration': '11/30',
            'order_number': '1g-x2f-q112-op32',
            'sum': '25,425'  
        ],
        Метод PaymentController::processPayment($payment_params) -> обнаружение, что параметр cvv отсутствует в списке параметров -> исключение InvalidPaymentParamsException
        Ответ "cvv не найден в списке параметров" с кодом 400



    5) order_number
        Требования:
        order_number должен представлять собой непустую строку произвольных символов, кл-во символов в которой не может превышать 16.
        Входящие данные:
         [
            'card_number': '2918231098324571',
            'card_holder': 'Ivan Ivanov',
            'card_expiration': '11/30',
            'cvv': '325',
            'order_number': '1g-x2f-q112-op322',
            'sum': '25,425'  
         ],
         Метод PaymentController::processPayment($payment_params) -> обнаружение, что длина параметра order_number превышает 16 символов -> исключение InvalidPaymentParamsException
         Ответ "Номер заказа не может превышать 16 символов" с кодом 400

         Входящие данные:
         [
            'card_number': '2918231098324571',
            'card_holder': 'Ivan Ivanov',
            'card_expiration': '11/30',
            'cvv': '325',
            'order_number': '',
            'sum': '25,425'  
         ],
         Метод PaymentController::processPayment($payment_params) -> обнаружение, что параметр order_number является пустым-> исключение InvalidPaymentParamsException
         Ответ "Номер заказа не может быть пустым" с кодом 400

        Входящие данные:
         [
            'card_number': '2918231098324571',
            'card_holder': 'Ivan Ivanov',
            'card_expiration': '11/30',
            'cvv': '325',
            'order_number': 222,
            'sum': '25,425'  
         ],
         Метод PaymentController::processPayment($payment_params) -> обнаружение, что параметр order_number содержит отличный от строки тип-> исключение InvalidPaymentParamsException
         Ответ "Номер заказа должен быть строкой" с кодом 400

        
        Входящие данные:
        [
            'card_number': '2918231098324571',
            'card_holder': 'Ivan Ivanov',
            'card_expiration': '11/30',
            'cvv': '325',
            'sum': '25,425'  
         ],
          Метод PaymentController::processPayment($payment_params) -> обнаружение, что параметр order_number отсутствует в списке параметров-> исключение InvalidPaymentParamsException
          Ответ "Номер заказа не найден в списке параметров" с кодом 400


    6) sum
        Требования:
        Параметр sum должен быть строкой, которая не должна быть пустой и должна состоять только из цифр, допускается символ запятой, за исключением начала или конца строки. Пример валидных данных -'25,425', '425'. Примеры невалидных данных - '25.425', 'aa12'.

        Входящие данные:
        [
            'card_number': '2918231098324571',
            'card_holder': 'Ivan Ivanov',
            'card_expiration': '11/30',
            'cvv': '325',
            'order_number': '1g-x2f-q112-op32',
            'sum': '25.425'  
        ],
        Метод PaymentController::processPayment($payment_params) -> обнаружение, что параметр sum содержит отличные от цифр и запятой символы -> исключение InvalidPaymentParamsException
        Ответ "Сумма заказа не является корректной" с кодом 400



        Входящие данные:
        [
            'card_number': '2918231098324571',
            'card_holder': 'Ivan Ivanov',
            'card_expiration': '11/30',
            'cvv': '325',
            'order_number': '1g-x2f-q112-op32',
            'sum': 25.425  
        ],
        Метод PaymentController::processPayment($payment_params) -> обнаружение, что параметр sum не является строковым -> исключение InvalidPaymentParamsException
        Ответ "Сумма заказа должна быть строкой" с кодом 400



        Входящие данные:
        [
            'card_number': '2918231098324571',
            'card_holder': 'Ivan Ivanov',
            'card_expiration': '11/30',
            'cvv': '325',
            'order_number': '1g-x2f-q112-op32',
            'sum': ''  
        ],
        Метод PaymentController::processPayment($payment_params) -> обнаружение, что параметр sum является пустой строкой -> исключение InvalidPaymentParamsException
        Ответ "Сумма заказа не может быть пустой" с кодом 400 


        Входящие данные:
        [
            'card_number': '2918231098324571',
            'card_holder': 'Ivan Ivanov',
            'card_expiration': '11/30',
            'cvv': '325',
            'order_number': '1g-x2f-q112-op32',

        ],
        Метод PaymentController::processPayment($payment_params) -> обнаружение, что параметр параметр sum отсутствует в списке параметров -> исключение InvalidPaymentParamsException
        Ответ "Сумма заказа не найдена в списке параметров" с кодом 400 

        Входящие данные:
        [
            'card_number': '2918231098324571',
            'card_holder': 'Ivan Ivanov',
            'card_expiration': '11/30',
            'cvv': '325',
            'order_number': '1g-x2f-q112-op32',
            'sum': '25,,25'

        ],
        Метод PaymentController::processPayment($payment_params) -> обнаружение, что параметр параметр sum не является корректным -> исключение InvalidPaymentParamsException
        Ответ "Параметр суммы заказа не является корректным" с кодом 400


        Входящие данные:
        [
            'card_number': '2918231098324571',
            'card_holder': 'Ivan Ivanov',
            'card_expiration': '11/30',
            'cvv': '325',
            'order_number': '1g-x2f-q112-op32',
            'sum': ',2525'

        ],
        Метод PaymentController::processPayment($payment_params) -> обнаружение, что параметр параметр sum не является корректным -> исключение InvalidPaymentParamsException
        Ответ "Параметр суммы заказа не является корректным" с кодом 400 



        Входящие данные:
        [
            'card_number': '2918231098324571',
            'card_holder': 'Ivan Ivanov',
            'card_expiration': '11/30',
            'cvv': '325',
            'order_number': '1g-x2f-q112-op32',
            'sum': '2525,'

        ],
        Метод PaymentController::processPayment($payment_params) -> обнаружение, что параметр параметр sum не является корректным -> исключение InvalidPaymentParamsException
        Ответ "Параметр суммы заказа не является корректным" с кодом 400 

    



    1) Обработка данных не прошла успешно:

    2.1) Входящие данные - 
        [
            'card_number': '2918231098324571',
            'card_holder': 'Ivan Ivanov',
            'card_expiration': '11/30',
            'cvv': 325,
            'order_number': '1g-x2f-q112-op32',
            'sum': '25,425'  
        ],
    мок Logger::sendAlarm(string $alarm_message): void 
    мок PaymentWorker::sendToRefund(string $payment_transcation_id): bool -> true
    мок PaymentProcessingStorage::setOrderByNumberIfNotExists(string $order_number): bool -> true
    мок PaymentProcessingStorage::blockApplication(): bool -> true
    мок PaymentProcessingStorage::removeOrderByNumber(string $order_number): bool -> true
    мок PaymentRepository::checkPaidOrderIsExists(string $order_number): bool -> false
    мок PaymentRepository::checkSumIsValidForOrderNumber(string $order_number, float $sum): bool -> true
    мок PaymentRepository::setOrderIsPaid(string $order_number): bool -> true
    мок PaymentService::sendPayment(string $encoded_payment_params): string -> "{"processing":true, "transcation_id": "transcation_id", "code": 200}"


    PaymentController::processPayment($payment_params) -> ошибка валидации -> return ['message': 'cvv должен быть строкой', 'status': 400]

    Вызов Logger::sendAlarm осуществлен 0 раз
    Вызов PaymentWorker::sendToRefund осуществлен 0 раз
    Вызов PaymentProcessingStorage::setOrderByNumberIfNotExists осуществлен 0 раз
    Вызов PaymentProcessingStorage::blockApplication осуществлен 0 раз
    Вызов PaymentProcessingStorage::removeOrderByNumber осуществлен 0 раз
    Вызов PaymentRepository::checkPaidOrderIsExists осуществлен 0 раз
    Вызов PaymentRepository::checkSumIsValidForOrderNumber осуществлен 0 раз
    Вызов PaymentRepository::setOrderIsPaid осуществлен 0 раз
    Вызов PaymentService::sendPayment осуществлен 0 раз



    2) Обработка всех данных прошла успешно; Поведение зависит от результатов моков изолированного окружения.
        2.1) Входящие данные - 
            [
                'card_number': '2918231098324571',
                'card_holder': 'Ivan Ivanov',
                'card_expiration': '11/30',
                'cvv': '325',
                'order_number': '1g-x2f-q112-op32',
                'sum': '25,425'  
            ],


        2.1.a) Успешная оплата
        
        мок Logger::sendAlarm(string $alarm_message): void 
        мок PaymentWorker::sendToRefund(string $payment_transcation_id): bool -> true
        мок PaymentProcessingStorage::setOrderByNumberIfNotExists(string $order_number): bool -> true
        мок PaymentProcessingStorage::blockApplication(): bool -> true
        мок PaymentProcessingStorage::removeOrderByNumber(string $order_number): bool -> true
        мок PaymentRepository::checkPaidOrderIsExists(string $order_number): bool -> false
        мок PaymentRepository::checkSumIsValidForOrderNumber(string $order_number, float $sum): bool -> true
        мок PaymentRepository::setOrderIsPaid(string $order_number): bool -> true
        мок PaymentService::sendPayment(string $encoded_payment_params): string -> "{"processing":true, "transcation_id": "transcation_id", "code": 200}"

        2.1.b) 
        
        Вызов PaymentController::processPayment($payment_params) ->: in

        PaymentProcessingStorage::setOrderByNumberIfNotExists($order_number) -> PaymentRepository::checkPaidOrderIsExists($order_number) -> PaymentRepository::checkSumIsValidForOrderNumber($oder_number, $sum) -> PaymentService::sendPayment($encoded_payment_params) -> PaymentRepository::setOrderIsPaid($order_number, $sum) ->:out

        PaymentController::processPayment -> PaymentProcessingStorage::removeOrderByNumber($order_number) -> return ['message': 'Оплата прошла успешно', 'status': 200]

        2.1.c)
        Вызов PaymentProcessingStorage::setOrderByNumberIfNotExists осуществлен 1 раз
        Вызов PaymentRepository::checkPaidOrderIsExists осуществлен 1 раз
        Вызов PaymentRepository::checkSumIsValidForOrderNumber осуществлен 1 раз
        Вызов PaymentService::sendPayment осуществлен 1 раз
        Вызов PaymentRepository::setOrderIsPaid осуществлен 1 раз
        Вызов PaymentProcessingStorage::removeOrderByNumber осуществлен 1 раз

        Вызов PaymentWorker::sendToRefund осуществлен 0 раз
        Вызов Logger::sendAlarm осуществлен 0 раз
        Вызов PaymentProcessingStorage::blockApplication осуществлен 0 раз

        2.1.d) Возврат ответа с сообщением "Оплата прошла успешно"  и кодом 200 

        2.2.a) Невалидные реквизиты
        
        мок Logger::sendAlarm(string $alarm_message): void 
        мок PaymentWorker::sendToRefund(string $payment_transaction_id): bool -> true
        мок PaymentProcessingStorage::setOrderByNumberIfNotExists(string $order_number): bool -> true
        мок PaymentProcessingStorage::blockApplication(): bool -> true
        мок PaymentProcessingStorage::removeOrderByNumber(string $order_number): bool -> true
        мок PaymentRepository::checkPaidOrderIsExists(string $order_number): bool -> false
        мок PaymentRepository::checkSumIsValidForOrderNumber(string $order_number, float $sum): bool -> true
        мок PaymentRepository::setOrderIsPaid(string $order_number): bool -> true
        мок PaymentService::sendPayment(string $encoded_payment_params): string -> "{"processing":false, "transcation_id": "transcation_id", "code": 403}"


        2.2.b) 
        
        PaymentController::processPayment($payment_params) ->:in

        PaymentProcessingStorage::setOrderByNumberIfNotExists($order_number) -> PaymentRepository::checkPaidOrderIsExists($order_number) -> PaymentRepository::checkSumIsValidForOrderNumber($order_number, $sum) -> PaymentService::sendPayment($encoded_payment_params) -> выброс исключения InvalidPaymentCredentialsException -> :out

        PaymentController::processPayment -> PaymentProcessingStorage::removeOrderByNumber($order_number) -> return ['message': 'Не удалось списать данные с карты, проверьте реквизиты', 'status': 403]
        
        2.2.c)
        Вызов PaymentProcessingStorage::setOrderByNumberIfNotExists  осуществлен 1 раз
        Вызов PaymentRepository::checkPaidOrderIsExists  осуществлен 1 раз
        Вызов PaymentRepository::checkSumIsValidForOrderNumber осуществлен 1 раз 
        Вызов PaymentService::sendPayment осуществлен 1 раз
        Вызов PaymentProcessingStorage::removeOrderByNumber осуществлен 1 раз

        Вызов PaymentRepository::setOrderIsPaid осуществлен 0 раз
        Вызов PaymentWorker::sendToRefund осуществлен 0 раз
        Вызов Logger::sendAlarm осуществлен 0 раз
        Вызов PaymentProcessingStorage::blockApplication осуществлен 0 раз

        2.2.d) Возврат ответа с сообщением "Не удалось списать данные с карты, проверьте реквизиты" и кодом 403

        2.3.a) Оплаченный заказ уже существует в системе

        мок Logger::sendAlarm(string $alarm_message): void 
        мок PaymentWorker::sendToRefund(string $payment_transaction_id): bool -> true
        мок PaymentProcessingStorage::setOrderByNumberIfNotExists(string $order_number): bool -> true
        мок PaymentProcessingStorage::blockApplication(): bool -> true
        мок PaymentProcessingStorage::removeOrderByNumber(string $order_number): bool -> true
        мок PaymentRepository::checkPaidOrderIsExists(string $order_number): bool -> true
        мок PaymentRepository::checkSumIsValidForOrderNumber(string $order_number, float $sum): bool -> true
        мок PaymentRepository::setOrderIsPaid(string $order_number): bool -> true
        мок PaymentService::sendPayment(string $encoded_payment_params): string -> "{"processing":true, "transcation_id": "transcation_id", "code": 200}"
        
        2.3.b) 
        
        PaymentController::processPayment($payment_params)->:in

        Вызов PaymentProcessingStorage::setOrderByNumberIfNotExists($order_number) -> PaymentRepository::checkPaidOrderIsExists($order_number) -> выброс исключения PaidOrderAlreadyExistsException ->:out

        PaymentController::processPayment -> PaymentProcessingStorage::removeOrderByNumber($order_number) -> return ['message': 'Оплаченный заказ уже существует в системе', 'status': 409]
        
        2.3.c)
        Вызов PaymentProcessingStorage::setOrderByNumberIfNotExists осуществлен 1 раз
        Вызов PaymentRepository::checkPaidOrderIsExists осуществлен 1 раз
        Вызов PaymentProcessingStorage::removeOrderByNumber осуществлен 1 раз

        Вызов PaymentRepository::checkSumIsValidForOrderNumber осуществлен 0 раз
        Вызов PaymentRepository::setOrderIsPaid осуществлен 0 раз
        Вызов PaymentWorker::sendToRefund осуществлен 0 раз
        Вызов PaymentService::sendPayment осуществлен 0 раз
        Вызов Logger::sendAlarm осуществлен 0 раз
        Вызов PaymentProcessingStorage::blockApplication осуществлен 0 раз

        2.3.d) Возврат ответа с сообщением "Оплаченный заказ уже существует в системе" и кодом 409

        2.4.a) Сумма платежа несоответствует номеру заказа

        мок Logger::sendAlarm(string $alarm_message): void 
        мок PaymentWorker::sendToRefund(string $payment_transaction_id): bool -> true
        мок PaymentProcessingStorage::setOrderByNumberIfNotExists(string $order_number): bool -> true
        мок PaymentProcessingStorage::blockApplication(): bool -> true
        мок PaymentProcessingStorage::removeOrderByNumber(string $order_number): bool -> true
        мок PaymentRepository::checkPaidOrderIsExists(string $order_number): bool -> false
        мок PaymentRepository::checkSumIsValidForOrderNumber(string $order_number, float $sum): bool -> false
        мок PaymentRepository::setOrderIsPaid(string $order_number): bool -> true
        мок PaymentService::sendPayment(string $encoded_payment_params): string -> "{"processing":true, "transcation_id": "transcation_id", "code": 200}"
        
        2.4.b) 
        
        PaymentController::processPayment($payment_params) ->:in

        Вызов PaymentProcessingStorage::setOrderByNumberIfNotExists($order_number) -> PaymentRepository::checkPaidOrderIsExists($order_number) -> PaymentRepository::checkSumIsValidForOrderNumber($order_number) -> выброс исключения PaymentSumInconsistenceForOrder ->:out

        PaymentController::processPayment -> PaymentProcessingStorage::removeOrderByNumber($order_number)-> return ['message': 'Сумма платежа несоответствует номеру заказа', 'status': 409]

        2.4.c)
        Вызов PaymentProcessingStorage::setOrderByNumberIfNotExists осуществлен 1 раз
        Вызов PaymentRepository::checkPaidOrderIsExists осуществлен 1 раз
        Вызов PaymentRepository::checkSumIsValidForOrderNumber осуществлен 1 раз
        Вызов PaymentProcessingStorage::removeOrderByNumber осуществлен 1 раз

        Вызов PaymentRepository::setOrderIsPaid осуществлен 0 раз
        Вызов PaymentService::sendPayment осуществлен 0 раз
        Вызов PaymentWorker::sendToRefund осуществлен 0 раз
        Вызов Logger::sendAlarm осуществлен 0 раз
        Вызов PaymentProcessingStorage::blockApplication осуществлен 0 раз

        2.4.d) Возврат сообщения об ошибке "Сумма платежа несоответствует номеру заказа" с кодом 409

        2.5.a) Заказ с таким номером уже проходит обработку в системе
        
        мок Logger::sendAlarm(string $alarm_message): void 
        мок PaymentWorker::sendToRefund(string $payment_transaction_id): bool -> true
        мок PaymentProcessingStorage::setOrderByNumberIfNotExists(string $order_number): bool -> false
        мок PaymentProcessingStorage::blockApplication(): bool -> true
        мок PaymentProcessingStorage::removeOrderByNumber(string $order_number): bool -> true
        мок PaymentRepository::checkPaidOrderIsExists(string $order_number): bool -> false
        мок PaymentRepository::checkSumIsValidForOrderNumber(string $order_number, float $sum): bool -> true
        мок PaymentRepository::setOrderIsPaid(string $order_number): bool -> true
        мок PaymentService::sendPayment(string $encoded_payment_params): string -> "{"processing":true, "transcation_id": "transcation_id", "code": 200}"
    
        2.5.b) 
        
        PaymentController::processPayment($payment_params) ->:in
        
        Вызов PaymentProcessingStorage::setOrderByNumberIfNotExists($order_number) -> Выброс исключения OrderAlreadyProcessingException ->:out

        PaymentController::processPayment -> return ['message': 'Данный заказ уже проходит обработку в системе', 'status': 409]

        2.5.c)
        Вызов PaymentProcessingStorage::setOrderByNumberIfNotExists осуществлен 1 раз

        Вызов PaymentRepository::checkPaidOrderIsExists осуществлен 0 раз
        Вызов PaymentRepository::checkSumIsValidForOrderNumber осуществлен 0 раз
        Вызов PaymentRepository::setOrderIsPaid осуществлен 0 раз
        Вызов PaymentService::sendPayment осуществлен 0 раз
        Вызов PaymentWorker::sendToRefund осуществлен 0 раз
        Вызов PaymentProcessingStorage::removeOrderByNumber осуществлен 0 раз
        Вызов PaymentProcessingStorage::blockApplication осуществлен 0 раз
        Вызов Logger::sendAlarm осуществлен 0 раз

        2.5.d) Возврат сообщения "Данный заказ уже проходит обработку в системе" с кодом 409


        2.6.a) Сервис оплаты A вернул 500

        мок Logger::sendAlarm(string $alarm_message): void 
        мок PaymentWorker::sendToRefund(string $payment_transaction_id): bool -> true
        мок PaymentProcessingStorage::setOrderByNumberIfNotExists(string $order_number): bool -> true
        мок PaymentProcessingStorage::blockApplication(): bool -> true
        мок PaymentProcessingStorage::removeOrderByNumber(string $order_number): bool -> true
        мок PaymentRepository::checkPaidOrderIsExists(string $order_number): bool -> false
        мок PaymentRepository::checkSumIsValidForOrderNumber(string $order_number, float $sum): bool -> true
        мок PaymentRepository::setOrderIsPaid(string $order_number): bool -> true
        мок PaymentService::sendPayment(string $encoded_payment_params): string -> "{"code": 500}"

        2.6.b)

        PaymentController::processPayment($payment_params) ->:in

        Вызов PaymentProcessingStorage::setOrderByNumberIfNotExists($order_number) -> PaymentRepository::checkPaidOrderIsExists($order_number) -> PaymentRepository::checkSumIsValidForOrderNumber($order_number, $sum) -> PaymentService::sendPayment($encoded_payment_params) -> Выброс исключения PaymentServiceNotAllowedException ->:out 

        PaymentController::processPayment -> PaymentProcessingStorage::removeOrderByNumber($order_number) -> return ['message': 'Сервер оплаты недоступен, повторите позднее', 'status': 500]

        2.6.c)
        Вызов PaymentProcessingStorage::setOrderByNumberIfNotExists осуществлен 1 раз
        Вызов PaymentProcessingStorage::removeOrderByNumber осуществлен 1 раз
        Вызов PaymentRepository::checkPaidOrderIsExists осуществлен 1 раз
        Вызов PaymentRepository::checkSumIsValidForOrderNumber осуществлен 1 раз
        Вызов PaymentService::sendPayment осуществлен 1 раз

        Вызов PaymentRepository::setOrderIsPaid осуществлен 0 раз
        Вызов PaymentWorker::sendToRefund осуществлен 0 раз
        Вызов Logger::sendAlarm осуществлен 0 раз
        Вызов PaymentProcessingStorage::blockApplication осуществлен 0 раз

        2.6.d) Возврат сообщения "Сервер оплаты недоступен, повторите позднее" с кодом 500



        2.7.a) В методе PaymentRepository::checkSumIsValidForOrderNumber произошло исключение SumMustGreaterThanZero
        
        2.7.b)
        мок Logger::sendAlarm(string $alarm_message): void 
        мок PaymentWorker::sendToRefund(string $payment_transaction_id): bool -> true
        мок PaymentProcessingStorage::blockApplication(): bool -> true
        мок PaymentProcessingStorage::setOrderByNumberIfNotExists(string $order_number): bool -> true
        мок PaymentProcessingStorage::removeOrderByNumber(string $order_number): bool -> true
        мок PaymentRepository::checkPaidOrderIsExists(string $order_number): bool -> false
        мок PaymentRepository::checkSumIsValidForOrderNumber(string $order_number, float $sum): bool -> throws exception SumMustBeGreaterThanZero
        мок PaymentRepository::setOrderIsPaid(string $order_number): bool -> true
        мок PaymentService::sendPayment(string $encoded_payment_params): string -> "{"processing":true, "transcation_id": "transcation_id", "code": 200}"

        PaymentController::processPayment($payment_params) ->:in

        Вызов PaymentProcessingStorage::setOrderByNumberIfNotExists($order_number) -> PaymentRepository::checkPaidOrderIsExists($order_number) -> PaymentRepository::checkSumIsValidForOrderNumber($order_number, $sum) -> исключение SumMustBeGreaterThanZero->withMessage('Сумма оплаты заказа не может быть равной или меньше нуля') ->:out

        PaymentController::processPayment -> PaymentProcessingStorage::removeOrderByNumber($order_number) -> Logger::sendAlarm($exception_message) -> return ['error': 'Ошибка сервиса, повторите операцию позднее', 'status': 520]

        2.7.c)
        Вызов Logger::sendAlarm осуществлен 1 раз
        Вызов PaymentProcessingStorage::setOrderByNumberIfNotExists осуществлен 1 раз
        Вызов PaymentProcessingStorage::removeOrderByNumber осуществлен 1 раз
        Вызов PaymentRepository::checkPaidOrderIsExists осуществлен 1 раз
        Вызов PaymentRepository::checkSumIsValidForOrderNumber осуществлен 1 раз

        Вызов PaymentRepository::setOrderIsPaid осуществлен 0 раз
        Вызов PaymentService::sendPayment осуществлен 0 раз
        Вызов PaymentWorker::sendToRefund осуществлен 0 раз
        Вызов PaymentProcessingStorage::blockApplication осуществлен 0 раз

        2.7.d) Возврат сообщения "Ошибка сервиса, повторите операцию позднее" с кодом 520

        2.8.a) В методе PaymentRepository::checkPaidOrderIsExists произошло исключение

        2.8.b)
        мок Logger::sendAlarm(string $alarm_message): void 
        мок PaymentWorker::sendToRefund(string $payment_transaction_id): bool -> true
        мок PaymentProcessingStorage::blockApplication(): bool -> true
        мок PaymentProcessingStorage::setOrderByNumberIfNotExists(string $order_number): bool -> true
        мок PaymentProcessingStorage::removeOrderByNumber(string $order_number): bool -> true
        мок PaymentRepository::checkPaidOrderIsExists(string $order_number): bool -> throws throwable
        мок PaymentRepository::checkSumIsValidForOrderNumber(string $order_number, float $sum): bool
        мок PaymentRepository::setOrderIsPaid(string $order_number): bool -> true
        мок PaymentService::sendPayment(string $encoded_payment_params): string -> "{"processing":true, "transcation_id": "transcation_id", "code": 200}"


        PaymentController::processPayment($payment_params) -> :in

        Вызов PaymentProcessingStorage::setOrderByNumberIfNotExists($order_number) -> PaymentRepository::checkPaidOrderIsExists($order_number) -> исключение throwable ->:out

        PaymentController::processPayment -> PaymentProcessingStorage::removeOrderByNumber($order_number) -> Logger::sendAlarm($alarm_message) -> PaymentProcessingStorage::blockApplication() -> return ['error': 'Ошибка сервиса, повторите операцию позднее', 'status': 520]

        2.8.c)
        Вызов PaymentProcessingStorage::setOrderByNumberIfNotExists осуществлен 1 раз
        Вызов PaymentProcessingStorage::removeOrderByNumber осуществлен 1 раз
        Вызов PaymentRepository::checkPaidOrderIsExists осуществлен 1 раз
        Вызов PaymentProcessingStorage::blockApplication осуществлен 1 раз
        Вызов Logger::sendAlarm осуществлен 1 раз

        Вызов PaymentRepository::checkSumIsValidForOrderNumber осуществлен 0 раз
        Вызов PaymentRepository::setOrderIsPaid осуществлен 0 раз
        Вызов PaymentService::sendPayment осуществлен 0 раз
        Вызов PaymentWorker::sendToRefund осуществлен 0 раз

        2.8.d) Возврат сообщения "Ошибка сервиса, повторите операцию позднее" с кодом 520


        2.9.a) В методе PaymentRepository::checkSumIsValidForOrderNumber произошло исключение

        2.9.b)
        мок Logger::sendAlarm(string $alarm_message): void 
        мок PaymentWorker::sendToRefund(string $payment_transaction_id): bool -> true
        мок PaymentProcessingStorage::blockApplication(): bool -> true
        мок PaymentProcessingStorage::setOrderByNumberIfNotExists(string $order_number): bool -> true
        мок PaymentProcessingStorage::removeOrderByNumber(string $order_number): bool -> true
        мок PaymentRepository::checkPaidOrderIsExists(string $order_number): bool -> false
        мок PaymentRepository::checkSumIsValidForOrderNumber(string $order_number, float $sum): bool -> throws throwable
        мок PaymentRepository::setOrderIsPaid(string $order_number): bool -> true
        мок PaymentService::sendPayment(string $encoded_payment_params): string -> "{"processing":true, "transcation_id": "transcation_id", "code": 200}"

        PaymentController::processPayment($payment_params) -> :in

        PaymentProcessingStorage::setOrderByNumberIfNotExists($order_number) -> PaymentRepository::checkPaidOrderIsExists($order_number) -> PaymentRepository::checkSumIsValidForOrderNumber($order_number, $sum) -> исключение throwable ->:out

        PaymentController::processPayment -> PaymentProcessingStorage::removerOrderByNumber($order_number) -> Logger::sendAlarm($alarm_message) -> PaymentProcessingStorage::blockApplication() -> return ['error': 'Ошибка сервиса, повторите операцию позднее', 'status': 520]

        2.9.c)

        Вызов PaymentProcessingStorage::setOrderByNumberIfNotExists осуществлен 1 раз
        Вызов PaymentProcessingStorage::removeOrderByNumber осуществлен 1 раз
        Вызов PaymentRepository::checkPaidOrderIsExists осуществлен 1 раз
        Вызов PaymentRepository::checkSumIsValidForOrderNumber осуществлен 1 раз
        Вызов PaymentProcessingStorage::blockApplication осуществлен 1 раз
        Вызов Logger::sendAlarm осуществлен 1 раз

        
        Вызов PaymentRepository::setOrderIsPaid осуществлен 0 раз
        Вызов PaymentService::sendPayment осуществлен 0 раз
        Вызов PaymentWorker::sendToRefund осуществлен 0 раз

        2.9.d) Возврат сообщения "Ошибка сервиса, повторите операцию позднее" с кодом 520

        2.10.a) В методе PaymentRepository::setOrderIsPaid произошло исключение throwable

        2.10.b)
        мок Logger::sendAlarm(string $alarm_message): void 
        мок PaymentWorker::sendToRefund(string $payment_transaction_id): bool -> true
        мок PaymentProcessingStorage::blockApplication(): bool -> true
        мок PaymentProcessingStorage::setOrderByNumberIfNotExists(string $order_number): bool -> true
        мок PaymentProcessingStorage::removeOrderByNumber(string $order_number): bool -> true
        мок PaymentRepository::checkPaidOrderIsExists(string $order_number): bool -> false
        мок PaymentRepository::checkSumIsValidForOrderNumber(string $order_number, float $sum): bool  -> true
        мок PaymentRepository::setOrderIsPaid(string $order_number): bool -> throws throwable
        мок PaymentService::sendPayment(string $encoded_payment_params): string -> "{"processing":true, "transcation_id": "transcation_id", "code": 200}"


        PaymentController::processPayment($payment_params) ->:in

        PaymentProcessingStorage::setOrderByNumberIfNotExists($order_number) -> PaymentRepository::checkPaidOrderIsExists -> PaymentRepository::checkSumIsValidForOrderNumber -> PaymentService::sendPayment($encoded_payment_params) -> PaymentRepository::setOrderIsPaid -> исключение throwable ->:out

        PaymentController::processPayment -> PaymentProcessingStorage::removerOrderByNumber($order_number) -> Logger::sendAlarm($alarm_message) -> PaymentWorker::sendToRefund($payment_transaction_id) -> PaymentProcessingStorage::blockApplication() -> return ['error': 'Ошибка сервиса, сумма скоро вернется к вам на карту', 'status': 520]

        2.10.c)

        Вызов PaymentProcessingStorage::setOrderByNumberIfNotExists осуществлен 1 раз
        Вызов PaymentProcessingStorage::removeOrderByNumber осуществлен 1 раз
        Вызов PaymentProcessingStorage::blockApplication осуществлен 1 раз
        Вызов PaymentRepository::checkPaidOrderIsExists осуществлен 1 раз
        Вызов PaymentRepository::checkSumIsValidForOrderNumber осуществлен 1 раз
        Вызов PaymentRepository::setOrderIsPaid осуществлен 1 раз
        Вызов PaymentService::sendPayment осуществлен 1 раз
        Вызов Logger::sendAlarm осуществлен 1 раз
        Вызов PaymentWorker::sendToRefund осуществлен 1 раз

        2.10.d) Возврат сообщения "Ошибка сервиса, сумма скоро вернется к вам на карту" с кодом 520




# Интеграционное тестирование

## front - back

В рамках тестирования front - back можно выделить несколько кейсов предполагаемого поведения - использование фронта as is, то есть мы предполагаем рутинный сценарий действий пользователя по заполнению/незаполнению форм и отправки данных на сервер: В рамках данного кейса не предполагается несколько групп ошибок - ошибки, связанные с неуказанием наименования поля перед упаковкой в JSON объект, то есть рутинно должны отправлятся все поля, если таковое не указано, значением по умолчанию предполагается пустая строка. Но возможен кейс нерутинного использования, когда пользователь может путем изменения кода фронта удалить определенное поле из списка или изменить его тип. Подобные ошибки были описаны в модульном тестировании как "Если 'поле' имеет отличный от строки тип/Если 'поле' не найдено в списке входящих параметров". Далее в кейсах интеграционного тестирования фронта я буду описывать кейсы как рутинного использования фронтенда, так и нерутинного.

На бэке в PaymentController используем моки для репозитория и сервиса оплаты A. Мок метода репозитория setOrderIsPaid возвращает true, мок метода сервиса оплаты A возвращает "{"process":true, "transcation_id": "transcation_id", "code"; 200}";

Таким образом: PaymentController->contains:[PaymentService:Mocked, PaymentRepository:Mocked]

1 Валидационные кейсы

        "{
            "card_number": "2918231098324571",
            "card_holder": "Ivan Ivanov",
            "card_expiration": "11/30",
            "cvv": "325",
            "order_number": "1g-x2f-q112-op32",
            "sum": "25,425"
        }"



    1.1) card_number
        1.1.1) 
        Запрос:
        "{
           "card_number": "111122223333qqqq",
           "card_holder": "Ivan Ivanov",
           "card_expiration": "11/30",
           "cvv": "325",
           "order_number": "1g-x2f-q112-op32",
           "sum": "25,425"
        }"
        1.1.1) После ответа от бэка, поле "Номер карты" выделяется красной рамой с текстом над полем "Номер карты содержит отличные от цифр символы"


        1.1.2)
        Запрос:
         "{
           "card_number": "",
           "card_holder": "Ivan Ivanov",
           "card_expiration": "11/30",
           "cvv": "325",
           "order_number": "1g-x2f-q112-op32",
           "sum": "25,425"
        }"
        1.1.2) После ответа от бэка, поле "Номер карты" выделяется красной рамой с текстом над полем "Номер карты не может быть пустым"


        1.1.3)
        Запрос:
        "{
           "card_number": 222,
           "card_holder": "Ivan Ivanov",
           "card_expiration": "11/30",
           "cvv": "325",
           "order_number": "1g-x2f-q112-op32",
           "sum": "25,425"
        }"
        1.1.3) После ответа от бэка, поле "Номер карты" выделяется красной рамой с текстом над полем "Номер карты должен быть строкой"


        1.1.4)
        Запрос:
        "{
           "card_holder": "Ivan Ivanov",
           "card_expiration": "11/30",
           "cvv": "325",
           "order_number": "1g-x2f-q112-op32",
           "sum": "25,425"
        }"
        1.1.4) После ответа от бэка, поле "Номер карты" выделяется красной рамой с текстом над полем "Номер карты не найден в списке входящих параметров"


    1.2) card_holder
        1.2.1)
        Запрос:
        "{
           "card_number": "2918231098324571"
           "card_holder": "Ivan111",
           "card_expiration": "11/30",
           "cvv": "325",
           "order_number": "1g-x2f-q112-op32",
           "sum": "25,425"
        }"
        1.2.1) После ответа от бэка, поле "Владелец карты" выделяется красной рамой с текстом над полем "Владелец карты содержит некорректные символы"

        1.2.2)
        Запрос:
         "{
           "card_number": "2918231098324571"
           "card_holder": "",
           "card_expiration": "11/30",
           "cvv": "325",
           "order_number": "1g-x2f-q112-op32",
           "sum": "25,425"
        }"
        1.2.2) После ответа от бэка, поле "Владелец карты" выделяется красной рамой с текстом над полем "Владелец карты не может быть пустым"


        1.2.3)
        Запрос:
         "{
           "card_number": "2918231098324571"
           "card_expiration": "11/30",
           "cvv": "325",
           "order_number": "1g-x2f-q112-op32",
           "sum": "25,425"
        }"
        1.2.3) После ответа от бэка, поле "Владелец карты" выделяется красной рамой с текстом над полем "Владелец карты не обнаружен в списке входящих параметров"

        1.2.4)
        Запрос:
         "{
           "card_number": "2918231098324571",
           "card_holder": "Anna",
           "card_expiration": "11/30",
           "cvv": "325",
           "order_number": "1g-x2f-q112-op32",
           "sum": "25,425"
        }"
        1.2.4) После ответа от бэка, поле "Владелец карты" выделяется красной рамой с текстом над полем "Владелец карты не содержит фамилию"


        1.2.5)
        Запрос:
        "{
           "card_number": "2918231098324571",
           "card_holder": "Anna Maria Ii",
           "card_expiration": "11/30",
           "cvv": "325",
           "order_number": "1g-x2f-q112-op32",
           "sum": "25,425"
        }"
        1.2.5) После ответа от бэка, поле "Владелец карты" выделяется красной рамой с текстом над полем "Владелец карты не является корректным"

        1.2.6)
        Запрос:
        "{
           "card_number": "2918231098324571",
           "card_holder": "Anna-Maria-Ii",
           "card_expiration": "11/30",
           "cvv": "325",
           "order_number": "1g-x2f-q112-op32",
           "sum": "25,425"
        }"
        1.2.6) После ответа от бэка, поле "Владелец карты" выделяется красной рамой с текстом над полем "Владелец карты не является корректным"



    1.3) card_expiration

        1.3.1)
        Запрос:
         "{
            "card_number": "2918231098324571",
            "card_holder": "Ivan Ivanov",
            "card_expiration": "15/30",
            "cvv": "325",
            "order_number": "1g-x2f-q112-op32",
            "sum": "25,425"
        }"
        1.3.1) После ответа от бэка, поле "Срок действия" выделяется красной рамой с текстом над полем "Срок действия содержит некорректный месяц"

        1.3.2)
        Запрос:
        "{
            "card_number": "2918231098324571",
            "card_holder": "Ivan Ivanov",
            "card_expiration": "12/100",
            "cvv": "325",
            "order_number": "1g-x2f-q112-op32",
            "sum": "25,425"
        }"
        1.3.2) После ответа от бэка, поле "Срок действия" выделяется красной рамой с текстом над полем "Срок действия содержит некорректный год"


        1.3.3)
        Запрос:
        "{
            "card_number": "2918231098324571",
            "card_holder": "Ivan Ivanov",
            "card_expiration": "12-25",
            "cvv": "325",
            "order_number": "1g-x2f-q112-op32",
            "sum": "25,425"
        }"
        1.3.3) После ответа от бэка, поле "Срок действия" выделяется красной рамой с текстом над полем "Срок действия содержит некорректный разделитель"

        1.3.4)
        Запрос:
        "{
            "card_number": "2918231098324571",
            "card_holder": "Ivan Ivanov",
            "card_expiration": "",
            "cvv": "325",
            "order_number": "1g-x2f-q112-op32",
            "sum": "25,425"
        }"
        1.3.4) После ответа от бэка, поле "Срок действия" выделяется красной рамой с текстом над полем "Срок действия не может быть пустым"


        1.3.5)
        Запрос:
        "{
            "card_number": "2918231098324571",
            "card_holder": "Ivan Ivanov",
            "cvv": "325",
            "order_number": "1g-x2f-q112-op32",
            "sum": "25,425"
        }"
        1.3.5) После ответа от бэка, поле "Срок действия" выделяется красной рамой с текстом над полем "Срок действия не найден в списке входящих параметров"

    
    1.4) cvv

        1.4.1)
        Запрос:
         "{
            "card_number": "2918231098324571",
            "card_holder": "Ivan Ivanov",
            "card_expiration": "11/30",
            "cvv": "32q",
            "order_number": "1g-x2f-q112-op32",
            "sum": "25,425"
        }"
        1.4.1) После ответа от бэка, поле "cvv" выделяется красной рамой с текстом над полем "cvv содержит отличные от цифр символы"


        1.4.2)
        Запрос:
         "{
            "card_number": "2918231098324571",
            "card_holder": "Ivan Ivanov",
            "card_expiration": "11/30",
            "cvv": "12",
            "order_number": "1g-x2f-q112-op32",
            "sum": "25,425"
        }"
        1.4.2) После ответа от бэка, поле "cvv" выделяется красной рамой с текстом над полем "Длина cvv не равна 3"

        1.4.3)
        Запрос:
         "{
            "card_number": "2918231098324571",
            "card_holder": "Ivan Ivanov",
            "card_expiration": "11/30",
            "cvv": "",
            "order_number": "1g-x2f-q112-op32",
            "sum": "25,425"
        }"
        1.4.3) После ответа от бэка, поле "cvv" выделяется красной рамой с текстом над полем "cvv не может быть пустым"

        1.4.4)
        Запрос:
         "{
            "card_number": "2918231098324571",
            "card_holder": "Ivan Ivanov",
            "card_expiration": "11/30",
            "cvv": 222,
            "order_number": "1g-x2f-q112-op32",
            "sum": "25,425"
        }"
        1.4.4) После ответа от бэка, поле "cvv" выделяется красной рамой с текстом над полем "cvv должен быть строкой"


        1.4.5)
        Запрос:
         "{
            "card_number": "2918231098324571",
            "card_holder": "Ivan Ivanov",
            "card_expiration": "11/30",
            "order_number": "1g-x2f-q112-op32",
            "sum": "25,425"
        }"
        1.4.5) После ответа от бэка, поле "cvv" выделяется красной рамой с текстом над полем "cvv не найден в списке параметров"


    1.5) order_number

        1.5.1)
        Запрос:
        "{
            "card_number": "2918231098324571",
            "card_holder": "Ivan Ivanov",
            "card_expiration": "11/30",
            "cvv": "325",
            "order_number": "1g-x2f-q112-op322",
            "sum": "25,425"
        }"
        1.5.1) После ответа от бэка, поле "Номер заказа" выделяется красной рамой с текстом над полем "Номер заказа не может превышать 16 символов"

        1.5.2)
        Запрос:
        "{
            "card_number": "2918231098324571",
            "card_holder": "Ivan Ivanov",
            "card_expiration": "11/30",
            "cvv": "325",
            "order_number": "",
            "sum": "25,425"
        }"
        1.5.2) После ответа от бэка, поле "Номер заказа" выделяется красной рамой с текстом над полем "Номер заказа не может быть пустым"

        1.5.3)
        Запрос:
        "{
            "card_number": "2918231098324571",
            "card_holder": "Ivan Ivanov",
            "card_expiration": "11/30",
            "cvv": "325",
            "order_number": 222,
            "sum": "25,425"
        }"
        1.5.3) После ответа от бэка, поле "Номер заказа" выделяется красной рамой с текстом над полем "Номер заказа должен быть строкой"

        1.5.4)
        Запрос:
        "{
            "card_number": "2918231098324571",
            "card_holder": "Ivan Ivanov",
            "card_expiration": "11/30",
            "cvv": "325",
            "sum": "25,425"
        }"
        1.5.4) После ответа от бэка, поле "Номер заказа" выделяется красной рамой с текстом над полем "Номер заказа не найден в списке входящих параметров"

 

    1.6) sum
        1.6.1)
        Запрос:
         "{
            "card_number": "2918231098324571",
            "card_holder": "Ivan Ivanov",
            "card_expiration": "11/30",
            "cvv": "325",
            "order_number": "1g-x2f-q112-op32",
            "sum": "25.425"
        }"
         1.6.1) После ответа от бэка, поле "Сумма заказа" выделяется красной рамой с текстом над полем "Сумма заказа не является корректной"


        1.6.1)
        Запрос:
         "{
            "card_number": "2918231098324571",
            "card_holder": "Ivan Ivanov",
            "card_expiration": "11/30",
            "cvv": "325",
            "order_number": "1g-x2f-q112-op32",
            "sum": 25.425
        }"
        1.6.2) После ответа от бэка, поле "Сумма заказа" выделяется красной рамой с текстом над полем "Сумма заказа должна быть строкой"


        1.6.3)
        Запрос:
         "{
            "card_number": "2918231098324571",
            "card_holder": "Ivan Ivanov",
            "card_expiration": "11/30",
            "cvv": "325",
            "order_number": "1g-x2f-q112-op32",
            "sum": ""
        }"
        1.6.3) После ответа от бэка, поле "Сумма заказа" выделяется красной рамой с текстом над полем "Сумма заказа должна не может быть пустой"

        1.6.4)
        Запрос:
         "{
            "card_number": "2918231098324571",
            "card_holder": "Ivan Ivanov",
            "card_expiration": "11/30",
            "cvv": "325",
            "order_number": "1g-x2f-q112-op32",
        }"
        1.6.4) После ответа от бэка, поле "Сумма заказа" выделяется красной рамой с текстом над полем "Сумма заказа не найдена в списке параметров"


    2) Запрос:
    "{
        "card_number": "2918231098324571",
        "card_holder": "Ivan Ivanov",
        "card_expiration": "11/30",
        "cvv": "325",
        "order_number": "1g-x2f-q112-op32",
        "sum": "25,425"
    }"

    2.1) Переопределение в коде метода запроса с POST на PUT/PATCH/DELETE - после ответа от бэка вывести в консоль ответ "Method not allowed"

    2.2) В случае недоступности бэка после запроса, вывести сообщение об ошибке "Сервер недоступен, пожалуйста попробуйте позднее"

    2.3) После получения ответа от бэка, в случае ошибки 500/520, на фронте выводится сообщение об ошибке "Не удалось совершить операцию, повторите операцию позднее"

    2.4) После получения ответа от бэка, в случае ошибки 502, на фронте выводится сообщение об ошибке "Не удалось совершить операцию "

    2.5.a)Мок метода сервиса оплаты A возвращает 200. В случае указания валидных card_number, card_holder, card_expiration, cvv, order_number - после ответа от бэка вывести сообщение "Оплата прошла успешно"

    2.5.б) Мок метода сервиса оплаты A возвращает 403. В случае указания валидных card_number, card_holder, card_expiration, cvv, order_number - после ответа от бэка вывести сообщение об ошибке "Не удалось списать данные с карты, проверьте реквизиты"


## back - repository

    Окружение - В рамках связки бэк - репозиторий предполагается наличие асинхронного воркера, который будет обрабатывать платежи, оплата за которые прошла, но потом возникла ошибка на бэке - далее рассматривается единственный кейс такой ошибки, а именно - ошибка бд при записи оплаченного заказа. Данный воркер должен будет передавать в платежный сервис A идентификатор платежной транзакции со статусом refund для перевода денег обратно клиенту. Конкретно в рамках данного кейса мы тестируем связку бэк - репозиторий, и соответственно, будем мокать данный воркер. Так же для предотвращения повторной обработки неоплаченного платежа, предусмотрено сервис к in memory хранилищу PaymentProcessingStorage с методами PaymentProcessingStorage::setOrderByNumberIfNotExists(string $order_number), PaymentProcessingStorage::removeOrderByNumber(string $order_number). Методы PaymentProce

    В репозитории должны быть следующие методы: 

    checkPaidOrderIsExists(string $order_number): bool - должен проверять наличие в базе оплаченного заказа и который всегда должен вызыватся до вызова метода платежного сервиса A.

    checkSumIsValidForOrderNumber(string $order_number, float $sum): bool - должен проверять соответствие суммы заказа номеру заказа и который всегда должен вызыватся до вызова метода платежного сервиса A.

    setOrderIsPaid(string $order_number, float $sum): bool - должен осуществлять запись номера заказа успешно совершенного платежа и его суммы в базу данных.

    getPaidOrderByNumber(string $order_number):Order - должен вернуть entity оплаченного заказа

    Метод репозитория мы вызваем из контроллера оплаты, в котором мокаем сервис оплаты A, с моком метода оплаты, должным возвращать "{"process":true, "transcation_id": "transcation_id", "code"; 200}" Репозиторий использует тестовую базу данных.
    
    На бэке в контроллере оплаты PaymentController используем мок для сервиса оплаты A, мок сервиса к in memory хранилищу PaymentProcessingStorage, мок Воркера для refund, мок Логгера. репозиторий используем реальный с тестовой базой данных.

    Таким образом: 

    PaymentController->содержит[PaymentService:Mocked, PaymentProcessingStorage::Mocked, Logger::Mocked PaymentRepository:Real, PaymentWorker:Mocked]
    PaymentRepository->содержит[DatabaseConnection:Real]
    DatabaseConnection->осуществляет соединение с тестовой базой данных

    Далее, действует конвенция, в рамках которой обозначение ->:in следующее за вызовом метода, обозначает, что последующие методы должны вызыватся в данном методе, а обозначение ->:out означает, что осуществляет выход из метода

    Входящие данные для каждого из последующих кейсов:
    [
        'card_number': '2918231098324571',
        'card_holder': 'Ivan Ivanov',
        'card_expiration': '11/30',
        'cvv': '325',
        'order_number': '1g-x2f-q112-op32',
        'sum': '25,425'  
    ],           
 
    Предполагаемая успешная цепочка выполнения оплаты заказа: 

    Вызов PaymentController::sendPayment($payment_params) ->:in

    Вызов PaymentProcessingStorage::setOrderByNumberIfNotExists($order_number) -> PaymentRepository::checkPaidOrderIsExists($order_number) -> PaymentRepository::checkSumIsValidForOrderNumber($order_number, $sum) -> PaymentService::sendPayment($encoded_payment_params) -> PaymentRepository::setOrderIsPaid($order_number) -> PaymentProcessingStorage::removeOrderByNumber($order_number) -> return ['message': 'Оплата прошла успешно', 'status': 200]


    1.1) Ошибка соединения с базой данных/инконсистентности вводимых плейсохолдеров к кл-ву параметров и прочее в методе checkPaidOrderIsExists:
    Мок PaymentProcessingStorage::setOrderByNumberIfNotExists(string $order_number): bool -> true
    Мок PaymentProcessingStorage::removeOrderByNumber(string $order_number): bool -> true
    Мок PaymentProcessingStorage::blockApplication(): bool -> true
    Мок PaymentService::sendPayment(string $encoded_payment_params): "{"processing": true, "transaction_id": "transaction_id", "status": 200}"
    Мок PaymentWorker::sendToRefund(string $payment_transaction_id): bool -> true
    Мок Logger::sendAlarm(string $message): void


    1.2) Вызов PaymentController::processPayment($payment_params) ->:in

    Вызов PaymentProcessingStorage::setOrderByNumberIfNotExists($order_number) -> PaymentRepository::checkPaidOrderIsExists($order_number) -> исключение DatabaseException->:out 
    
    PaymentController::processPayment -> PaymentProcessingStorage::removeOrderByNumber($order_number) -> Logger::sendAlarm($exception_message) -> PaymentProcessingStorage::blockApplication() -> return ['error': 'Ошибка сервиса, повторите операцию позднее', 'status': 520]

    1.3)
    Вызов PaymentProcessingStorage::setOrderByNumberIfNotExists осуществлен 1 раз
    Вызов PaymentProcessingStorage::removeOrderByNumber осуществлен 1 раз
    Вызов Logger::sendAlarm осуществлен 1 раз
    Вызов PaymentProcessingStorage::blockApplication осуществлен 1 раз

    Вызов PaymentService::sendPayment осуществлен 0 раз


    2.1) Сумма заказа несоответствует номеру заказа в методе checkSumIsValidForOrderNumber
    Мок PaymentProcessingStorage::setOrderByNumberIfNotExists(string $order_number): bool -> true
    Мок PaymentProcessingStorage::removeOrderByNumber(string $order_number): bool -> true
    Мок PaymentProcessingStorage::blockApplication(): bool -> true
    Мок PaymentService::sendPayment(string $encoded_payment_params): "{"processing": true, "transaction_id": "transaction_id", "status": 200}"
    Мок PaymentWorker::sendToRefund(string $payment_transaction_id): bool -> true
    Мок Logger::sendAlarm(string $message): void

    2.2) Вызов PaymentController::processPayment($payment_params) ->:in

    Вызов PaymentProcessingStorage::setOrderByNumberIfNotExists($order_number) -> PaymentRepository::checkPaidOrderIsExists($order_number) -> PaymentRepository::checkSumIsValidForOrderNumber($order_number) -> возврат false -> PaymentProcessingStorage::removeOrderByNumber($order_number) -> return ['message': 'Сумма заказа несоответствует номеру]

    2.3)
    Вызов PaymentProcessingStorage::setOrderByNumberIfNotExists осуществлен 1 раз
    Вызов PaymentProcessingStorage::removeOrderByNumber осуществлен 1 раз
    
    Вызов PaymentProcessingStorage::blockApplication осуществлен 0 раз
    Вызов PaymentService::sendPayment осуществлен 0 раз
    Вызов PaymentWorker::sendToRefund осуществлен 0 раз
    Вызов Logger::sendAlarm осуществлен 0 раз


    3.1) Ошибка соединения с базой данных/инконсистентности вводимых плейсохолдеров к кл-ву параметров и прочее в методе в методе checkSumIsValidForOrderNumber
    Мок PaymentProcessingStorage::setOrderByNumberIfNotExists(string $order_number): bool -> true
    Мок PaymentProcessingStorage::removeOrderByNumber(string $order_number): bool -> true
    Мок PaymentProcessingStorage::blockApplication(): bool -> true
    Мок PaymentService::sendPayment(string $encoded_payment_params): "{"processing": true, "transaction_id": "transaction_id", "status": 200}"
    Мок PaymentWorker::sendToRefund(string $payment_transaction_id): bool -> true
    Мок Logger::sendAlarm(string $message): void

    3.2) Вызов PaymentController::processPayment($payment_params) ->:in

    Вызов PaymentProcessingStorage::setOrderByNumberIfNotExists($order_number) -> PaymentRepository::checkPaidOrderIsExists($order_number) -> PaymentRepository::checkSumIsValidForOrderNumber($order_number, $sum) -> исключение DatabaseException ->:out 

    PaymentController::processPayment -> PaymentProcessingStorage::removeOrderByNumber($order_number) -> Logger::sendAlarm($exception_message) -> PaymentProcessingStorage::blockApplication() -> return ['error': 'Ошибка сервиса, повторите операцию позднее', 'status': 520]

    3.3)
    Вызов PaymentProcessingStorage::setOrderByNumberIfNotExists осуществлен 1 раз
    Вызов PaymentProcessingStorage::removeOrderByNumber осуществлен 1 раз
    Вызов PaymentProcessingStorage::blockApplication осуществлен 1 раз
    Вызов Logger::sendAlarm осуществлен 1 раз
    
    Вызов PaymentService::sendPayment осуществлен 0 раз
    Вызов PaymentWorker::sendToRefund осуществлен 0 раз

    4.1) Ошибка соединения с базой данных/инконсистентности вводимых плейсохолдеров к кл-ву параметров и прочее в методе в методе setOrderIsPaid($order_number, $sum)
    Мок PaymentProcessingStorage::setOrderByNumberIfNotExists(string $order_number): bool -> true
    Мок PaymentProcessingStorage::removeOrderByNumber(string $order_number): bool -> true
    Мок PaymentService::sendPayment(string $encoded_payment_params): string -> "{"processing": true, "transaction_id": "transaction_id", "status": 200}"
    Мок PaymentWorker::sendToRefund(string|int $payment_transaction_id): bool -> true
    Мок PaymentProcessingStorage::blockApplication(): bool -> true
    Мок Logger::sendAlarm(): void

    4.2) Вызов PaymentController::processPayment($payment_params) ->:in

    Вызов PaymentProcessingStorage::setOrderByNumberIfNotExists($order_number) -> PaymentRepository::checkPaidOrderIsExists($order_number) -> PaymentRepository::checkSumIsValidForOrderNumber($order_number, $sum) -> PaymentService::sendPayment($encoded_payment_params) -> PaymentRepository::setOrderIsPaid($order_number, $sum) -> исключение DatabaseException ->:out 

    PaymentController::processPayment -> PaymentProcessingStorage::removeOrderByNumber($order_number) -> PaymentWorker::sendToRefund($payment_transaction_id) -> Logger::sendAlarm($exception_message) -> PaymentProcessingStorage::blockApplication()

    4.3)
    Вызов PaymentProcessingStorage::setOrderByNumberIfNotExists осуществлен 1 раз
    Вызов PaymentProcessingStorage::removeOrderByNumber осуществлен 1 раз
    Вызов PaymentService::sendPayment осуществлен 1 раз
    Вызов PaymentWorker::sendToRefund осуществлен 1 раз
    Вызов PaymentProcessingStorage::blockApplication осуществлен 1 раз
    Вызов Logger::sendAlarm осуществлен 1 раз
    Вызов PaymentWorker::sendToRefund осуществлен 1 раз

    

    5) Сценарий проходит успешно:
    Мок PaymentProcessingStorage::setOrderByNumberIfNotExists(string $order_number): bool -> true
    Мок PaymentProcessingStorage::removeOrderByNumber(string $order_number): bool -> true
    Мок PaymentService::sendPayment(string $encoded_payment_params): string -> "{"processing": true, "transaction_id": "transaction_id", "status": 200}"
    Мок PaymentWorker::sendToRefund(string|int $payment_transaction_id): bool -> true
    Мок PaymentProcessingStorage::blockApplication(): bool -> true
    Мок Logger::sendAlarm(): void

    Вызов PaymentController::sendPayment($payment_params) ->:in

    Вызов PaymentProcessingStorage::setOrderByNumberIfNotExists($order_number) -> PaymentRepository::checkPaidOrderIsExists($order_number) -> PaymentRepository::checkSumIsValidForOrderNumber($order_number, $sum) -> PaymentService::sendPayment($encoded_payment_params) -> PaymentRepository::setOrderIsPaid($order_number, $sum) -> PaymentProcessingStorage::removeOrderByNumber($order_number) -> Возврат ответа "Оплата прошла успешно" с кодом 200
    
    6) Кейс проверки заказа на существования заказа в системе - инкапсулирует в себя цепочку вызовов, приведенную в кейсе 5, и последующий вызов метода PaymentRepository::getPaidOrderByNumber(string $order_number)

    Вызов PaymentController::sendPayment($payment_params) ->:in

    Вызов PaymentProcessingStorage::setOrderByNumberIfNotExists($order_number) -> PaymentRepository::checkPaidOrderIsExists($order_number) -> PaymentRepository::checkSumIsValidForOrderNumber($order_number, $sum) -> PaymentService::sendPayment($encoded_payment_params) -> PaymentRepository::setOrderIsPaid($order_number, $sum) -> PaymentProcessingStorage::removeOrderByNumber($order_number) -> return ['message': 'Оплата прошла успено', 'status': 200] ->:out

    Вызов PaymentRepository::getPaidOrderByNumber($order_number) -> order -> assert to: ($order->getSum() * 100) === $sum AND $order->isPaid() === true
    
    7) Кейс, когда вызываем PaymentRepository::getPaidOrderByNumber без предварительного создания заказа:
    Вызов PaymentRepository::getPaidOrderByNumber($order_number) -> order -> assert to: $order === NULL
    
    


## back - payment_service
    Окружение - сервис оплаты А представляет sandbox сервиса оплаты, а репозиторий является моком.

    1.1) Реквизиты для успешной оплаты с кодом ответа 200:
    "
    {
        "card_number": "4111 1111 1111 1111",
        "cvv": "111",
        "amount": {
            "value": :"input_sum",
            "currency": "RUB"
        }

    }
    "
    1.2) В случае, если сервис оплаты вернул ответ с кодом 200, выполнить setOrderIsPaid репозитория (рассматриваем успешный кейс - иные случае описаны в секции back - repository) и вернуть клиенту ответ с кодом 200.

    2.1) Реквизиты для невалидных реквизитов с 403
     "
    {
        "card_number": "4000 0000 0000 0101",
        "cvv": "111",
        "amount": {
            "value": :"input_sum",
            "currency": "RUB"
        }

    }
    "
    2.2) В случае, если сервис оплаты вернул ответ с кодом 403, вернуть клиенту ответ с кодом 403


## back - worker
    Окружение - воркер представляет собой действительный воркер, инкапсулирующий мок сервиса A оплаты.

    1) В случае, если воркер не отвечает, выбросить исключение WorkerIsNotAvailable, клиенту вернуть код 520
    2) В случае успеха, воркер возвращает ответ ['accepted' => true] 


# Системное тестирование
Окружение - sandbox сервиса оплаты А и репозиторий с подключением к базе данных.

    1) Если один/несколько параметров из ['card_number', 'card_holder', 'card_expiration', 'cvv', 'order_number', 'sum'] отсутствуют/не являются корректными, то:
        Клиент -> backend -> валидация списка входящих параметров -> валидация списка входящих параметров |EXCEPTION| -> возврат сообщения об ошибке с кодом 400 -> клиент -> вывод сообщения об ошибке валидации.

    2) Если один/несколько параметров из ['card_number', 'card_holder', 'card_expiration', 'cvv'] не являются действительными, то:
        Клиент -> backend -> валидация списка входящих параметров -> запись номера текущего заказа в in memory хранилище если такового не существует -> проверка в репозитории номера заказа на предмет оплаты -> backend -> запрос к сервису оплаты A |403 response| -> backend -> возврат ошибки 403 -> удаление сведений об обработке заказа из in memory хранилища -> клиент -> вывод сообщения об ошибке "Не удалось списать данные с карты, проверьте реквизиты"

    3) Если сумма платежа несоответствует номеру заказу, то:
        Клиент -> backend -> валидация списка входящих параметров -> запись номера текущего заказа в in memory хранилище если такового не существует -> проверка в репозитории номера заказа на предмет оплаты -> backend -> проверка в репозитории соответствия суммы платежа номеру заказа -> backend |EXCEPTION| -> удаление сведений о заказе из in memory хранилища -> возврат ошибки 409 с телом ответа "Сумма платежа несоответствует номеру заказа" -> клиент -> вывод сообщения об ошибке "Номер платежа несоответствует номеру заказа"
    
    4) Если оплаченный заказ существует в системе, то:
        Клиент -> backend -> валидация списка входящих параметров -> запись номера текущего заказа в in memory хранилище если такового не существует -> проверка в репозитории номера заказа на предмет оплаты -> backend |EXCEPTION| -> backend -> удаление сведений о заказе из in memory хранилища -> возврат ошибки 409 c телом ответа "Данный заказ уже существует в системе" -> клиент -> вывод сообщения об ошибке "Данный заказ уже существует в системе"

    5) Если сервис оплаты A недоступен, то:
        Клиент -> backend -> валидация списка входящих параметров -> запись номера текущего заказа в in memory хранилище если такового не существует -> проверка в репозитории номера заказа на предмет оплаты -> backend ->  проверка в репозитории соответствия суммы платежа номеру заказа -> backend -> запрос к сервису оплаты A |ERROR| -> backend -> удаление сведений об обработке заказа из in memory хранилища -> возврат сообщения об ошибке с кодом 500 -> клиент -> вывод сообщения об ошибке "Сервис оплаты временно недоступен, повторите операцию позднее"

    6) Если возникли ошибки бд во время вызова checkPaidOrderIsExists репозитория, то:
        Клиент -> backend -> валидация списка входящих параметров -> запись номера текущего заказа в in memory хранилище если такового не существует -> проверка в репозитории номера заказа на предмет оплаты|ERROR| -> backend -> удаление сведений о заказе из in memory хранилища -> возврат сообщения об ошибке с кодом 500 -> клиент -> вывод сообщения об ошибке "Сервис временно недоступен, повторите операцию позднее"

    7) Если возникли ошибки бд во время вызова checkSumIsValidForOrderNumber репозитория, то:
        Клиент -> backend -> валидация списка входящих параметров -> запись номера текущего заказа в in memory хранилище если такового не существует -> проверка в репозитории номера заказа на предмет оплаты -> backend -> проверка в репозитории соответствия суммы платежа номеру заказа|ERROR| -> backend -> удаление сведений о заказе из in memory хранилища -> возврат сообщения об ошибке с кодом 500 -> клиент -> вывод сообщения об ошибке "Сервис временно недоступен, повторите операцию позднее".

    8) Если возникли ошибки бд во время вызова setOrderIsPaid, то:
        Клиент -> backend -> валидация списка входящих параметров -> запись номера текущего заказа в in memory хранилище если такового не существует -> проверка в репозитории номера заказа на предмет оплаты -> backend -> проверка в репозитории соответствия суммы платежа номеру заказа -> backend -> обращения к платежному сервису A -> запись в базу сведений о оплаченном заказе|ERROR| -> backend -> передача идентификатора платежной транзакции в воркер для refund -> возврат сообщения об ошибке с кодом 500 -> клиент -> удаление сведений об обработке текущего заказа из in memory хранилища -> вывод сообщения об ошибке "Ошибка сервиса, ожидается возврат средств"

    9) Повторная отправка заказа:
        Клиент -> backend -> валидация списка входящих параметров -> запись номера текущего заказа в in memory хранилище если такового не существует -> возврат ответа "Данный заказ уже проходит обработку в системе" с кодом 409

    10) Повторная отправка нескольких заказов:
        10.1)Клиент -> backend -> валидация списка входящих параметров -> запись номера текущего заказа в in memory хранилище если такового не существует -> возврат ответа "Данный заказ уже проходит обработку в системе" с кодом 409

         --->  На 0.0001мс позже, чем 10.1 -->    10.2) Клиент -> backend -> валидация списка входящих параметров -> запись номера текущего заказа в in memory хранилище если такового не существует -> возврат ответа "Данный заказ уже проходит обработку в системе" с кодом 409


    10) Позитивный кейс:
        Клиент -> backend -> валидация списка входящих параметров -> запись номера текущего заказа в in memory хранилище если такового не существует -> проверка в репозитории номера заказа на предмет оплаты -> проверка в репозитории соответствия суммы платежа номеру заказа -> backend -> обращение к платежному сервису A -> запись в базу сведений о оплаченном заказе -> backend -> удаление сведений об обработке текущего заказа из in memory хранилища -> возврат сообщения с кодом 200 -> клиент -> вывод сообщения "Оплата прошла успешно"

    11) Повторная отправка
        
        Клиент -> backend -> валидация списка входящих параметров -> запись номера текущего заказа в in memory хранилище если такового не существует ->


