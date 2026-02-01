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

use JiushuTech\DingTalkWebhook\Helpers\Post;
use JiushuTech\DingTalkWebhook\Models\Webhook;
use JiushuTech\DingTalkWebhook\Response;

class Approved extends Action
{
    const EVENT = 'Flarum\Approval\Event\PostWasApproved';

    /**
     * @param Webhook $webhook
     * @param mixed   $event
     *
     * @return Response|null
     */
    public function handle(Webhook $webhook, $event): ?Response
    {
        if (!class_exists('Flarum\Approval\Event\PostWasApproved')) {
            return null;
        }

        return Response::build($event)
            ->setTitle(
                $this->translate('post.approved', $event->post->discussion->title)
            )
            ->setEventType('post.approved')
            ->setURL('discussion', [
                'id' => $event->post->discussion_id,
            ], '/'.$event->post->number)
            ->setDescription(Post::getContent($event->post, $webhook))
            ->setAuthor($event->actor)
            ->setColor($this->getColor($event->post))
            ->setTags($this->getTagNames($event->post))
            ->setTimestamp($event->post->created_at);
    }

    /**
     * @param mixed $event
     *
     * @return bool
     */
    public function ignore($event): bool
    {
        return !class_exists('Flarum\Approval\Event\PostWasApproved') || parent::ignore($event);
    }
}
