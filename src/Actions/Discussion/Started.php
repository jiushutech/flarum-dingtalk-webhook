<?php

/*
 * This file is part of jiushutech/flarum-dingtalk-webhook.
 *
 * Copyright (c) JiushuTech.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JiushuTech\DingTalkWebhook\Actions\Discussion;

use JiushuTech\DingTalkWebhook\Helpers\Post;
use JiushuTech\DingTalkWebhook\Models\Webhook;
use JiushuTech\DingTalkWebhook\Response;

class Started extends Action
{
    const EVENT = \Flarum\Discussion\Event\Started::class;

    /**
     * @param Webhook                          $webhook
     * @param \Flarum\Discussion\Event\Started $event
     *
     * @return Response
     */
    public function handle(Webhook $webhook, $event): Response
    {
        return Response::build($event)
            ->setTitle(
                $this->translate('discussion.started', $event->discussion->title)
            )
            ->setEventType('discussion.started')
            ->setURL('discussion', [
                'id' => $event->discussion->id,
            ])
            ->setDescription(Post::getContent($event->discussion->firstPost, $webhook))
            ->setAuthor($event->actor)
            ->setColor($this->getColor($event->discussion))
            ->setTags($this->getTagNames($event->discussion))
            ->setTimestamp($event->discussion->created_at);
    }
}
