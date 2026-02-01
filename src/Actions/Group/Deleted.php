<?php

/*
 * This file is part of jiushutech/flarum-dingtalk-webhook.
 *
 * Copyright (c) JiushuTech.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JiushuTech\DingTalkWebhook\Actions\Group;

use Carbon\Carbon;
use JiushuTech\DingTalkWebhook\Action;
use JiushuTech\DingTalkWebhook\Models\Webhook;
use JiushuTech\DingTalkWebhook\Response;

class Deleted extends Action
{
    const EVENT = \Flarum\Group\Event\Deleted::class;

    /**
     * @param Webhook                      $webhook
     * @param \Flarum\Group\Event\Deleted  $event
     *
     * @return Response
     */
    public function handle(Webhook $webhook, $event): Response
    {
        return Response::build($event)
            ->setTitle(
                $this->translate('group.deleted', $event->group->name_plural)
            )
            ->setEventType('group.deleted')
            ->setAuthor($event->actor)
            ->setTimestamp(Carbon::now());
    }
}
