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

class Restored extends Action
{
    const EVENT = \Flarum\Post\Event\Restored::class;

    /**
     * @param Webhook                     $webhook
     * @param \Flarum\Post\Event\Restored $event
     *
     * @return Response
     */
    public function handle(Webhook $webhook, $event): Response
    {
        return Response::build($event)
            ->setTitle(
                $this->translate('post.restored', $event->post->discussion->title)
            )
            ->setEventType('post.restored')
            ->setURL('discussion', [
                'id' => $event->post->discussion_id,
            ], '/'.$event->post->number)
            ->setAuthor($event->actor)
            ->setColor($this->getColor($event->post))
            ->setTags($this->getTagNames($event->post))
            ->setTimestamp(Carbon::now());
    }
}
