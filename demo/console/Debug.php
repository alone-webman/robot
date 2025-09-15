<?php

namespace Telegram\Bot\Demo\console;

use AlonePhp\Telegram\Bot;
use AloneWebMan\RoBot\BotConsole;

/**
 * 自定调试方法
 */
class Debug extends BotConsole {
    /**
     * @param Bot $bot
     * @return void
     */
    public function main(Bot $bot): void {
        $this->command->showRed("debug");
    }
}