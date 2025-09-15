<?php

namespace Telegram\Bot\Demo\call;

use Telegram\Bot\Demo\app\Bot;
use Telegram\Bot\Demo\app\Group;
use Telegram\Bot\Demo\app\Channel;
use AloneWebMan\RoBot\BotApi;

/**
 * 程序执行结束时回凋
 */
class End extends BotApi {
    /**
     * @param string            $type 类型 Bot|Group|Channel
     * @param array             $post 请求post
     * @param Bot|Group|Channel $app  执行对像
     * @return void
     */
    public function main(string $type, array $post, Bot|Group|Channel $app): void {
        // dump($app);
    }
}