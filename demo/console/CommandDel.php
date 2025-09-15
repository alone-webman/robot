<?php

namespace Telegram\Bot\Demo\console;

use AloneWebMan\RoBot\BotConsole;

/**
 * 删除命令列表
 */
class CommandDel extends BotConsole {
    /**
     * @param array $command 命令列表
     * @param bool  $status  设置状态
     * @param array $body    返回信息
     * @return void
     */
    public function main(array $command, bool $status, array $body): void {
        // dump($status);
    }
}