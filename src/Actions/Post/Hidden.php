<?php

/*
 * This file is part of jiushutech/flarum-dingtalk-webhook.
 *
 * Copyright (c) JiushuTech.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JiushuTech\DingTalkWebhook\Actions\Post;

use JiushuTech\DingTalkWebhook\Models\Webhook;
use JiushuTech\DingTalkWebhook\Response;

class Hidden extends Action
{
    const EVENT = \Flarum\Post\Event\Hidden::class;

    /**
     * @param Webhook                   $webhook
     * @param \Flarum\Post\Event\Hidden $event
     *
     * @return Response
     */
    public function handle(Webhook $webhook, $event): Response
    {
        return Response::build($event)
            ->setTitle(
                $this->translate('post.hidden', $event->post->discussion->title)
            )
            ->setEventType('post.hidden')
            ->setURL('discussion', [
                'id' => $event->post->discussion_id,
            ], '/'.$event->post->number)
            ->setAuthor($event->actor)
            ->setColor($this->getColor($event->post))
            ->setTags($this->getTagNames($event->post))
            ->setTimestamp($event->post->hidden_at);
    }
}
