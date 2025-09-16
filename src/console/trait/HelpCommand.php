<?php

namespace AloneWebMan\RoBot\console\trait;

use Symfony\Component\Console\Helper\Table;

trait HelpCommand {
    /**
     * @return Table
     */
    protected function help(): Table {
        return $this->table("命令帮助说明", ["命令", "说明"],
            [
                ["php webman alone:bot list", "查看插件列表信息"],
                ["php webman alone:bot add [插件名称]", "生成插件目录代码 /plugin/插件名称"],
                ["php webman alone:bot set [插件名称]", "设置机器人命令列表"],
                ["php webman alone:bot dev [插件名称]", "调试开发"]
            ]
        );
    }
}