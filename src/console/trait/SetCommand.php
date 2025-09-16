<?php

namespace AloneWebMan\RoBot\console\trait;

use AloneWebMan\RoBot\Cmd;
use AloneWebMan\RoBot\BotWay;
use AloneWebMan\RoBot\BotFacade;
use AloneWebMan\RoBot\BotCommand;
use Symfony\Component\Console\Helper\TableCellStyle;

trait SetCommand {

    /**
     * 操作
     * @param string $text
     * @return void
     */
    protected function set(string $text = ""): void {
        if (empty($this->plugin)) {
            $res = BotCommand::botList();
            if (is_string($res)) {
                $this->showRed($res);
                return;
            }
            $table = $res['table'];
            if (count($table) == 1) {
                $this->plugin = $table[key($table)]['plugin'] ?? '';
            } else {
                $this->list($res);
                ($text) && $this->showRed($text);
                $this->optPlugin = Cmd::input("请输入插件编号: ", function($value) use ($res, $table) {
                    if (empty($value)) {
                        $this->list($res);
                        return "请输入插件编号";
                    }
                    if (empty(isset($table[$value]))) {
                        $this->list($res);
                        return "插件编号不正确";
                    }
                    return null;
                });
                $this->plugin = $table[$this->optPlugin]['plugin'] ?? '';
            }
        }
        if (empty($this->plugin)) {
            $this->list($res ?? BotCommand::botList());
            $this->showRed("请输入插件名称 alone:bot set [插件名称]");
            return;
        }
        $file = run_path("plugin/$this->plugin/deploy/Bot.php");
        if (empty(is_file($file))) {
            $this->list($res ?? BotCommand::botList());
            $this->showRed("$this->plugin 插件名称不正确");
            return;
        }
        // 机器人表单
        $this->botTable();
        // 选择机器人
        $this->botOpt();
    }

    /**
     * 2-机器人表单
     * @return void
     */
    protected function botTable(): void {
        $this->botArray = $this->callDeploy("Bot");
        if (count($this->botArray) == 0) {
            $this->plugin = null;
            $this->set("$this->plugin 插件没有机器人列表没有数据");
            return;
        }
        $j = 0;
        $botArray = [];
        $mode = [1 => "实时", 2 => "协程", 3 => "队列", 4 => "异步"];
        foreach ($this->botArray as $value) {
            $j++;
            $config = BotFacade::config($this->plugin);
            $this->botTable[$j] = [
                $j,
                $value["name"],
                ($value["pull"] ? "进程" : "网页"),
                $mode[$value["mode"]],
                $value["key"],
                ($value["domain"] ?: ($config['app_domain'] ?? '')) ?: "null",
                ($value["content"] ?? "null") ?: "null"
            ];
            $botArray[$j] = $value;
        }
        $this->botArray = $botArray;
        $this->table("插件 ($this->plugin) 机器人列表", ["编号", "名称", "接收", "模式", "密钥", "域名", "备注"], $this->botTable);
    }

    /**
     * 3-选择机器人
     * @return void
     */
    protected function botOpt(): void {
        $this->optBot = Cmd::input("请输入机器人编号: ", function($value) {
            if (empty($value)) {
                return null;
            }
            if (!isset($this->botArray[$value])) {
                // 显示机器人列表
                $this->botTable();
                return "输入机器人编号不正确";
            }
            return null;
        });
        if (empty($this->optBot)) {
            $this->plugin = null;
            $this->set();
            return;
        }
        // 功能表单
        $this->botFunTable();
        // 选择功能
        $this->botFunOpt();
    }

    /**
     * 4-功能表单
     * @return void
     */
    protected function botFunTable(): void {
        $item = $this->botArray[$this->optBot] ?? [];
        if (empty($item)) {
            $this->showRed("没有获取到机器人数据");
            return;
        }
        $count = 0;
        $arr = [];
        $array = [];
        foreach (static::$callList as $key => $val) {
            $arr[] = Cmd::style($key);
            $arr[] = $val["name"];
            ++$count;
            if ($count % 3 == 0) {
                $array[] = $arr;
                $arr = [];
            }
        }
        $align = new TableCellStyle(["align" => "center"]);
        $header = Cmd::style("<fg=green;blob>功能编号 - 说明</fg=green;blob>", 2, ['style' => $align]);
        $this->table("插件($this->plugin)---机器人(" . $item["name"] . ")---功能列表", [$header, $header, $header], $array);
    }

    /**
     * 5-选择功能
     * @return void
     */
    protected function botFunOpt(): void {
        $this->optFun = Cmd::input("请输入功能编号: ", function($value) {
            if (empty($value)) {
                return null;
            }
            if (empty(in_array($value, array_keys(static::$callList)))) {
                // 显示功能列表
                $this->botFunTable();
                return "输入功能编号不正确";
            }
            return null;
        });
        if (empty($this->optFun)) {
            // 为空回退到选择机器人
            $this->botTable();
            $this->botOpt();
            return;
        }
        $this->execCall();
    }

    /**
     * 6-确认
     * @return void
     */
    protected function execCall(): void {
        $this->bot = $this->botArray[$this->optBot] ?? [];
        $this->res = alone_bot($this->bot['key']);
        $this->config = BotFacade::config($this->plugin);
        $this->token = BotWay::getBotRouteToken($this->bot["key"], $this->config['app_key']);
        $this->secret = BotWay::getBotHeaderToken($this->token, $this->config['app_key']);
        $fun = static::$callList[$this->optFun];
        $method = "get" . $fun["method"];
        $methodArray = method_exists($this, $method) ? call_user_func([$this, $method]) : [];
        $methodArray = array_merge([
            ["插件名称", $this->plugin],
            ["机器名称", $this->bot['name']],
            ["功能名称", $fun["name"]]
        ], $methodArray);
        if (!empty($fun["confirm"])) {
            $this->table("操作确认信息", ["类型", "内容"], $methodArray);
            $opt = Cmd::input("确认操作吗?(y|Y): ");
            if (strtoupper($opt) == 'Y') {
                $this->call();
                return;
            }
            $this->botFunTable();
            $this->botFunOpt();
        } else {
            $this->call();
        }
    }

    /**
     * 7-执行
     * @return void
     */
    protected function call(): void {
        $fun = static::$callList[$this->optFun];
        $method = $fun["method"];
        $this->showBlue("====================================" . $fun["name"] . "[开始]====================================");
        $res = call_user_func([$this, static::$callList[$this->optFun]["method"]]);
        if (isset($res)) {
            $this->callConsole($method, ...$res);
        }
        $this->showBlue("====================================" . $fun["name"] . "[结束]====================================");
        $opt = Cmd::input("返回功能列表?(y|Y): ");
        if (strtoupper($opt) == 'Y') {
            // 功能表单
            $this->botFunTable();
            // 选择功能
            $this->botFunOpt();
        }
        $this->resFun();
    }

    /**
     * @return void
     */
    protected function resFun(): void {
        $opt = Cmd::input("返回功能列表?(y|Y): ");
        if (strtoupper($opt) == 'Y') {
            // 功能表单
            $this->botFunTable();
            // 选择功能
            $this->botFunOpt();
        } else {
            $this->resFun();
        }
    }
}