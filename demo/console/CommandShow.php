<?php

namespace Telegram\Bot\Demo\console;

use AloneWebMan\RoBot\BotConsole;

/**
 * 查看命令列表
 */
class CommandShow extends BotConsole {
    /**
     * @param array $command 命令列表
     * @return void
     */
    public function main(array $command): void {
        // dump($command);
    }
}