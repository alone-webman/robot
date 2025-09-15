<?php

namespace Telegram\Bot\Demo\call;

use AloneWebMan\RoBot\BotCall;

/**
 * 通过$this->token返回机器人key
 */
class Key extends BotCall {
    /**
     * @return string
     */
    public function main(): string {
        return "";
    }
}