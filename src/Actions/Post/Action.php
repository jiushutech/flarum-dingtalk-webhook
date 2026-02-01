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

use Flarum\Tags\Tag;
use JiushuTech\DingTalkWebhook\Action as BaseAction;

abstract class Action extends BaseAction
{
    /**
     * Get the color for the post event.
     *
     * @param \Flarum\Post\Post $post
     *
     * @return string|null
     */
    protected function getColor($post): ?string
    {
        if (!class_exists(Tag::class)) {
            return null;
        }

        $discussion = $post->discussion;

        if (!$discussion) {
            return null;
        }

        $tags = $discussion->tags;

        if ($tags && $tags->count() > 0) {
            $tag = $tags->first();
            return $tag->color ?? null;
        }

        return null;
    }

    /**
     * Get the tag names for the post's discussion.
     *
     * @param \Flarum\Post\Post $post
     *
     * @return array
     */
    protected function getTagNames($post): array
    {
        if (!class_exists(Tag::class)) {
            return [];
        }

        $discussion = $post->discussion;

        if (!$discussion) {
            return [];
        }

        $tags = $discussion->tags;

        if (!$tags || $tags->count() === 0) {
            return [];
        }

        return $tags->pluck('name')->toArray();
    }
}
