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

use Flarum\Api\Controller\AbstractCreateController;
use Flarum\Http\RequestUtil;
use Illuminate\Support\Arr;
use JiushuTech\DingTalkWebhook\Api\Serializer\WebhookSerializer;
use JiushuTech\DingTalkWebhook\Models\Webhook;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class CreateWebhookController extends AbstractCreateController
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

        $data = Arr::get($request->getParsedBody(), 'data.attributes', []);

        $webhook = new Webhook();
        $webhook->name = Arr::get($data, 'name');
        $webhook->url = Arr::get($data, 'url', '');
        $webhook->secret = Arr::get($data, 'secret');
        $webhook->events = Arr::get($data, 'events', []);
        $webhook->group_id = Arr::get($data, 'groupId', 2);
        $webhook->tag_id = Arr::get($data, 'tagId', []);
        $webhook->extra_text = Arr::get($data, 'extraText');
        $webhook->max_post_content_length = Arr::get($data, 'maxPostContentLength', 200);
        $webhook->use_plain_text = (bool) Arr::get($data, 'usePlainText', false);
        $webhook->include_tags = (bool) Arr::get($data, 'includeTags', false);
        $webhook->message_template = Arr::get($data, 'messageTemplate');

        $webhook->save();

        return $webhook;
    }
}
