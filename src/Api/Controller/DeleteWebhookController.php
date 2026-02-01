<?php

/*
 * This file is part of jiushutech/flarum-dingtalk-webhook.
 *
 * Copyright (c) JiushuTech.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JiushuTech\DingTalkWebhook\Api\Controller;

use Flarum\Api\Controller\AbstractDeleteController;
use Flarum\Http\RequestUtil;
use Illuminate\Support\Arr;
use JiushuTech\DingTalkWebhook\Models\Webhook;
use Psr\Http\Message\ServerRequestInterface;

class DeleteWebhookController extends AbstractDeleteController
{
    /**
     * {@inheritdoc}
     */
    protected function delete(ServerRequestInterface $request)
    {
        $actor = RequestUtil::getActor($request);

        $actor->assertAdmin();

        $id = Arr::get($request->getQueryParams(), 'id');

        $webhook = Webhook::findOrFail($id);
        $webhook->delete();
    }
}
