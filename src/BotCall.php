<?php

namespace AloneWebMan\RoBot;
/**
 * api目录使用
 */
abstract class BotCall {
    // 当前插件名
    public string $plugin = "";
    // 路由token
    public string $token = "";

    /**
     * @param string $plugin 插件名
     * @param string $token  路由token
     */
    public function __construct(string $plugin, string $token = "") {
        $this->plugin = $plugin;
        $this->token = $token;
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