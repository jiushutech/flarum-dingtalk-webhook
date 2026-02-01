<?php

/*
 * This file is part of jiushutech/flarum-dingtalk-webhook.
 *
 * Copyright (c) JiushuTech.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JiushuTech\DingTalkWebhook\Models;

use Flarum\Database\AbstractModel;
use Flarum\Group\Group;
use Flarum\Tags\Tag;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int         $id
 * @property string|null $name
 * @property string      $url
 * @property string|null $secret
 * @property string|null $error
 * @property array       $events
 * @property int         $group_id
 * @property array|null  $tag_id
 * @property string|null $extra_text
 * @property int         $max_post_content_length
 * @property bool        $use_plain_text
 * @property bool        $include_tags
 * @property string|null $message_template
 * @property Group|null  $group
 */
class Webhook extends AbstractModel
{
    /**
     * {@inheritdoc}
     */
    protected $table = 'dingtalk_webhooks';

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'events' => 'array',
        'tag_id' => 'array',
        'use_plain_text' => 'boolean',
        'include_tags' => 'boolean',
    ];

    /**
     * @param string $url
     *
     * @return static
     */
    public static function build(string $url): Webhook
    {
        $webhook = new static();
        $webhook->url = $url;
        $webhook->events = [];

        return $webhook;
    }

    public function getEvents(): array
    {
        return $this->events ?? [];
    }

    public function isValid(): bool
    {
        return preg_match('/^https:\/\/oapi\.dingtalk\.com\/robot\/send\?access_token=.+$/', $this->url);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function tags()
    {
        if (!class_exists(Tag::class)) {
            return null;
        }

        $tagIds = $this->tag_id ?? [];
        return Tag::whereIn('id', $tagIds)->get();
    }

    public function appliedTags(): array
    {
        if (!class_exists(Tag::class)) {
            return [];
        }

        $tagIds = $this->tag_id ?? [];
        return Tag::select('name')->whereIn('id', $tagIds)->pluck('name')->toArray();
    }

    public function getIncludeTags(): bool
    {
        return $this->include_tags ?? false;
    }

    public function asGuest(): bool
    {
        $group = $this->group;

        return !$group || $group->id == Group::GUEST_ID;
    }
}
