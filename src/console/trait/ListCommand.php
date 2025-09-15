<?php

namespace AloneWebMan\RoBot\console\trait;

use AloneWebMan\RoBot\BotCommand;

trait ListCommand {
    /**
     * @param array|null $res
     * @return void
     */
    protected function list(array|null $res = null): void {
        $res = !empty($res) ? $res : BotCommand::botList();
        if (is_array($res)) {
            $this->table("插件列表", $res['head'], $res['table']);
        } else {
            $this->showRed($res);
        }
    }
}