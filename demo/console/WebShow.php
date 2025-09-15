<?php

namespace Telegram\Bot\Demo\console;

use AloneWebMan\RoBot\BotConsole;

/**
 * 查看接收网址
 */
class WebShow extends BotConsole {

    /**
     * @param bool   $status  true=网址已设置,false=网址未设置
     * @param string $url     接收网址
     * @param string $secret  验证头部 空时没有开启设置
     * @param array  $allowed 信息类型
     * @return void
     */
    public function main(bool $status, string $url, string $secret, array $allowed): void {
        //dump($url);
    }
}