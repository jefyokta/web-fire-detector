<?php

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

return [
    "app" => [
        "host" => $_ENV['APP_HOST'] ?? "127.0.0.1",
        "port" => $_ENV["APP_PORT"] ?? 8000,
        "dev" => $_ENV["APP_DEBUG"]
    ],
    "db" => [
        "host" => $_ENV["DB_HOST"],
        "port" => $_ENV["DB_PORT"],
        "name" => $_ENV["DB_NAME"],
        "user" => $_ENV["DB_USER"],
        "pass" => $_ENV["DB_PASS"]
    ]
];
