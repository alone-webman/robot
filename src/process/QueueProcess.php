<?php

namespace AloneWebMan\RoBot\process;

use support\Redis;
use AloneWebMan\RoBot\BotWay;
use AloneWebMan\RoBot\BotRoute;
use AloneWebMan\RoBot\BotFacade;

/**
 * 获取redis队列信息处理
 */
class QueueProcess {
    public function onWorkerStart(mixed $worker): void {
        $name = $worker->name;
        $start = strlen("plugin.");
        $end = strpos($name, '.', $start);
        $plugin = substr($name, $start, $end - $start);
        $plugin = !empty($plugin) ? $plugin : BotWay::getPluginName($worker);
        $config = BotFacade::config($plugin);
        BotWay::timer($config['queue_timer'], function() use ($plugin, $config) {
            if ($config['queue_key']) {
                $count = $config['queue_task'];
                for ($i = 1; $i <= $count; $i++) {
                    $json = Redis::rPop($config['queue_key'] . "_" . $plugin . "_list");
                    $arr = json_decode($json, true);
                    if (!empty($plugin = $arr['plugin'] ?? '') && !empty($token = $arr['token'] ?? '') && !empty($post = $arr['post'] ?? [])) {
                        BotRoute::exec($plugin, $token, $post);
                    }
                }
            }
        });
    }
}