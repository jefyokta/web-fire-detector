<?php

namespace App\Channel;

use Oktaax\Interfaces\Channel;
use Oktaax\Websocket\Client;
use Oktaax\Websocket\Support\Table;

class Detector implements Channel
{


    public function eligible(Client $client): bool
    {


        return Table::get($client->fd)["role"] ?? false == "detector";
    }
}
