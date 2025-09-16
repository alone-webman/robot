<?php

namespace AloneWebMan\RoBot;

use Exception;
use AlonePhp\Telegram\Bot;

/**
 * app目录使用
 */
abstract class BotApp {
    // 当前插件名
    public string $plugin = "";
    // 路由token
    public string $token = "";
    // 请求信息
    public BotRequest|null $req = null;
    // 原样post
    public array $post = [];
    // 当前主体信息
    public array $data = [];
    // 发送信息 带回复id
    public Bot|null $res = null;
    // 机器人key
    public string|int $key = '';
    // 当前 update_id
    public string|int $update_id = '';

    /**
     * 公共入口启动
     * @return $this
     */
    abstract public function start(): static;

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


    /**
     * @param BotRequest $req
     * @return $this
     * @throws Exception
     */
    public function handleMessage(BotRequest $req): static {
        $this->req = $req;
        $this->post = $this->req->post ?? [];
        $this->data = $this->req->data ?? [];
        $this->update_id = $this->req->update_id ?? '';
        $this->key = BotFacade::callDeploy($this->plugin, "Key", $this->token);
        if (empty($this->key)) {
            throw new Exception("plugin/{$this->plugin}/deploy/Key.php --- Cannot be empty");
        }
        $this->res = $this->chat();
        call_user_func([$this, 'start']);
        return $this;
    }

    /**
     * 发信息带id
     * @return Bot
     */
    public function chat(): Bot {
        return $this->bot()->chat_id($this->req->chat_id)->query_id($this->req->query_id)->message_id($this->req->msg_id);
    }

    /**
     * 机器人实例
     * @return Bot
     */
    public function bot(): Bot {
        return alone_bot($this->key);
    }

    /**
     * 获取post
     * @param string|int|null $key
     * @param mixed|null      $default
     * @return mixed
     */
    public function getPost(string|null|int $key = null, mixed $default = null): mixed {
        return BotWay::getArr($this->post, $key, $default);
    }

    /**
     * 获取主体信息
     * @param string|int|null $key
     * @param mixed|null      $default
     * @return mixed
     */
    public function getData(string|null|int $key = null, mixed $default = null): mixed {
        return BotWay::getArr($this->data, $key, $default);
    }
}