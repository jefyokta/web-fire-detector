<?php

use App\Channel\Consumer;
use App\Channel\Publisher;
use App\Channel\Watcher;
use App\Controller\Home;
use App\Database\Pool;
use Oktaax\Oktaax;
use Oktaax\Trait\HasWebsocket;
use Oktaax\Views\PhpView;
use Oktaax\Websocket\Support\Table;
use Swoole\Coroutine;
use Swoole\Server\Task;
use Swoole\Table as SwooleTable;

$app =  new class extends Oktaax {
    use HasWebsocket;
};



$app->setServer('task_worker_num', 2);
$app->setServer('task_enable_coroutine', true);
$app->setView(new PhpView(__DIR__ . "/../resources/view/"));
$app->documentRoot(__DIR__ . "/../public/");

$app->on("Task", function ($server, Task $task) {});
$app->table(function (SwooleTable $table) {
    $table->column("role", SwooleTable::TYPE_STRING, 256);
});
$app->on("Finish", function ($s, $id) {

    echo "finish $id\n";
});
$app->on("WorkerStart", function () {
    // Pool::fill();
});
$app->on("WorkerExit", function () {
    // Pool::close();
});
$app->get("/", [Home::class, 'index']);

$app->get("/test", function () {
    return "1";
});


$app->gate(function ($serv, $req) {
    if (@$req->has('token') ?? false) {
        if ($req->get('token') == 'rahasia') {
            Table::add($req->fd, ["role" => "publisher"]);
        } else if ($req->get('token') == 'detector-rahasia') {
            Table::add($req->fd, ["role" => "detector"]);
        }
    }
});
$app->ws("publish", function ($serv, $client) {

    $serv->toChannel(Consumer::class)->broadcast($client->data['message']);
});
$app->ws("is_fire", function ($serv, $client) {
    $serv->reply( Coroutine::readFile(__DIR__.'/../storage/last_fire') ?? "off");
});
$app->ws("fire_status", function ($serv, $client) {
    Coroutine::writeFile(__DIR__ . "/../storage/last_fire", $client->data['message']);
    echo "got message " . $client->data['message'];
    $serv->toChannel(Publisher::class)->broadcast($client->data['message']);
    $serv->toChannel(Consumer::class)->broadcast($client->data['message']);
});



return $app;
