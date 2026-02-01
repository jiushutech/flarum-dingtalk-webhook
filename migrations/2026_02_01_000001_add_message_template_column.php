<?php

/*
 * This file is part of jiushutech/flarum-dingtalk-webhook.
 *
 * Copyright (c) JiushuTech.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        if (!$schema->hasTable('dingtalk_webhooks')) {
            return;
        }

        if ($schema->hasColumn('dingtalk_webhooks', 'message_template')) {
            return;
        }

        $schema->table('dingtalk_webhooks', function (Blueprint $table) {
            $table->text('message_template')->nullable()->after('include_tags')->comment('自定义消息模板');
        });
    },
    'down' => function (Builder $schema) {
        if (!$schema->hasTable('dingtalk_webhooks')) {
            return;
        }

        if (!$schema->hasColumn('dingtalk_webhooks', 'message_template')) {
            return;
        }

        $schema->table('dingtalk_webhooks', function (Blueprint $table) {
            $table->dropColumn('message_template');
        });
    },
];
