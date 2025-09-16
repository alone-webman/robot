<?php

namespace Telegram\Bot\Demo\deploy;

use AloneWebMan\RoBot\BotCall;

/**
 * 通过$this->token返回机器人处理方式
 */
class Mode extends BotCall {
    /**
     * 1=实时,2=协程,3=队列,4=异步
     * @return int
     */
    public function main(): int {
        return 2;
    }
}