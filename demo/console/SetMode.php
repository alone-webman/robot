<?php

namespace Telegram\Bot\Demo\console;

use AloneWebMan\RoBot\BotConsole;

/**
 * 设置处理模式
 */
class SetMode extends BotConsole {
    /**
     * @param int $status 1=实时,2=协程,3=队列,4=异步
     * @return void
     */
    public function main(int $status): void {
       // dump($status);
    }
}