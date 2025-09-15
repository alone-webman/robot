<?php

namespace Telegram\Bot\Demo\console;

use AloneWebMan\RoBot\BotConsole;

/**
 * 设置按钮信息
 */
class ButtonSet extends BotConsole {
    /**
     * @param array $button 按钮信息
     * @param bool  $status 设置状态
     * @param array $body   返回信息
     * @return void
     */
    public function main(array $button, bool $status, array $body): void {
        // dump($status);
    }
}