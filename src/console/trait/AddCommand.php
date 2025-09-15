<?php

namespace AloneWebMan\RoBot\console\trait;

use AloneWebMan\RoBot\BotWay;

trait AddCommand {
    /**
     * @return void
     */
    protected function add(): void {
        if (empty($this->plugin)) {
            $this->showRed("请输入插件名称");
            return;
        }
        /*
        if (is_dir(base_path("plugin/$this->plugin"))) {
            $this->showRed("插件目录已存在");
            return;
        }
        */
        $list = [];
        $port = rand(1, 3) . rand(2, 9) . rand(001, 999);
        $app_key = md5($this->plugin . time() . $port);
        $pathList = ["call", "app", "config", "console"];
        foreach ($pathList as $path) {
            $path = base_path("plugin/$this->plugin/$path");
            BotWay::mkDir($path);
        }
        $api = glob(__DIR__ . "/../../../demo/call/**");
        foreach ($api as $files) {
            $list["plugin/$this->plugin/call/" . basename($files)] = $this->savePluginFile('api', $files);
        }
        $app = glob(__DIR__ . "/../../../demo/app/**");
        foreach ($app as $files) {
            $list["plugin/$this->plugin/app/" . basename($files)] = $this->savePluginFile('app', $files);
        }
        $app = glob(__DIR__ . "/../../../demo/config/**");
        foreach ($app as $files) {
            $list["plugin/$this->plugin/config/" . basename($files)] = $this->savePluginFile('config', $files, function($body, $files, $name) use ($app_key, $port) {
                return BotWay::tag($body, [
                    "plugin"  => $this->plugin,
                    "app_key" => $app_key,
                    "port"    => $port
                ]);
            });
        }
        $app = glob(__DIR__ . "/../../../demo/console/**");
        foreach ($app as $files) {
            $list["plugin/$this->plugin/console/" . basename($files)] = $this->savePluginFile('console', $files);
        }
        $list["count"] = count($list);
        print_r($list);
        $this->showBlue("plugin/$this->plugin 创建成功");
    }

    /**
     * @param string        $name
     * @param string        $files
     * @param callable|null $callback
     * @return false|int
     */
    protected function savePluginFile(string $name, string $files, callable|null $callback = null): bool|int {
        $body = @file_get_contents($files);
        $body = str_replace("Telegram\Bot\Demo", "plugin\\" . $this->plugin, $body);
        $body = !empty($callback) ? $callback($body, $files, $name) : $body;
        $file = "plugin/$this->plugin/$name/" . basename($files);
        return @file_put_contents(base_path($file), $body);
    }
}