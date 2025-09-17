<?php
/*
 * 默认配置
 */
return [
    /*
     * ==========================路由设置==========================
     */
    // 程序域名,设置机器人接收使用
    "app_domain"    => "",
    // 接收路由: 开头/{token}
    "app_router"    => "",
    // 通过路由接收信息时是否验证头部token
    "app_token"     => false,
    //token加密的md5 key
    "app_key"       => "",
    /*
     * ==========================开发设置==========================
     */
    // 是否开发
    "dev_status"    => false,
    // 1=实时,2=协程,3=队列,4=异步
    "dev_mode"      => 2,
    // 机器人Key Token
    "dev_key"       => "",
    // 聊天id (接收报错)
    "dev_chat"      => "",
    /*
     * ==========================拉取设置==========================
     */
    // 拉取自定义进程开关
    "pull_status"   => false,
    // 拉取进程数量
    "pull_count"    => 20,
    // 处理类,null为默认
    "pull_handler"  => null,
    // 拉取定时器
    "pull_timer"    => 0.1,
    // 多信检查一次数据
    "pull_time"     => 60,
    // 空闲进程等待时间
    "pull_wait"     => 30,
    // redis保存key名称
    "pull_key"      => "",

    /*
     * ==========================队列设置==========================
     */
    // 队列自定义进程开关
    "queue_status"  => false,
    // 队列进程数量
    "queue_count"   => 10,
    // 队列定时器
    "queue_timer"   => 0.2,
    // 队列任务数
    "queue_task"    => 3,
    // 处理类,null为默认
    "queue_handler" => null,
    // 队列redis,key名称
    "queue_key"     => "",

    /*
     * ==========================异步设置==========================
     */
    // 异步自定义进程开关
    "async_status"  => false,
    // 启动异步ip端口
    "async_listen"  => "",
    // 异步进程数量
    "async_count"   => 10,
    // 处理类,null为默认
    "async_handler" => null,
    // 连接异步ip端口
    "async_connect" => ""
];