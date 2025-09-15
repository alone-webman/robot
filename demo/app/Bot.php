<?php

namespace Telegram\Bot\Demo\app;


use AloneWebMan\RoBot\BotWay;

/**
 * 机器人信息
 */
class Bot extends Common {
    /**
     * 入口方法
     * @return void
     */
    public function main(): void {
        $this->chat()->sendMessage(BotWay::json($this->post));
    }
}