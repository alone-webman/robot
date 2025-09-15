<?php

namespace Telegram\Bot\Demo\app;

use AloneWebMan\RoBot\BotApp;

/**
 * 公共类
 */
class Common extends BotApp {
    /**
     * 公共入口启动
     * @return $this
     */
    public function start(): static {
        call_user_func([$this, 'main']);
        return $this;
    }
}