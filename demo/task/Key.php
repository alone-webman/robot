<?php

namespace Telegram\Bot\Demo\task;

use AloneWebMan\RoBot\BotTask;

/**
 * 通过$this->token返回机器人key
 */
class Key extends BotTask {
    /**
     * @return string
     */
    public function main(): string {
        return "";
    }
}