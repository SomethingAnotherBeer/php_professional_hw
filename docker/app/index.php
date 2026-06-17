<?php
declare(strict_types=1);



echo "current application <br/><br/>";

function dbTest(PDO $connection) {
    $stmt = $connection->prepare("INSERT INTO products (category_id, product_name) VALUES (:category_id, :product_name)");

    $stmt = $connection->prepare("SELECT id, product_name FROM products");
    $stmt->execute();

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Вывод из базы данных<br/>";
    echo "<pre>";
    print_r($products);
    echo "</pre>";
}

function redisTest(Redis $redis) {
    $redis->set('test_key', "message for redis");

    echo "Вывод из редиса<br/>";
    echo "По ключу test_key было получено значение " . $redis->get('test_key') . "<br/>";
}


try {
    $redis = new Redis();
    $redis->connect("redis_container", 6379);

    $connection = new PDO("mysql:host=mariadb_container;dbname=test_database;charset=utf8", 'root', '555');
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    dbTest($connection);
    redisTest($redis);

}
catch(PDOException $e) {
    echo "Ошибка mariadb: " . $e->getMessage() . "<br/>";
}

catch(RedisException $e) {
    echo "Ошибка redis: " . $e->getMessage() . "<br/>";
}

catch(Exception $e) {
    echo "Ошибка: " . $e->getMessage() . "<br/>";
}