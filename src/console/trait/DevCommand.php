<?php

namespace AloneWebMan\RoBot\console\trait;

use AloneWebMan\RoBot\BotCommand;

trait DevCommand {
    /**
     * @return void
     */
    protected function dev(): void {
        if (empty($this->plugin)) {
            $res = BotCommand::botList();
            if (is_string($res)) {
                $this->help();
                $this->showRed($res);
                return;
            }
            $table = $res['table'];
            if (count($table) == 1) {
                $this->plugin = $table[key($table)]['plugin'] ?? '';
            }
        }
        $file = run_path("plugin/$this->plugin/api/Bot.php");
        if (empty(is_file($file))) {
            $this->list($res ?? BotCommand::botList());
            $this->showRed("$this->plugin 插件名称不正确");
            return;
        }
        $this->callConsole("Debug");
    }
}