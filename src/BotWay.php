<?php

namespace AloneWebMan\RoBot;

use Throwable;
use Exception;
use Workerman\Timer;
use ReflectionFunction;

/**
 * 方法类
 */
class BotWay {
    /**
     * 机器人key转换成路由token
     * @param string $botToken 机器人Token
     * @param string $md5Key   md5key
     * @return string
     */
    public static function getBotRouteToken(string $botToken, string $md5Key): string {
        return md5($botToken . $md5Key);
    }

    /**
     * 路由token转换成头部token
     * @param string $routeToken 路由token
     * @param string $md5Key     md5key
     * @return string
     */
    public static function getBotHeaderToken(string $routeToken, string $md5Key): string {
        return md5($md5Key . md5($routeToken . $md5Key));
    }

    /**
     * @param mixed $json
     * @param bool  $associative
     * @param int   $depth
     * @param int   $flags
     * @return mixed
     */
    public static function isJson(mixed $json, bool $associative = true, int $depth = 512, int $flags = 0): mixed {
        $json = json_decode((is_string($json) ? ($json ?: '') : ''), $associative, $depth, $flags);
        return (($json && is_object($json)) || (is_array($json) && $json)) ? $json : [];
    }

    /**
     * 数组转Json 格式化
     * @param array|object $array
     * @param bool         $int 是否数字检查
     * @return bool|string
     */
    public static function json(array|object $array, bool $int = true): bool|string {
        return $int ? json_encode($array, JSON_NUMERIC_CHECK + JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES + JSON_PRETTY_PRINT) : json_encode($array, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES + JSON_PRETTY_PRINT);
    }

    /**
     * 文件夹不存在创建文件夹(无限级)
     * @param $dir
     * @return bool
     */
    public static function mkDir($dir): bool {
        return (!empty(is_dir($dir)) || @mkdir($dir, 0777, true));
    }

    /**
     * 替换内容
     * @param string|null $string 要替换的string
     * @param array       $array  ['key'=>'要替换的内容']
     * @param string      $symbol key前台符号
     * @return string
     */
    public static function tag(string|null $string, array $array = [], string $symbol = '%'): string {
        if (!empty($string)) {
            $array = array_combine(array_map(fn($key) => ($symbol . $key . $symbol), array_keys($array)), array_values($array));
            $result = strtr($string, $array);
            $string = trim(preg_replace("/" . $symbol . "[^" . $symbol . "]+" . $symbol . "/", '', $result));
        }
        return $string ?? '';
    }

    /**
     * 定时器
     * @param int|float $interval
     * @param callable  $callable
     * @return bool|int
     */
    public static function timer(int|float $interval, callable $callable): bool|int {
        return Timer::add($interval, function() use ($interval, $callable) {
            $callable();
            static::timer($interval, $callable);
        }, [], false);
    }

    /**
     * 自定进程中获取名称
     * @param mixed $worker
     * @return string
     */
    public static function getPluginName(mixed $worker): string {
        $staticProperties = static::getStatic($worker);
        $plugin_name = $staticProperties['config']['name'] ?? '';
        if (empty($plugin_name)) {
            $arr = explode('.', $worker->name);
            $plugin_name = $arr[1] ?? '';
        }
        return $plugin_name;
    }

    /**
     * 自定进程中获取配置
     * @param mixed $worker
     * @return array
     */
    public static function getStatic(mixed $worker): array {
        $staticProperties = [];
        if ($worker && ($worker->onWorkerStart ?? '')) {
            try {
                $reflection = new ReflectionFunction($worker->onWorkerStart);
                $staticProperties = $reflection->getStaticVariables();
            } catch (Exception|Throwable $e) {
                $staticProperties = ['code' => $e->getCode(), 'message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()];
            }
        }
        return $staticProperties;
    }

    /**
     * @param array|null      $array
     * @param string|int|null $key
     * @param mixed|null      $default
     * @param string          $symbol
     * @return mixed
     */
    public static function getArr(array|null $array, string|null|int $key = null, mixed $default = null, string $symbol = '.'): mixed {
        if (isset($key)) {
            if (isset($array[$key])) {
                $array = $array[$key];
            } else {
                $symbol = $symbol ?: '.';
                $arr = explode($symbol, trim($key, $symbol));
                foreach ($arr as $v) {
                    if (isset($v) && isset($array[$v])) {
                        $array = $array[$v];
                    } else {
                        $array = $default;
                        break;
                    }
                }
            }
        }
        return $array ?? $default;
    }
}