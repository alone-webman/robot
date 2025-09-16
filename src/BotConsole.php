<?php

namespace AloneWebMan\RoBot;

use AloneWebMan\RoBot\console\CommandHelper;

/**
 * console目录使用
 */
class BotConsole {
    // 当前插件名
    public string $plugin = "";
    // 路由token
    public string|null $token = "";
    // 路由token
    public CommandHelper|null $command = null;

    /**
     * @param string      $plugin 插件名
     * @param string|null $token  路由token
     */
    public function __construct(string $plugin, string|null $token = "", CommandHelper|null $command = null) {
        $this->plugin = $plugin;
        $this->token = $token;
        $this->command = $command;
    }

    /**
     * 获取配置
     * @param string|null $key
     * @param mixed       $default
     * @return mixed
     */
    public function getConfig(string|null $key = null, mixed $default = null): mixed {
        return BotFacade::getConfig($this->plugin, $key, $default);
    }
}