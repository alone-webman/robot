<?php

namespace Telegram\Bot\Demo\console;

use AloneWebMan\RoBot\BotConsole;

/**
 * 自定调试方法
 */
class Debug extends BotConsole {
    /**
     * @return void
     */
    public function main(): void {
        $this->command->showRed("debug");
    }
}