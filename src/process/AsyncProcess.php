<?php

namespace AloneWebMan\RoBot\process;

use AloneWebMan\RoBot\BotWay;
use AloneWebMan\RoBot\BotRoute;
use Workerman\Connection\TcpConnection;

/**
 * 接收机器人信息(异步)
 */
class AsyncProcess {
    public function onMessage(TcpConnection $connection, mixed $data): void {
        if (!empty($arr = BotWay::isJson($data))) {
            if (!empty($plugin = $arr['plugin'] ?? '') && !empty($token = $arr['token'] ?? '') && !empty($post = $arr['post'] ?? [])) {
                BotRoute::exec($plugin, $token, $post);
            }
        }
    }
}