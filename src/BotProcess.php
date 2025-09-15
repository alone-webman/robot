<?php


namespace AloneWebMan\RoBot;

use support\Redis;
use Workerman\Events\Fiber;
use AloneWebMan\RoBot\process\PullProcess;
use AloneWebMan\RoBot\process\QueueProcess;
use AloneWebMan\RoBot\process\AsyncProcess;

/**
 * 进程类
 */
class BotProcess {
    /**
     * 自定进程
     * @param string $plugin
     * @return array
     */
    public static function start(string $plugin): array {
        $process = [];
        $config = BotFacade::config($plugin);
        if ($config['pull_status']) {
            $items = (array) BotFacade::callApi($plugin, 'Bot');
            $pull_count = $config['pull_count'] ?? 0;
            $count = $pull_count > 0 ? $pull_count : (count($items) + 8);
            if ($count > 0) {
                $pull_handler = $config["pull_handler"] ?? null;
                $process['pull'] = [
                    "name"      => $plugin,
                    'eventLoop' => Fiber::class,
                    'handler'   => !empty($pull_handler) ? $pull_handler : PullProcess::class,
                    'count'     => $count
                ];
            }
        }
        if ($config['queue_status']) {
            $queue_handler = $config["queue_handler"] ?? null;
            $process['queue'] = [
                "name"      => $plugin,
                'eventLoop' => Fiber::class,
                'handler'   => !empty($queue_handler) ? $queue_handler : QueueProcess::class,
                'count'     => $config['queue_count']
            ];
        }
        if ($config['async_status'] && $config['async_listen']) {
            $async_handler = $config["async_handler"] ?? null;
            $process['async'] = [
                "name"      => $plugin,
                'eventLoop' => Fiber::class,
                "handler"   => !empty($async_handler) ? $async_handler : AsyncProcess::class,
                'listen'    => $config['async_listen'],
                'count'     => $config['async_count']
            ];
        }
        return $process;
    }

    /**
     * 设置update_id
     * @param int    $updateId
     * @param string $plugin
     * @param string $token
     * @param string $file
     * @param string $pull_key
     * @return bool|int
     */
    public static function setBotUpdateId(int $updateId, string $plugin, string $token, string $file, string $pull_key): bool|int {
        ($pull_key) && Redis::set($pull_key . "_" . $plugin . "_" . $token . "_update_id", $updateId);
        return @file_put_contents($file, $updateId);
    }

    /**
     * 获取update_id
     * @param string $plugin
     * @param string $token
     * @param string $file
     * @param string $pull_key
     * @return int
     */
    public static function getBotUpdateId(string $plugin, string $token, string $file, string $pull_key): int {
        if (!empty($pull_key)) {
            $updateId = (Redis::get($pull_key . "_" . $plugin . "_" . $token . "_update_id")) ?: "";
            if (!empty($updateId)) {
                return $updateId;
            }
        }
        return (@file_get_contents($file) ?: 0);

    }

    /**
     * 保存机器人信息
     * @param string $plugin
     * @param string $file
     * @param string $pull_key
     * @param bool   $start
     * @return array
     */
    public static function setBotCache(string $plugin, string $file, string $pull_key, bool $start): array {
        $items = (array) BotFacade::callApi($plugin, "Bot");
        $config = BotFacade::config($plugin);
        $save = [];
        foreach ($items as $item) {
            // $bot = alone_bot($item['key']);
            $token = BotWay::getBotRouteToken($item['key'], $config['app_key']);
            $type = BotFacade::callApi($plugin, "Type", $token);
            $msgType = array_merge([
                //普通消息
                'message'              => true,
                //回调查询（来自按钮点击）
                'callback_query'       => true,
                //匿名投票,接收投票详细
                'poll'                 => false,
                //实名投票 那个用户投了那个票
                'poll_answer'          => false,
                //频道消息
                'channel_post'         => false,
                //编辑过的普通消息
                'edited_message'       => false,
                //编辑过的频道消息
                'edited_channel_post'  => false,
                //内联查询
                'inline_query'         => false,
                //选择的内联结果
                'chosen_inline_result' => false,
                //运输查询（用于购物）
                'shipping_query'       => false,
                //预检查查询（用于购物）
                'pre_checkout_query'   => false
            ], $type['msgType'] ?? []);
            $updates = [];
            foreach ($msgType as $k => $v) {
                if (is_numeric($k)) {
                    $updates[] = $v;
                } elseif ($v) {
                    $updates[] = $k;
                }
            }
            if (!empty($item['pull'] ?? null)) {
                // 第一次启动时删除更新
                //(!empty($start)) && $bot->deleteWebhook(true);
                $save[] = array_merge(["token" => $token, "updates" => $updates], $item);
            }
        }
        $json = json_encode($save);
        ($pull_key) && Redis::set($pull_key . "_" . $plugin . "_bot", $json);
        @file_put_contents($file, $json);
        return $save;
    }

    /**
     * 获取机器人信息
     * @param string $plugin
     * @param string $file
     * @param string $pull_key
     * @return array
     */
    public static function getBotCache(string $plugin, string $file, string $pull_key): array {
        if (!empty($pull_key)) {
            $json = (Redis::get($pull_key . "_" . $plugin . "_bot")) ?: "";
            $items = BotWay::isJson($json);
            if (!empty($items)) {
                return $items;
            }
        }
        $json = @file_get_contents($file);
        return BotWay::isJson($json);
    }
}