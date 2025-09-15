<?php

namespace Telegram\Bot\Demo\console;

use AloneWebMan\RoBot\BotConsole;

/**
 * 设置接收网址
 */
class WebSet extends BotConsole {
    /**
     * 设置成功后,机器人不能自动拉取信息了
     * @param string $url    接收网址
     * @param bool   $status 设置状态
     * @param array  $body   设置返回的body
     * @return void
     */
    public function main(string $url, bool $status, array $body): void {
       // dump($body);
    }
}