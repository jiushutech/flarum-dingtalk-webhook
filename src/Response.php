<?php

/*
 * This file is part of jiushutech/flarum-dingtalk-webhook.
 *
 * Copyright (c) JiushuTech.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JiushuTech\DingTalkWebhook;

use Carbon\Carbon;
use Flarum\Http\UrlGenerator;
use Flarum\User\User;
use JiushuTech\DingTalkWebhook\Models\Webhook;

class Response
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $color;

    /**
     * @var array|null
     */
    public $tags;

    /**
     * @var string
     */
    public $timestamp;

    /**
     * @var User
     */
    public $author;

    /**
     * @var mixed
     */
    public $event;

    /**
     * @var string
     */
    public $eventType;

    /**
     * @var UrlGenerator
     */
    private $urlGenerator;

    /**
     * @var Webhook
     */
    protected $webhook;

    /**
     * Response constructor.
     *
     * @param $event
     */
    public function __construct($event)
    {
        $this->event = $event;
        $this->urlGenerator = resolve(UrlGenerator::class);
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function setEventType(string $eventType): self
    {
        $this->eventType = $eventType;

        return $this;
    }

    public function setURL(string $name, ?array $data = null, ?string $extra = null): self
    {
        $url = $this->urlGenerator->to('forum')->route($name, $data);

        if (isset($extra)) {
            $url = $url.$extra;
        }

        $this->url = $url;

        return $this;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function setAuthor(User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function setTimestamp($timestamp): self
    {
        $this->timestamp = $timestamp ?: Carbon::now();

        return $this;
    }

    public function setTags(?array $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function getColor()
    {
        return $this->color ? hexdec(substr($this->color, 1)) : null;
    }

    public static function build($event): self
    {
        return new self($event);
    }

    public function getAuthorUrl(): ?string
    {
        return $this->author && $this->author->exists ? $this->urlGenerator->to('forum')->route('user', [
            'username' => $this->author->username,
        ]) : null;
    }

    public function getExtraText(): ?string
    {
        return $this->webhook->extra_text ?? null;
    }

    public function getIncludeTags(): bool
    {
        return $this->webhook->include_tags ?? false;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function getWebhookName(): ?string
    {
        return $this->webhook->name ?? null;
    }

    public function getMaxPostContentLength(): int
    {
        return $this->webhook->max_post_content_length ?? 200;
    }

    public function getUsePlainText(): bool
    {
        return $this->webhook->use_plain_text ?? false;
    }

    public function getMessageTemplate(): ?string
    {
        return $this->webhook->message_template ?? null;
    }

    public function withWebhook(Webhook $webhook): self
    {
        $this->setWebhook($webhook);

        return $this;
    }

    protected function setWebhook(Webhook $webhook)
    {
        $this->webhook = $webhook;
    }

    public function __toString()
    {
        $authorName = $this->author ? $this->author->display_name : 'Unknown';
        return "Response{title=$this->title,url=$this->url,author={$authorName}}";
    }
}
