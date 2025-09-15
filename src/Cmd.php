<?php

namespace AloneWebMan\RoBot;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Helper\TableCellStyle;

class Cmd {
    /**
     * 输出显示
     * @param string|iterable $messages
     * @param int             $options
     * @return ConsoleOutput
     * @extends
     *             <fg=black>黑色文本<fg=black>
     *             <fg=red>红色文本</fg=red>
     *             <fg=green>绿色文本</fg=green>
     *             <fg=yellow>黄色文本</fg=yellow>
     *             <fg=blue>蓝色文本</fg=blue>
     *             <fg=magenta>洋红色文本</fg=magenta>
     *             <fg=cyan>青色文本</fg=cyan>
     *             <fg=white>白色文本</fg=white>
     *             <fg=gray>灰色文本</fg=gray>
     */
    public static function show(string|iterable $messages, int $options = 1): ConsoleOutput {
        $output = new ConsoleOutput();
        if (is_array($messages)) {
            $fg = $messages[1] ?? "red";
            $output->writeln("<fg=" . $fg . ">" . $messages[0] . "</fg=" . $fg . ">", $options);
        } else {
            $output->writeln($messages, $options);
        }
        return $output;
    }

    /**
     * 合并行
     * @param string $name
     * @param int    $number
     * @param array  $config
     * @return TableCell
     */
    public static function style(string $name, int $number = 0, array $config = []): TableCell {
        if ($number > 0) {
            $options['colspan'] = $number;
        }
        $options['style'] = new TableCellStyle(["align" => "center"]);
        return new TableCell($name, array_merge($options, $config));
    }

    /**
     * 显示表格
     * new TableCell('合并二例', ['colspan' => 2]);
     * @param array $array
     * @param array $config
     * @return Table
     */
    public static function table(array $array, array $config = []): Table {
        $config = array_merge([
            // 是否开启行符号
            "line"  => true,
            // 头部列名称
            "head"  => [],
            // 头部行显示名称
            "title" => "",
            // 底部行名称
            "foot"  => ""
        ], $config);
        $output = new ConsoleOutput();
        $table = new Table($output);
        //头部显示
        (!empty($config["title"])) && $table->setHeaderTitle($config["title"]);
        //底部显示
        (!empty($config["foot"])) && $table->setFooterTitle($config["foot"]);
        $rows = [];
        $i = 0;
        $count = count($array);
        foreach ($array as $item) {
            ++$i;
            $rows[] = array_values($item);
            if ($config['line'] && $count > $i) {
                $rows[] = new TableSeparator();
            }
        }
        $header = !empty($config["head"]) ? $config["head"] : array_keys($array[key($array)]);
        $table->setHeaders($header);
        $table->setRows($rows);
        $table->render();
        return $table;
    }

    /**
     * 命令提示输入并获取值
     * @param string        $title    显示提示
     * @param callable|null $callback 验证成,返回空通过验证
     * @param string        $def      默认值
     * @return string
     */
    public static function input(string $title, callable|null $callback = null, string $def = ""): string {
        $input = new ArgvInput();
        $output = new ConsoleOutput();
        $helper = new QuestionHelper();
        $question = new Question($title, $def);
        if (!empty($callback)) {
            $question->setValidator(function($value) use ($callback) {
                (!empty($res = $callback($value))) && throw new \RuntimeException($res);
                return $value;
            });
        }
        return $helper->ask($input, $output, $question);
    }

    /**
     * 命令提示输入并获取值
     * @param string   $title    显示提示
     * @param callable $callback 验证成,返回空通过验证
     * @param string   $def      默认值
     * @return string
     */
    public static function cmdInput(string $title, callable $callback, string $def = ""): string {
        print_r($title);
        $key = trim(fgets(STDIN));
        $key = !empty($key) ? $key : $def;
        if (!empty($res = $callback($key))) {
            print_r($res . "\r\n");
            return static::cmdInput($title, $callback, $def);
        }
        return $key;
    }
}