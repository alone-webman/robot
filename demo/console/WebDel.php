<?php

namespace Telegram\Bot\Demo\console;

use AloneWebMan\RoBot\BotConsole;

/**
 * 删除接收网址
 */
class WebDel extends BotConsole {
    /**
     * 删除成功后,机器人要自动拉取信息
     * @param bool  $status 删除状态
     * @param array $body   删除返回的body
     * @return void
     */
    public function main(bool $status, array $body): void {
       // dump($body);
    }
}