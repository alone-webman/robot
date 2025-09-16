<?php

namespace AloneWebMan\RoBot;
/**
 * 扩展类
 */
class BotFacade {
    // 配置
    protected static array $config = [];

    /**
     * 获取配置
     * @param string          $plugin
     * @param string|int|null $key
     * @param mixed           $default
     * @return mixed
     */
    public static function getConfig(string $plugin, string|int|null $key = null, mixed $default = null): mixed {
        $config = BotFacade::config($plugin);
        return isset($key) ? ($config[$key] ?? $default) : $config;
    }

    /**
     * 获取配置
     * @param string $plugin 插件名
     * @return array
     */
    public static function config(string $plugin): array {
        if (empty(isset(static::$config[$plugin]))) {
            $confFile = __DIR__ . '/../config.php';
            $conf = is_file($confFile) ? include $confFile : [];
            $configFile = run_path('plugin/' . $plugin . '/config/telegram.php');
            $config = is_file($configFile) ? include $configFile : [];
            static::$config[$plugin] = array_merge($conf, $config);
        }
        return static::$config[$plugin] ?? [];
    }

    /**
     * 执行App
     * @param string $plugin     插件名
     * @param string $name       文件名
     * @param string $routeToken 路由token
     * @param string $method     方法名
     * @param        ...$parameter
     * @return mixed
     */
    public static function callApp(string $plugin, string $name, string $method, string $routeToken = "", ...$parameter): mixed {
        $className = "\\plugin\\{$plugin}\\app\\" . $name;
        $app = new $className($plugin, $routeToken);
        call_user_func_array([$app, $method], $parameter);
        return $app;
    }

    /**
     * 执行Message
     * @param string $plugin     插件名
     * @param string $file       方法名
     * @param string $routeToken 路由token
     * @param        ...$parameter
     * @return mixed
     */
    public static function callDeploy(string $plugin, string $file, string $routeToken = "", ...$parameter): mixed {
        $className = "\\plugin\\{$plugin}\\deploy\\" . ucfirst($file);
        return call_user_func_array([new $className($plugin, $routeToken), "main"], $parameter);
    }
}