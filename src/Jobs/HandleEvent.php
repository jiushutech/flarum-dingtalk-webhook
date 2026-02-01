<?php

/*
 * This file is part of jiushutech/flarum-dingtalk-webhook.
 *
 * Copyright (c) JiushuTech.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JiushuTech\DingTalkWebhook\Jobs;

use Flarum\Group\Group;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\Tags\Tag;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use JiushuTech\DingTalkWebhook\Adapters\DingTalk\Adapter;
use JiushuTech\DingTalkWebhook\Listener\TriggerListener;
use JiushuTech\DingTalkWebhook\Models\Webhook;
use JiushuTech\DingTalkWebhook\Response;
use ReflectionException;

class HandleEvent implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var mixed
     */
    protected $event;

    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * HandleEvent constructor.
     *
     * @param string $name
     * @param mixed  $event
     */
    public function __construct(string $name, $event)
    {
        $this->name = $name;
        $this->event = $event;
    }

    /**
     * @throws ReflectionException
     */
    public function handle(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;

        $clazz = Arr::get(TriggerListener::$listeners, $this->name);

        if (!$clazz) {
            TriggerListener::debug("{$this->name}: no action class found");
            return;
        }

        TriggerListener::debug("{$this->name}: handling");

        /** @var \JiushuTech\DingTalkWebhook\Action $action */
        $action = new $clazz();

        if ($action->ignore($this->event)) {
            TriggerListener::debug("{$this->name}: ignored");
            return;
        }

        $webhooks = Webhook::all();

        foreach ($webhooks as $webhook) {
            $this->handleWebhook($webhook, $action);
        }
    }

    /**
     * @param Webhook $webhook
     * @param mixed   $action
     *
     * @throws ReflectionException
     */
    protected function handleWebhook(Webhook $webhook, $action)
    {
        $events = $webhook->getEvents();

        if (!in_array($this->name, $events)) {
            TriggerListener::debug("{$this->name}: webhook {$webhook->id} --> not subscribed");
            return;
        }

        // 检查标签限制
        if (!$this->checkTagRestriction($webhook)) {
            TriggerListener::debug("{$this->name}: webhook {$webhook->id} --> tag restriction not met");
            return;
        }

        // 检查组群权限
        if (!$this->checkGroupPermission($webhook)) {
            TriggerListener::debug("{$this->name}: webhook {$webhook->id} --> group permission not met");
            return;
        }

        /** @var Response|null $response */
        $response = $action->handle($webhook, $this->event);

        if (!$response) {
            TriggerListener::debug("{$this->name}: webhook {$webhook->id} --> no response");
            return;
        }

        $response->withWebhook($webhook);

        $adapter = new Adapter($this->settings);
        $adapter->handle($webhook, $response);
    }

    /**
     * 检查标签限制
     *
     * @param Webhook $webhook
     *
     * @return bool
     */
    protected function checkTagRestriction(Webhook $webhook): bool
    {
        $tagIds = $webhook->tag_id ?? [];

        // 如果没有标签限制，允许所有
        if (empty($tagIds)) {
            return true;
        }

        // 检查 Tags 扩展是否启用
        if (!class_exists(Tag::class)) {
            return true;
        }

        // 获取事件相关的标签
        $eventTags = $this->getEventTags();

        if (empty($eventTags)) {
            return false;
        }

        // 检查是否有交集
        return !empty(array_intersect($tagIds, $eventTags));
    }

    /**
     * 获取事件相关的标签 ID
     *
     * @return array
     */
    protected function getEventTags(): array
    {
        $event = $this->event;

        // 尝试从 discussion 获取标签
        if (isset($event->discussion) && method_exists($event->discussion, 'tags')) {
            return $event->discussion->tags->pluck('id')->toArray();
        }

        // 尝试从 post 的 discussion 获取标签
        if (isset($event->post) && isset($event->post->discussion) && method_exists($event->post->discussion, 'tags')) {
            return $event->post->discussion->tags->pluck('id')->toArray();
        }

        return [];
    }

    /**
     * 检查组群权限
     *
     * @param Webhook $webhook
     *
     * @return bool
     */
    protected function checkGroupPermission(Webhook $webhook): bool
    {
        $groupId = $webhook->group_id ?? Group::GUEST_ID;

        // 游客组可以看到所有公开内容
        if ($groupId == Group::GUEST_ID) {
            return true;
        }

        // 这里可以添加更复杂的权限检查逻辑
        return true;
    }
}
