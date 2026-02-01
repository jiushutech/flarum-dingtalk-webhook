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

class Posted extends Action
{
    const EVENT = \Flarum\Post\Event\Posted::class;

    /**
     * @param Webhook                    $webhook
     * @param \Flarum\Post\Event\Posted  $event
     *
     * @return Response|null
     */
    public function handle(Webhook $webhook, $event): ?Response
    {
        // 忽略主题的第一个帖子（由 Discussion\Started 处理）
        if ($event->post->number == 1) {
            return null;
        }

        return Response::build($event)
            ->setTitle(
                $this->translate('post.posted', $event->post->discussion->title)
            )
            ->setEventType('post.posted')
            ->setURL('discussion', [
                'id' => $event->post->discussion_id,
            ], '/'.$event->post->number)
            ->setDescription(Post::getContent($event->post, $webhook))
            ->setAuthor($event->actor)
            ->setColor($this->getColor($event->post))
            ->setTags($this->getTagNames($event->post))
            ->setTimestamp($event->post->created_at);
    }
}
