<?php

namespace AloneWebMan\RoBot;

use AloneWebMan\RoBot\command\BotPluginCommand;

class BotCommand {
    /**
     * 命令启动
     * @return array
     */
    public static function start(): array {
        return [
            BotPluginCommand::class
        ];
    }

    /**
     * 插件列表
     * @return array|string
     */
    public static function botList(): array|string {
        $j = 0;
        $arr = [];
        $list = glob(base_path() . '/plugin/*/config');
        foreach ($list as $file) {
            $plugin = basename(dirname($file));
            $file = rtrim($file, DIRECTORY_SEPARATOR) . "/telegram.php";
            if (is_file($file)) {
                $config = BotFacade::config($plugin);
                $j++;
                $array = [
                    'number' => $j,
                    'plugin' => $plugin,
                    'count'  => count(BotFacade::callTask($plugin, "Bot")),
                    'router' => $config["app_router"] ?: "null",
                    'domain' => $config["app_domain"] ?: "null",
                    'key'    => $config["app_key"] ?: "null",
                ];
                $arr[$j] = $array;
            }
        }
        return !empty($arr) ? ["table" => $arr, "head" => ["编号", "名称", "数量 ", "路由", "域名", "密钥"]] : "没有插件";
    }
}