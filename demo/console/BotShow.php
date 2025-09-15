<?php

namespace Telegram\Bot\Demo\console;

use AlonePhp\Telegram\Bot;
use AloneWebMan\RoBot\BotConsole;

/**
 * 查看详细信息
 */
class BotShow extends BotConsole {
    /**
     * @param Bot $hook WebHook https://core.telegram.org/bots/api#getwebhookinfo
     * @param Bot $me   机器人信息 https://core.telegram.org/bots/api#getme
     * @return void
     */
    public function main(Bot $hook, Bot $me): void {
        // dump($hook->array());
        // dump($me->array());
    }
}