<?php

namespace Telegram\Bot\Demo\call;

use Exception;
use Throwable;
use AloneWebMan\RoBot\BotApi;

/**
 * 程序致命错误时回调
 */
class Error extends BotApi {
    /**
     * @param array               $post  请求post
     * @param Exception|Throwable $error 错误对像
     * @param array               $array 错误数据
     * @return void
     */
    public function main(array $post, Exception|Throwable $error, array $array = []): void {
        //dump($array);
    }
}