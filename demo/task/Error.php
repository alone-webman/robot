<?php

namespace Telegram\Bot\Demo\task;

use Exception;
use Throwable;
use AloneWebMan\RoBot\BotTask;

/**
 * 程序致命错误时回调
 */
class Error extends BotTask {
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