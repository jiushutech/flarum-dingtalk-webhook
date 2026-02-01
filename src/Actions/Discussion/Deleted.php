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

use Carbon\Carbon;
use JiushuTech\DingTalkWebhook\Models\Webhook;
use JiushuTech\DingTalkWebhook\Response;

class Deleted extends Action
{
    const EVENT = \Flarum\Discussion\Event\Deleted::class;

    /**
     * @param Webhook                          $webhook
     * @param \Flarum\Discussion\Event\Deleted $event
     *
     * @return Response
     */
    public function handle(Webhook $webhook, $event): Response
    {
        return Response::build($event)
            ->setTitle(
                $this->translate('discussion.deleted', $event->discussion->title)
            )
            ->setEventType('discussion.deleted')
            ->setAuthor($event->actor)
            ->setColor($this->getColor($event->discussion))
            ->setTags($this->getTagNames($event->discussion))
            ->setTimestamp(Carbon::now());
    }
}
