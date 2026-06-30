<?php
declare(strict_types=1);
require_once "vendor/autoload.php";

echo "current application <br/><br/>";

function dbTest(PDO $connection) {
    
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

function getEnvVars(array $expected_env_vars): array {
    $current_env_vars = [];
    $failed_env_vars = [];
    $current_env = null;

    foreach ($expected_env_vars as $expected_env_var_key => $expected_var_type) {
        $current_env = getenv($expected_env_var_key);
        if (!$current_env) {
            $failed_env_vars[] = $expected_env_var_key;
        }

        if ($current_env) {
            if ('int' === $expected_var_type) {
                $current_env = (int)$current_env;
            }
        }

        $current_env_vars[$expected_env_var_key] = $current_env;
    }

    if (count($failed_env_vars) > 0) {
        $failed_env_vars_str = implode(" ", $failed_env_vars);
        throw new Exception("Следующие переменные окружения не смогли быть прочитаны: {$failed_env_vars_str}");
    }

    return $current_env_vars;
    
}


try {

    $db_expected_env = ['DB_HOST' => 'string', 'DB_NAME' => 'string', 'DB_USER' => 'string', 'DB_PASSWORD' => 'string'];
    $redis_expected_env = ['REDIS_HOST' => 'string', 'REDIS_PORT' => 'int'];

    $db_env_vars = getEnvVars($db_expected_env);
    $redis_env_vars = getEnvVars($redis_expected_env);

    $redis = new Redis();
    $redis->connect($redis_env_vars['REDIS_HOST'], $redis_env_vars['REDIS_PORT']);

    $connection = new PDO("mysql:host={$db_env_vars['DB_HOST']};dbname={$db_env_vars['DB_NAME']};charset=utf8", $db_env_vars['DB_USER'], $db_env_vars['DB_PASSWORD']);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    dbTest($connection);
    redisTest($redis);

    $app = App\Application::getInstance();

    

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