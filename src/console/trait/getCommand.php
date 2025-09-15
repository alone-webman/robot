<?php

namespace AloneWebMan\RoBot\console\trait;
/**
 * 显示信息
 */
trait getCommand {
    /**
     * 设置接收网址
     * @return array
     */
    protected function getWebSet(): array {
        return [["设置网址", $this->getWebRoute()]];
    }

    /**
     * 设置命令列表
     * @return array
     */
    protected function getCommandSet(): array {
        $command = $this->callExec('Command');
        $array[] = ["<fg=green;blob>标识</fg=green;blob>", "<fg=green;blob>名称</fg=green;blob>"];
        foreach ($command as $key => $value) {
            $array[] = [$key, $value];
        }
        return $array;
    }
}