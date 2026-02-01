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

use Carbon\Carbon;
use JiushuTech\DingTalkWebhook\Models\Webhook;
use JiushuTech\DingTalkWebhook\Response;

class Deleted extends Action
{
    const EVENT = \Flarum\Post\Event\Deleted::class;

    /**
     * @param Webhook                    $webhook
     * @param \Flarum\Post\Event\Deleted $event
     *
     * @return Response
     */
    public function handle(Webhook $webhook, $event): Response
    {
        return Response::build($event)
            ->setTitle(
                $this->translate('post.deleted', $event->post->discussion->title)
            )
            ->setEventType('post.deleted')
            ->setAuthor($event->actor)
            ->setColor($this->getColor($event->post))
            ->setTags($this->getTagNames($event->post))
            ->setTimestamp(Carbon::now());
    }
}
