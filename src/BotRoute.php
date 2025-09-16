<?php

namespace AloneWebMan\RoBot;

use Exception;
use Throwable;
use Webman\Route;
use support\Request;
use Workerman\Coroutine;
use Workerman\Connection\AsyncTcpConnection;

/**
 * 接收路由类
 */
class BotRoute {
    /**
     * 路由启动
     * @param string $plugin
     * @return void
     */
    public static function start(string $plugin): void {
        $config = BotFacade::config($plugin);
        $app_router = $config['app_router'] ?? null;
        if (empty($app_router)) {
            return;
        }
        $router = trim(str_replace('\\', '/', $app_router), '/');
        Route::post('/' . $router . '[/{token}]', function(Request $req, mixed $token = '') use ($plugin, $config) {
            $post = $req->post();
            $res = BotFacade::callDeploy($plugin, "Route", $token, $post);
            if (!empty($res)) {
                return $res;
            }
            if (empty($token)) {
                return response("error.token");
            }
            if (empty($post)) {
                return response("error.post");
            }
            if (!empty($config['app_token'] ?? null)) {
                $md5_key = $config['app_key'] ?? '';
                $secret = request()->header('x-telegram-bot-api-secret-token', "");
                if ((empty($secret) || $secret != BotWay::getBotHeaderToken($token, $md5_key))) {
                    return response("error.secret");
                }
            }
            static::routeHandle($plugin, $token, $post);
            return response("success");
        });
    }

    /**
     * 1=实时,2=协程,3=队列,4=异步
     * @param string $plugin 插件名
     * @param string $token  路由token
     * @param array  $post
     * @return void
     */
    public static function routeHandle(string $plugin, string $token, array $post): void {
        $mode = (int) BotFacade::callDeploy($plugin, "Mode", $token);
        switch ($mode) {
            case 1:
                // 实时
                static::exec($plugin, $token, $post);
                break;
            case 3:
                // 队列
                static::queue($plugin, $token, $post);
                break;
            case 4:
                // 异步
                static::async($plugin, $token, $post);
                break;
            default:
                // 协程
                static::coroutine($plugin, $token, $post);
                break;
        }
    }

    /**
     * 异步
     * @param string $plugin 插件名
     * @param string $token  路由token
     * @param array  $post
     * @return void
     */
    public static function async(string $plugin, string $token, array $post): void {
        $config = BotFacade::config($plugin);
        if ($config['async_status'] && $config['async_connect']) {
            $async = new AsyncTcpConnection($config['async_connect']);
            $async->onConnect = function(AsyncTcpConnection $connection) use ($plugin, $token, $post) {
                $connection->send(json_encode(['plugin' => $plugin, 'token' => $token, 'post' => $post]));
                $connection->close();
            };
            $async->connect();
        } else {
            static::coroutine($plugin, $token, $post);
        }
    }

    /**
     * 队列
     * @param string $plugin 插件名
     * @param string $token  路由token
     * @param array  $post
     * @return void
     */
    public static function queue(string $plugin, string $token, array $post): void {
        $config = BotFacade::config($plugin);
        if ($config['queue_status'] && $config['queue_key']) {
            alone_redis_set($config['queue_key'] . "_" . $plugin . "_list", ['plugin' => $plugin, 'token' => $token, 'post' => $post]);
        } else {
            static::coroutine($plugin, $token, $post);
        }
    }

    /**
     * 协程
     * @param string $plugin 插件名
     * @param string $token  路由token
     * @param array  $post
     * @return void
     */
    public static function coroutine(string $plugin, string $token, array $post): void {
        Coroutine::create(fn() => static::exec($plugin, $token, $post));
    }

    /**
     * 实时
     * @param string $plugin 插件名
     * @param string $token  路由token
     * @param array  $post
     * @return void
     */
    public static function exec(string $plugin, string $token, array $post): void {
        try {
            $type = BotFacade::callDeploy($plugin, "Type", $token);
            $req = new BotRequest($post, $type['msgType'] ?? [], $type['msgClass'] ?? []);
            $req->handle();
            BotFacade::callDeploy($plugin, "Exec", $token, $post, $req);
            if (!empty($req->allow)) {
                switch ($req->chat_type) {
                    case 'bot':
                        // 机器人信息
                        $app = BotFacade::callApp($plugin, "Bot", "handleMessage", $token, $req);
                        // 执行完成回调
                        BotFacade::callDeploy($plugin, "End", $token, "Bot", $post, $app);
                        break;
                    case 'group':
                        // 群组信息
                        $app = BotFacade::callApp($plugin, "Group", "handleMessage", $token, $req);
                        // 执行完成回调
                        BotFacade::callDeploy($plugin, "End", $token, "Group", $post, $app);
                        break;
                    case 'channel':
                        // 频道信息
                        $app = BotFacade::callApp($plugin, "Channel", "handleMessage", $token, $req);
                        // 执行完成回调
                        BotFacade::callDeploy($plugin, "End", $token, "Group", $post, $app);
                        break;
                }
            }
        } catch (Exception|Throwable $exception) {
            $array = [
                'code' => $exception->getCode(),
                'msg'  => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'date' => date('Y-m-d H:i:s')
            ];
            BotFacade::callDeploy($plugin, "Error", $token, $post, $exception, $array);
        }
    }
}