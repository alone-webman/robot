<?php

namespace Telegram\Bot\Demo\api;

use support\Response;
use AloneWebMan\RoBot\BotTask;
use Webman\Http\Response as Res;

/**
 * 路由入口信息回调
 */
class Route extends BotTask {
    /**
     * @param array $post 收到的post
     * @return false|null|Response|Res  false|null=正常执行,Res=输出浏览器材
     */
    public function main(array $post): false|null|Response|Res {
        // dump($post);
        return false;
    }
}