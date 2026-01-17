<?php

namespace AloneWebMan\RoBot\console;

use AloneWebMan\RoBot\Cmd;
use AlonePhp\Telegram\Bot;
use AloneWebMan\RoBot\BotFacade;
use AloneWebMan\RoBot\BotRequest;
use Symfony\Component\Console\Helper\Table;
use AloneWebMan\RoBot\console\trait\AddCommand;
use AloneWebMan\RoBot\console\trait\SetCommand;
use AloneWebMan\RoBot\console\trait\getCommand;
use AloneWebMan\RoBot\console\trait\CallCommand;
use AloneWebMan\RoBot\console\trait\HelpCommand;
use AloneWebMan\RoBot\console\trait\ListCommand;
use AloneWebMan\RoBot\console\trait\DevCommand;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Helper\TableSeparator;

/**
 * 命令执行入口
 */
class CommandHelper {
    use AddCommand, CallCommand, HelpCommand, ListCommand, SetCommand, getCommand, DevCommand;

    // 处理类型
    public string|null $type = null;
    // 插件名称
    public string|null $plugin = null;
    // 插件编号
    public string|int|null $optPlugin = null;
    // 机器列表
    public array|null $botArray = [];
    // 机器表单
    public array|null $botTable = [];
    // 机器编号
    public string|int|null $optBot = null;
    // 功能编号
    public string|int|null $optFun = null;
    // 机器人信息
    protected array $bot = [];
    // 发送实例
    public Bot|null $res = null;
    // 机器人标识
    public string|null $token = "";
    // 机器人验证
    public string|null $secret = "";
    // 配置
    public array $config = [];

    // 功能列表
    protected static array $callList = [
        // 系统功能
        55 => ["name" => "查看详细信息", "method" => "BotShow", "confirm" => false],
        66 => ["name" => "设置处理模式", "method" => "SetMode", "confirm" => false],
        88 => ["name" => "自定调试方法", "method" => "Debug", "confirm" => false],

        // 接收网址
        11 => ["name" => "查看接收网址", "method" => "WebShow", "confirm" => false],
        12 => ["name" => "设置接收网址", "method" => "WebSet", "confirm" => true],
        13 => ["name" => "删除接收网址", "method" => "WebDel", "confirm" => true],

        // 命令功能
        21 => ["name" => "查看命令列表", "method" => "CommandShow", "confirm" => false],
        22 => ["name" => "设置命令列表", "method" => "CommandSet", "confirm" => true],
        23 => ["name" => "删除命令列表", "method" => "CommandDel", "confirm" => true],

        // 按钮功能
        31 => ["name" => "查看按钮信息", "method" => "ButtonShow", "confirm" => false],
        32 => ["name" => "设置按钮信息", "method" => "ButtonSet", "confirm" => true],
        33 => ["name" => "删除按钮信息", "method" => "ButtonDel", "confirm" => true],
    ];

    protected string $cliFile = "webman alone:bot";

    /**
     * @return void
     */
    public static function run(): void {
        global $argv;
        $command = new CommandHelper($argv[1] ?? 'set', $argv[2] ?? '');
        $command->cliFile = $argv[0] ?? "robot";
        $command->start();
    }

    /**
     * @return false|int|void
     */
    public static function saveRobotFile() {
        $file = base_path("robot");
        if (empty(is_file($file))) {

            $content = <<<EOF
#!/usr/bin/env php
<?php
require_once __DIR__ . '/vendor/alone-webman/robot/bin/robot';
EOF;
            return @file_put_contents(base_path("robot"), $content);
        }
    }

    /**
     * @param string|null $type   处理类型
     * @param string|null $plugin 插件名称
     */
    public function __construct(string|null $type, string|null $plugin) {
        $this->type = $type;
        $this->plugin = $plugin;
    }

    /**
     * 启动
     * @return void
     */
    public function start(): void {
        switch (($this->type)) {
            case "add":
                // 添加插件
                $this->add();
                break;
            case "set":
                // 设置插件
                $this->set();
                break;
            case "list":
                // 插件列表
                $this->list();
                break;
            case "dev":
                $this->dev();
                break;
            case "help":
            default:
                // 命令帮助
                $this->help();
        }
    }

    /**
     * 回调
     * @param string $file
     * @param        ...$parameter
     * @return mixed
     */
    public function callConsole(string $file, ...$parameter): mixed {
        $className = "\\plugin\\{$this->plugin}\\console\\" . $file;
        return call_user_func_array([new $className($this->plugin, $this->token, $this), "main"], $parameter);
    }

    /**
     * @param string $file
     * @param        ...$parameter
     * @return mixed
     */
    public function callApi(string $file, ...$parameter): mixed {
        return BotFacade::callApi($this->plugin, $file, $this->token, ...$parameter);
    }

    /**
     * 机器人列表
     * @return mixed
     */
    public function callBotList(): mixed {
        return BotFacade::callBotList($this->plugin);
    }

    /**
     * @param string $text
     * @return void
     */
    public function showRed(string $text): void {
        Cmd::show([$text, "red"]);
    }

    /**
     * @param string $text
     * @return void
     */
    public function showBlue(string $text): void {
        Cmd::show([$text, "blue"]);
    }

    /**
     * @return Bot
     */
    public function res(): Bot {
        return alone_bot($this->bot['key'], false);
    }

    /**
     * 输出表格
     * @param string|callable $title  名称
     * @param array           $header 头部
     * @param array           $rows   列表
     * @return Table
     */
    public function table(string|callable $title, array $header, array $rows): Table {
        foreach ($header as &$item) {
            if (is_string($item)) {
                $item = Cmd::style("<fg=green;blob>$item</fg=green;blob>");
            }
        }
        $i = 0;
        $colspan = [];
        $count = count($rows);
        $row = [$header, new TableSeparator()];
        foreach ($rows as $val) {
            ++$i;
            $colspan[] = count($val);
            $row[] = array_values($val);
            if ($count > $i) {
                $row[] = new TableSeparator();
            }
        }
        $output = new ConsoleOutput();
        $table = new Table($output);
        $table->setHeaders(is_string($title) ? [Cmd::style("<fg=red;blob>$title</fg=red;blob>", max($colspan))] : $title(max($colspan)));
        $table->setRows($row);
        $table->render();
        return $table;
    }

    /**
     * 加载
     * @param string $text
     * @param int    $duration
     * @param int    $range
     * @return void
     */
    public function loader(string $text = "Loading", int $duration = 5, int $range = 100): void {
        $output = new ConsoleOutput();
        $startTime = time();
        $dots = array_map(fn($i) => str_repeat('.', $i), range(1, $range));
        $dotIndex = 0;
        $maxLength = 0;
        while (time() - $startTime < $duration) {
            $currentText = "$text {$dots[$dotIndex]}";
            $maxLength = max($maxLength, strlen($currentText));
            $output->write("\r{$currentText}");
            $dotIndex = ($dotIndex + 1) % count($dots);
            usleep(300000);
        }
        $output->write("\r" . str_repeat(' ', $maxLength) . "\r");
    }

    /**
     * 生成接收网址
     * @return string
     */
    public function getWebRoute(): string {
        $domain = $this->bot["domain"] ?? "";
        $domain = !empty($domain) ? $domain : ($this->config["app_domain"] ?? "");
        return trim($domain, '/') . "/" . trim($this->config['app_router'], '/') . "/" . $this->token;
    }

    /**
     * @return array
     */
    public function allowedUpdate(): array {
        $arr = $this->callApi("Type");
        $allowed = [];
        $msgType = $arr["msgType"] ?? "";
        foreach ($msgType as $k => $v) {
            if (is_numeric($k)) {
                $allowed[] = $v;
            } elseif ($v) {
                $allowed[] = $k;
            }
        }
        $updates = [];
        foreach ($allowed as $update) {
            $updates[] = BotRequest::$msgTypeList[$update] ?? $update;
        }
        return ['list' => $allowed, 'name' => $updates];
    }
}