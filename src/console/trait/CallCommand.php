<?php

namespace AloneWebMan\RoBot\console\trait;

use AloneWebMan\RoBot\Cmd;
use AloneWebMan\RoBot\BotWay;
use AloneWebMan\RoBot\BotRequest;

trait CallCommand {
    /**
     * 查看详细信息
     * @return array
     */
    protected function BotShow(): array {
        $this->loader("正在获取官方信息");
        $array = [["类型", "内容", "说明"]];
        $array[] = ["接口", $this->bot["key"], "机器人的唯一标识符"];
        $array[] = ["令牌", $this->token, "机器人令牌标识,路由使用"];
        $array[] = ["验证", BotWay::getBotHeaderToken($this->token, $this->config['app_key']), "机器人头部令牌,路由使用"];
        $hook = $this->res()->getWebhookInfo();
        if (!empty($hook->array('ok')) && !empty($result = $hook->array('result'))) {
            $updates = [];
            foreach ($result['allowed_updates'] ?? [] as $update) {
                $updates[] = BotRequest::$msgTypeList[$update] ?? $update;
            }
            $array[] = ["网址", $result['url'] ?: "未设置", "机器人接收信息网址"];
            $array[] = ["数量", $result['pending_update_count'], "未处理信息数量"];
            $array[] = ["类型", !empty($updates) ? join(",", $updates) : "全部信息", "接收信息类型"];
            if (!empty($error = $result['last_error_message'] ?? '')) {
                $array[] = ["错误", $error, "接收网址报错原因"];
            }
        }
        $me = $this->res()->getMe();
        if (!empty($me->array('ok')) && !empty($result = $me->array('result'))) {
            $array[] = ["标识", $result['id'], "机器人的唯一标识符"];
            $array[] = ["名字", $result['first_name'], "机器人名称"];
            $array[] = ["帐号", $result["username"], "机器人用户名"];
            $array[] = ["进群", $result["can_join_groups"] ? "是" : "否", "机器人可以被邀请进群"];
            $array[] = ["隐私", $result["can_read_all_group_messages"] ? "是" : "否", "机器人禁用了隐私模式"];
            $array[] = ["内联", $result["supports_inline_queries"] ? "是" : "否", "机器人支持内联查询"];
            $array[] = ["商业", $result["can_connect_to_business"] ? "是" : "否", "机器人可以连接到Telegram商业账户以接收其消息"];
            $array[] = ["应用", $result["has_main_web_app"] ? "是" : "否", "机器人有一个主Web应用"];
        }
        $this->table("查看详细信息", ["类型", "内容", "说明"], $array);
        return [$hook, $me];
    }

    /**
     * 设置处理模式
     * @param string $text
     * @return float[]|int[]|string[]|null
     */
    protected function SetMode(string $text = ""): array|null {
        $this->table("设置处理模式", ["编号", "功能"], [
            ["1", "实时"],
            ["2", "协程"],
            ["3", "队列"],
            ["4", "异步"]
        ]);
        (!empty($text)) && $this->showRed($text);
        $opt = Cmd::input("请输入模式编号: ");
        if (is_numeric($opt) && $opt > 0 && $opt <= 4) {
            return [$opt];
        }
        if (!empty($opt)) {
            $this->SetMode("请输入1-4之间");
            return null;
        }
        $this->botFunTable();
        $this->botFunOpt();
        return null;
    }

    /**
     * 自定调试方法
     * @return array
     */
    protected function Debug(): array {
        return [$this->res()];
    }

    /**
     * 查看接收网址
     * @return array
     */
    protected function WebShow(): array {
        $this->loader("正在获取官方信息");
        $array = [];
        $hook = $this->res()->getWebhookInfo();
        $result = $hook->array('result');
        $updates = [];
        $status = false;
        $url = ($result['url'] ?? "");
        if (!empty($url)) {
            $status = true;
            $array[] = ["已设网址", $url];
            $allowed = ($result['allowed_updates'] ?? []);
            if (!empty($allowed)) {
                foreach ($allowed as $update) {
                    $updates[] = BotRequest::$msgTypeList[$update] ?? $update;
                }
            }
        } else {
            $url = $this->getWebRoute();
            $array[] = ["可设网址", $url];
            $arr = $this->allowedUpdate();
            $allowed = $arr['list'];
            $updates = $arr['name'];
        }
        $array[] = ["信息类型", join(",", $updates)];
        if (!empty($this->secret)) {
            $array[] = ["验证头部", $this->secret];
        }
        $this->table("查看接收网址", ["类型", "内容"], $array);
        return [$status, $url, $this->secret, $allowed];
    }

    /**
     * 设置接收网址
     * @return array|null
     */
    protected function WebSet(): array|null {
        $url = $this->getWebRoute();
        if (!str_starts_with($url, 'http')) {
            $this->showBlue("接收网址不正确 :" . $url);
            return null;
        }
        $arr = $this->allowedUpdate();
        $allowed = $arr['list'];
        $updates = $arr['name'];
        $this->res()->deleteWebhook(true);
        $res = $this->res()->setWebhook($url, $allowed, $this->secret, ["drop_pending_updates" => true]);
        $array[] = ["网址", $url];
        $array[] = ["验证", $this->secret];
        $array[] = ["类型", join(",", $updates)];
        $array[] = ["状态", $res->array('ok') ? "成功" : "失败"];
        $array[] = ["说明", $res->array('description')];
        $this->table("设置接收网址", ["类型", "说明"], $array);
        return [$url, !empty($res->array('ok')), $res->array()];
    }

    /**
     * 删除接收网址
     * @return array
     */
    protected function WebDel(): array {
        $res = $this->res()->deleteWebhook(true);
        $array[] = ["状态", $res->array('ok') ? "成功" : "失败"];
        $array[] = ["说明", $res->array('description')];
        $this->table("删除接收网址", ["类型", "说明"], $array);
        return [!empty($res->array('ok')), $res->array()];
    }

    /**
     * 查看命令列表
     * @return array
     */
    protected function CommandShow(): array {
        $command = $this->callExec('Command');
        $array = [];
        foreach ($command as $k => $v) {
            $array[] = [$k, $v];
        }
        $this->table("查看命令列表", ["命令标识", "命令名称"], $array);
        return [$command];
    }

    /**
     * 设置命令列表
     * @return array
     */
    protected function CommandSet(): array {
        $command = $this->callExec('Command');
        $res = $this->res()->setMyCommands($command);
        $array = [];
        foreach ($command as $k => $v) {
            $array[] = [$k, $v];
        }
        $array[] = [Cmd::style(!empty($res->array('ok')) ? "成功" : "失败", 2)];
        $this->table("查看命令列表", ["命令标识", "命令名称"], $array);
        return [$command, !empty($res->array('ok')), $res->array()];
    }

    /**
     * 删除命令列表
     * @return array
     */
    protected function CommandDel(): array {
        $command = $this->callExec('Command');
        $res = $this->res()->setMyCommands();
        $array = [];
        foreach ($command as $k => $v) {
            $array[] = [$k, $v];
        }
        $array[] = [Cmd::style(!empty($res->array('ok')) ? "成功" : "失败", 2)];
        $this->table("删除命令列表", ["命令标识", "命令名称"], $array);
        return [$command, !empty($res->array('ok')), $res->array()];
    }

    /**
     * 查看按钮信息
     * @return array
     */
    protected function ButtonShow(): array {
        $button = $this->callExec('Button');
        $this->table("查看按钮信息", ["名称", "类型", "连接"], [[$button['name'], ($button['type'] ?? 'web_app') ?: "web_app", $button['url']]]);
        return [$button];
    }

    /**
     * 设置按钮信息
     * @return array
     */
    protected function ButtonSet(): array {
        $button = $this->callExec('Button');
        $res = $this->res()->setChatMenuButton(($button['type'] ?? 'web_app') ?: "web_app", $button['name'], $button['url']);
        $array[] = [$button['name'], ($button['type'] ?? 'web_app') ?: "web_app", $button['url']];
        $array[] = [Cmd::style(!empty($res->array('ok')) ? "成功" : "失败", 3)];
        $this->table("设置按钮信息", ["名称", "类型", "连接"], $array);
        return [$button, !empty($res->array('ok')), $res->array()];
    }

    /**
     * 删除按钮信息
     * @return array
     */
    protected function ButtonDel(): array {
        $button = $this->callExec('Button');
        $res = $this->res()->setChatMenuButton("commands");
        $array[] = [$button['name'], ($button['type'] ?? 'web_app') ?: "web_app", $button['url']];
        $array[] = [Cmd::style(!empty($res->array('ok')) ? "成功" : "失败", 3)];
        $this->table("删除按钮信息", ["名称", "类型", "连接"], $array);
        return [$button, !empty($res->array('ok')), $res->array()];
    }
}