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

use JiushuTech\DingTalkWebhook\Models\Webhook;
use JiushuTech\DingTalkWebhook\Response;

class Renamed extends Action
{
    const EVENT = \Flarum\Discussion\Event\Renamed::class;

    /**
     * @param Webhook                          $webhook
     * @param \Flarum\Discussion\Event\Renamed $event
     *
     * @return Response
     */
    public function handle(Webhook $webhook, $event): Response
    {
        return Response::build($event)
            ->setTitle(
                $this->translate('discussion.renamed', $event->oldTitle)
            )
            ->setEventType('discussion.renamed')
            ->setURL('discussion', [
                'id' => $event->discussion->id,
            ])
            ->setDescription($this->translate('discussion.renamed_description', $event->discussion->title))
            ->setAuthor($event->actor)
            ->setColor($this->getColor($event->discussion))
            ->setTags($this->getTagNames($event->discussion))
            ->setTimestamp($event->discussion->renamed_at);
    }
}
