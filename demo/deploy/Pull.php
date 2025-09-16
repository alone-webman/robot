<?php

namespace Telegram\Bot\Demo\deploy;

use AloneWebMan\RoBot\BotCall;

/**
 * 主动拉取回调
 */
class Pull extends BotCall {
    /**
     * @param mixed $status 状态
     * @param array $result 主体信息
     * @param array $array  完成返回信息
     * @return void
     */
    public function main(mixed $status, array $result, array $array) {
        // dump($result);
    }
}