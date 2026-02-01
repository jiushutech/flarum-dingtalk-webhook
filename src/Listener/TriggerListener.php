<?php

/*
 * This file is part of jiushutech/flarum-dingtalk-webhook.
 *
 * Copyright (c) JiushuTech.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JiushuTech\DingTalkWebhook\Listener;

use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Support\Arr;
use JiushuTech\DingTalkWebhook\Actions;
use JiushuTech\DingTalkWebhook\Jobs\HandleEvent;

class TriggerListener
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * @var Queue
     */
    protected $queue;

    /**
     * @var array<string, string>
     */
    public static $listeners = null;

    /**
     * @var bool|null
     */
    protected static $isDebugging = null;

    /**
     * EventListener constructor.
     *
     * @param SettingsRepositoryInterface $settings
     * @param Queue                       $queue
     */
    public function __construct(SettingsRepositoryInterface $settings, Queue $queue)
    {
        $this->settings = $settings;
        $this->queue = $queue;

        if (self::$listeners == null) {
            self::setupDefaultListeners();
        }
    }

    /**
     * Subscribes to the Flarum events.
     *
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen('*', [$this, 'run']);
    }

    /**
     * @param $name
     * @param $data
     *
     * @throws \ReflectionException
     */
    public function run($name, $data)
    {
        $event = Arr::get($data, 0);

        if (!isset($event) || !array_key_exists($name, self::$listeners)) {
            return;
        }

        self::debug("$name: queuing");

        $this->queue->push(
            new HandleEvent($name, $event)
        );
    }

    public static function setupDefaultListeners()
    {
        // Discussion events
        self::addListener(Actions\Discussion\Deleted::class);
        self::addListener(Actions\Discussion\Hidden::class);
        self::addListener(Actions\Discussion\Renamed::class);
        self::addListener(Actions\Discussion\Restored::class);
        self::addListener(Actions\Discussion\Started::class);

        // Group events
        self::addListener(Actions\Group\Created::class);
        self::addListener(Actions\Group\Renamed::class);
        self::addListener(Actions\Group\Deleted::class);

        // Post events
        self::addListener(Actions\Post\Posted::class);
        self::addListener(Actions\Post\Revised::class);
        self::addListener(Actions\Post\Hidden::class);
        self::addListener(Actions\Post\Restored::class);
        self::addListener(Actions\Post\Deleted::class);
        self::addListener(Actions\Post\Approved::class);

        // User events
        self::addListener(Actions\User\Renamed::class);
        self::addListener(Actions\User\Registered::class);
        self::addListener(Actions\User\Deleted::class);
    }

    public static function addListener(string $action)
    {
        if (!class_exists($action)) {
            return;
        }

        $clazz = @constant("$action::EVENT");

        if (isset($clazz) && (is_string($clazz) && class_exists($clazz))) {
            self::$listeners[$clazz] = $action;
        }
    }

    public static function debug(string $message)
    {
        if (is_null(self::$isDebugging)) {
            self::$isDebugging = (bool) (int) resolve('flarum.settings')->get('dingtalk-webhook.debug');
        }

        if (self::$isDebugging) {
            resolve('log')->info('[dingtalk-webhook] #DEBUG# '.$message);
        }
    }
}
