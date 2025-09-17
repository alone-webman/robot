<?php

namespace Telegram\Bot\Demo\api;

use AloneWebMan\RoBot\BotTask;

/**
 * 通过$this->token返回机器人命令列表
 */
class Command extends BotTask {
    /**
     * ["命令标识"=>"命令名称"]
     * @return array
     */
    public function main(): array {
        return [
            "start" => "启动机器"
        ];
    }
}