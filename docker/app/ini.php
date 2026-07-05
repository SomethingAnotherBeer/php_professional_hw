<?php
declare(strict_types=1);
ini_set('session.save_handler', 'redis');
ini_set('session.save_path', $_ENV['SESSION_PATH']);

