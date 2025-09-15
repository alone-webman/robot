<?php

namespace Telegram\Bot\Demo\call;

use AloneWebMan\RoBot\BotCall;

/**
 * 返回机器人列表(二维数组)
 */
class Bot extends BotCall {
    /**
     * @return array 二维数组
     * @property bool   $pull    true=主动拉取,false=网页接收
     * @property int    $mode    1=实时,2=协程,3=队列,4=异步
     * @property string $name    机器人名称
     * @property string $key     机器人Key Token
     * @property string $domain  设置的域名,不设置默认使用配置文件中的域名
     * @property string $content 备注
     */
    public function main(): array {
        return [
            [
                // true=主动拉取,false=网页接收
                "pull"    => false,
                // 1=实时,2=协程,3=队列,4=异步
                "mode"    => 2,
                // 机器人名称
                "name"    => "",
                // 机器人Key Token
                "key"     => "",
                // 设置的域名
                "domain"  => "",
                // 备注
                "content" => ""
            ]
        ];
    }
}