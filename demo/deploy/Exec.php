<?php

namespace Telegram\Bot\Demo\deploy;

use AloneWebMan\RoBot\BotCall;
use AloneWebMan\RoBot\BotRequest;

/**
 * 正在处理的信息
 */
class Exec extends BotCall {
    /**
     * @param array      $post 请求post
     * @param BotRequest $req  处理后的请求对像
     * @return void
     */
    public function main(array $post, BotRequest $req) {
        //dump($req);
    }
}