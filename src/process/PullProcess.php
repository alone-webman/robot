<?php

namespace AloneWebMan\RoBot\process;

use Workerman\Timer;
use Workerman\Coroutine;
use AloneWebMan\RoBot\BotWay;
use AloneWebMan\RoBot\BotRoute;
use AloneWebMan\RoBot\BotFacade;
use AloneWebMan\RoBot\BotProcess;
use Workerman\Connection\AsyncTcpConnection;

/**
 * 主动拉取信息
 */
class PullProcess {
    /**
     * @param mixed $worker
     * @return void
     */
    public function onWorkerStart(mixed $worker): void {
        $id = $worker->id;
        // 获取名称
        $plugin = BotWay::getPluginName($worker);
        // 获取配置
        $config = BotFacade::config($plugin);
        $pull_key = $config["pull_key"] ?? '';
        // 文件保存
        $cache_file = run_path('runtime/alone_bot/' . $plugin . '_bot.cache');
        BotWay::mkDir(dirname($cache_file));
        if ($id === 0) {
            BotProcess::setBotCache($plugin, $cache_file, $pull_key, true);
            BotWay::timer($config['pull_time'], function() use ($plugin, $config, $pull_key, $cache_file) {
                BotProcess::setBotCache($plugin, $cache_file, $pull_key, false);
            });
        } elseif ($id >= 1) {
            BotWay::timer($config['pull_timer'], function() use ($id, $plugin, $config, $pull_key, $cache_file) {
                $items = BotProcess::getBotCache($plugin, $cache_file, $pull_key);
                if (!empty($items) && !empty($item = ($items[$id - 1] ?? []))) {
                    $item = array_merge([
                        "name" => "bot name",
                        "key"  => "",
                        "mode" => 2
                    ], $item);
                    if (!empty($item['key'])) {
                        $update_id_file = run_path('runtime/alone_bot/' . $plugin . '_' . $item['token'] . '_update_id.cache');
                        $update_id = BotProcess::getBotUpdateId($plugin, $item['token'], $update_id_file, $pull_key);
                        $bot = alone_bot($item['key'], false);
                        $bot->getUpdates($update_id, 100, 0, $item["updates"]);
                        $array = $bot->array();
                        $ok = ($array['ok'] ?? '');
                        $result = $array['result'] ?? [];
                        BotFacade::callApi($plugin, "Pull", $item['token'], $ok, $result, $array);
                        if (!empty($ok) && !empty($result)) {
                            $update_ids = array_column($result, 'update_id');
                            $update_id = (int) (max($update_ids) ?: 0);
                            if (!empty($update_id)) {
                                BotProcess::setBotUpdateId($update_id + 1, $plugin, $item['token'], $update_id_file, $pull_key);
                                switch ((int) $item['mode']) {
                                    case 1:
                                        // 实时
                                        foreach ($result as $post) {
                                            BotRoute::exec($plugin, $item['token'], $post);
                                        }
                                        break;
                                    case 3:
                                        // 队列
                                        foreach ($result as $post) {
                                            BotRoute::queue($plugin, $item['token'], $post);
                                        }
                                        break;
                                    case 4:
                                        // 异步
                                        if ($config['async_status'] && $config['async_connect']) {
                                            $async = new AsyncTcpConnection($config['async_connect']);
                                            $async->onConnect = function(AsyncTcpConnection $connection) use ($plugin, $item, $result) {
                                                foreach ($result as $post) {
                                                    $connection->send(json_encode(['plugin' => $plugin, 'token' => $item['token'], 'post' => $post]));
                                                }
                                                $connection->close();
                                            };
                                            $async->connect();
                                        } else {
                                            Coroutine::create(function() use ($plugin, $item, $result) {
                                                foreach ($result as $post) {
                                                    BotRoute::exec($plugin, $item['token'], $post);
                                                }
                                            });
                                        }
                                        break;
                                    default:
                                        // 协程
                                        Coroutine::create(function() use ($plugin, $item, $result) {
                                            foreach ($result as $post) {
                                                BotRoute::exec($plugin, $item['token'], $post);
                                            }
                                        });
                                        break;
                                }
                            }
                        }
                    } else {
                        Timer::sleep($config['pull_wait']);
                    }
                } else {
                    Timer::sleep($config['pull_wait']);
                }
            });
        }
    }
}