<?php

namespace Telegram\Bot\Demo\api;

use AloneWebMan\RoBot\BotApi;

/**
 * 通过$this->token返回机器人key
 */
class Key extends BotApi {
    /**
     * @return string
     */
    public function main(): string {
        return "";
    }
}