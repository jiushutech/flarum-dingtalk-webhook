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

use Flarum\Api\Controller\AbstractListController;
use Flarum\Http\RequestUtil;
use JiushuTech\DingTalkWebhook\Api\Serializer\WebhookSerializer;
use JiushuTech\DingTalkWebhook\Models\Webhook;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class ListWebhooksController extends AbstractListController
{
    /**
     * {@inheritdoc}
     */
    public $serializer = WebhookSerializer::class;

    /**
     * {@inheritdoc}
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);

        $actor->assertAdmin();

        return Webhook::all();
    }
}
