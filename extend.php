<?php

/*
 * This file is part of jiushutech/flarum-dingtalk-webhook.
 *
 * Copyright (c) JiushuTech.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JiushuTech\DingTalkWebhook;

use Flarum\Api\Serializer\ForumSerializer;
use Flarum\Extend;
use Flarum\Frontend\Document;
use JiushuTech\DingTalkWebhook\Api\Serializer\WebhookSerializer;
use JiushuTech\DingTalkWebhook\Listener\TriggerListener;

return [
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')
        ->css(__DIR__.'/resources/less/admin.less')
        ->content(function (Document $document) {
            // 确保监听器已初始化
            if (TriggerListener::$listeners === null) {
                TriggerListener::setupDefaultListeners();
            }
            $document->payload['dingtalk-webhook.events'] = array_keys((array) TriggerListener::$listeners);
        }),

    new Extend\Locales(__DIR__.'/resources/locale'),

    (new Extend\Routes('api'))
        ->get('/dingtalk-webhooks', 'dingtalk-webhooks.index', Api\Controller\ListWebhooksController::class)
        ->post('/dingtalk-webhooks', 'dingtalk-webhooks.create', Api\Controller\CreateWebhookController::class)
        ->patch('/dingtalk-webhooks/{id}', 'dingtalk-webhooks.update', Api\Controller\UpdateWebhookController::class)
        ->delete('/dingtalk-webhooks/{id}', 'dingtalk-webhooks.delete', Api\Controller\DeleteWebhookController::class)
        ->post('/dingtalk-webhooks/{id}/test', 'dingtalk-webhooks.test', Api\Controller\TestWebhookController::class),

    (new Extend\ApiSerializer(ForumSerializer::class))
        ->hasMany('dingtalkWebhooks', WebhookSerializer::class),

    (new Extend\Event())
        ->subscribe(Listener\TriggerListener::class),

    (new Extend\Settings())
        ->default('dingtalk-webhook.debug', false),
];
