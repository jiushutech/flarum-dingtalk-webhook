<?php

/*
 * This file is part of jiushutech/flarum-dingtalk-webhook.
 *
 * Copyright (c) JiushuTech.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Flarum\Group\Group;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        if ($schema->hasTable('dingtalk_webhooks')) {
            return;
        }

        $schema->create('dingtalk_webhooks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable()->comment('Webhook名称');
            $table->string('url', 500)->comment('钉钉机器人Webhook地址');
            $table->string('secret')->nullable()->comment('加签密钥');
            $table->text('error')->nullable()->comment('最后错误信息');
            $table->json('events')->comment('启用的事件列表');
            $table->integer('group_id')->unsigned()->default(Group::GUEST_ID)->comment('可见组群ID');
            $table->json('tag_id')->nullable()->comment('限定标签ID');
            $table->text('extra_text')->nullable()->comment('附加文本');
            $table->integer('max_post_content_length')->default(200)->comment('最大内容长度');
            $table->boolean('use_plain_text')->default(false)->comment('使用纯文本');
            $table->boolean('include_tags')->default(false)->comment('包含标签信息');
            $table->text('message_template')->nullable()->comment('自定义消息模板');
            $table->timestamps();
        });
    },
    'down' => function (Builder $schema) {
        $schema->dropIfExists('dingtalk_webhooks');
    },
];
