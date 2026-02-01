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

use Flarum\Tags\Tag;
use JiushuTech\DingTalkWebhook\Action as BaseAction;

abstract class Action extends BaseAction
{
    /**
     * Get the color for the discussion event.
     *
     * @param \Flarum\Discussion\Discussion $discussion
     *
     * @return string|null
     */
    protected function getColor($discussion): ?string
    {
        if (!class_exists(Tag::class)) {
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
     * Get the tag names for the discussion.
     *
     * @param \Flarum\Discussion\Discussion $discussion
     *
     * @return array
     */
    protected function getTagNames($discussion): array
    {
        if (!class_exists(Tag::class)) {
            return [];
        }

        $tags = $discussion->tags;

        if (!$tags || $tags->count() === 0) {
            return [];
        }

        return $tags->pluck('name')->toArray();
    }
}
