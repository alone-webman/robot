<?php

namespace Telegram\Bot\Demo\task;

use AloneWebMan\RoBot\BotTask;

/**
 * 通过$this->token返回允许获取的信息类型
 * @return array
 */
class Type extends BotTask {
    /**
     * @return array
     */
    public function main(): array {
        return [
            // 信息类型
            "msgType"  => [
                //普通消息
                'message'              => true,
                //回调查询（来自按钮点击）
                'callback_query'       => true,
                //匿名投票,接收投票详细
                'poll'                 => false,
                //实名投票 那个用户投了那个票
                'poll_answer'          => false,
                //频道消息
                'channel_post'         => false,
                //编辑过的普通消息
                'edited_message'       => false,
                //编辑过的频道消息
                'edited_channel_post'  => false,
                //内联查询
                'inline_query'         => false,
                //选择的内联结果
                'chosen_inline_result' => false,
                //运输查询（用于购物）
                'shipping_query'       => false,
                //预检查查询（用于购物）
                'pre_checkout_query'   => false
            ],
            // 信息分类
            "msgClass" => [
                //文本
                'text'      => true,
                //图片
                'photo'     => false,
                //视频
                'video'     => false,
                //动画
                'animation' => false,
                //音频
                'audio'     => false,
                //语音
                'voice'     => false,
                //文档
                'document'  => false
            ]
        ];
    }
}