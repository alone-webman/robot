<?php

namespace AloneWebMan\RoBot\process;

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
                alone_redis_get($config['queue_key'] . "_" . $plugin . "_list", $config['queue_task'], function($arr) {
                    if (!empty($plugin = $arr['plugin'] ?? '') && !empty($token = $arr['token'] ?? '') && !empty($post = $arr['post'] ?? [])) {
                        BotRoute::exec($plugin, $token, $post);
                    }
                });
            }
        });
    }
}