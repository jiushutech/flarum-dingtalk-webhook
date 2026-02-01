<?php

/*
 * This file is part of jiushutech/flarum-dingtalk-webhook.
 *
 * Copyright (c) JiushuTech.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JiushuTech\DingTalkWebhook\Api\Serializer;

use Flarum\Api\Serializer\AbstractSerializer;
use JiushuTech\DingTalkWebhook\Models\Webhook;

class WebhookSerializer extends AbstractSerializer
{
    /**
     * {@inheritdoc}
     */
    protected $type = 'dingtalk-webhooks';

    /**
     * @param Webhook $webhook
     *
     * @return array
     */
    protected function getDefaultAttributes($webhook): array
    {
        return [
            'name'                  => $webhook->name,
            'url'                   => $webhook->url,
            'secret'                => $webhook->secret ? '******' : null,
            'hasSecret'             => !empty($webhook->secret),
            'events'                => $webhook->events,
            'groupId'               => $webhook->group_id,
            'tagId'                 => $webhook->tag_id,
            'extraText'             => $webhook->extra_text,
            'maxPostContentLength'  => $webhook->max_post_content_length,
            'usePlainText'          => $webhook->use_plain_text,
            'includeTags'           => $webhook->include_tags,
            'messageTemplate'       => $webhook->message_template,
            'error'                 => $webhook->error,
            'isValid'               => $webhook->isValid(),
        ];
    }
}
