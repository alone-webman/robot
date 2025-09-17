<?php

namespace Telegram\Bot\Demo\api;

use AloneWebMan\RoBot\BotTask;

/**
 * 通过$this->token返回机器人按钮信息
 */
class Button extends BotTask {
    /**
     * @return array
     * @property bool   $name    按钮名称
     * @property int    $type    按钮类型 default commands web_app
     * @property string $uel     按钮连接
     */
    public function main(): array {
        return [
            "name" => "Open",
            "type" => "web_app",
            "url"  => "https://www.google.com"
        ];
    }
}