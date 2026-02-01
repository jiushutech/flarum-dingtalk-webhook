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

use Flarum\Api\Controller\AbstractShowController;
use Flarum\Http\RequestUtil;
use Illuminate\Support\Arr;
use JiushuTech\DingTalkWebhook\Api\Serializer\WebhookSerializer;
use JiushuTech\DingTalkWebhook\Models\Webhook;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class UpdateWebhookController extends AbstractShowController
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

        $id = Arr::get($request->getQueryParams(), 'id');
        $data = Arr::get($request->getParsedBody(), 'data.attributes', []);

        $webhook = Webhook::findOrFail($id);

        if (Arr::has($data, 'name')) {
            $webhook->name = Arr::get($data, 'name');
        }

        if (Arr::has($data, 'url')) {
            $webhook->url = Arr::get($data, 'url');
        }

        if (Arr::has($data, 'secret')) {
            $webhook->secret = Arr::get($data, 'secret');
        }

        if (Arr::has($data, 'events')) {
            $webhook->events = Arr::get($data, 'events');
        }

        if (Arr::has($data, 'groupId')) {
            $webhook->group_id = Arr::get($data, 'groupId');
        }

        if (Arr::has($data, 'tagId')) {
            $webhook->tag_id = Arr::get($data, 'tagId');
        }

        if (Arr::has($data, 'extraText')) {
            $webhook->extra_text = Arr::get($data, 'extraText');
        }

        if (Arr::has($data, 'maxPostContentLength')) {
            $webhook->max_post_content_length = Arr::get($data, 'maxPostContentLength');
        }

        if (Arr::has($data, 'usePlainText')) {
            $webhook->use_plain_text = (bool) Arr::get($data, 'usePlainText');
        }

        if (Arr::has($data, 'includeTags')) {
            $webhook->include_tags = (bool) Arr::get($data, 'includeTags');
        }

        if (Arr::has($data, 'messageTemplate')) {
            $webhook->message_template = Arr::get($data, 'messageTemplate');
        }

        $webhook->save();

        return $webhook;
    }
}
