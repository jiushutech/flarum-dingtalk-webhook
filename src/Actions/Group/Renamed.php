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

class Renamed extends Action
{
    const EVENT = \Flarum\Group\Event\Renamed::class;

    /**
     * @param Webhook                      $webhook
     * @param \Flarum\Group\Event\Renamed  $event
     *
     * @return Response
     */
    public function handle(Webhook $webhook, $event): Response
    {
        return Response::build($event)
            ->setTitle(
                $this->translate('group.renamed', $event->group->name_plural)
            )
            ->setEventType('group.renamed')
            ->setAuthor($event->actor)
            ->setTimestamp(Carbon::now());
    }
}
