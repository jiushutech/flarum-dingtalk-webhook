<?php

/*
 * This file is part of jiushutech/flarum-dingtalk-webhook.
 *
 * Copyright (c) JiushuTech.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JiushuTech\DingTalkWebhook\Actions\User;

use Carbon\Carbon;
use JiushuTech\DingTalkWebhook\Action;
use JiushuTech\DingTalkWebhook\Models\Webhook;
use JiushuTech\DingTalkWebhook\Response;

class Registered extends Action
{
    const EVENT = \Flarum\User\Event\Registered::class;

    /**
     * @param Webhook                        $webhook
     * @param \Flarum\User\Event\Registered  $event
     *
     * @return Response
     */
    public function handle(Webhook $webhook, $event): Response
    {
        return Response::build($event)
            ->setTitle(
                $this->translate('user.registered')
            )
            ->setEventType('user.registered')
            ->setURL('user', [
                'username' => $event->user->username,
            ])
            ->setAuthor($event->user)
            ->setTimestamp($event->user->joined_at ?? Carbon::now());
    }

    /**
     * @param mixed $event
     *
     * @return bool
     */
    public function ignore($event): bool
    {
        return false;
    }
}
