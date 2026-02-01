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

use Flarum\Locale\Translator;
use JiushuTech\DingTalkWebhook\Models\Webhook;

abstract class Action
{
    /**
     * The event class to listen for.
     *
     * @var string
     */
    const EVENT = '';

    /**
     * @var Translator
     */
    protected $translator;

    public function __construct()
    {
        $this->translator = resolve(Translator::class);
    }

    /**
     * @param Webhook $webhook
     * @param mixed   $event
     *
     * @return Response|null
     */
    abstract public function handle(Webhook $webhook, $event): ?Response;

    /**
     * Ignore the event if the actor is a guest.
     *
     * @param mixed $event
     *
     * @return bool
     */
    public function ignore($event): bool
    {
        return !isset($event->actor) || $event->actor->isGuest();
    }

    /**
     * Translate a message.
     *
     * @param string $key
     * @param array  $params
     *
     * @return string
     */
    protected function translate(string $key, ...$params): string
    {
        $key = 'dingtalk-webhook.actions.'.$key;

        $translation = $this->translator->trans($key, ['{1}' => $params[0] ?? '']);

        return $translation;
    }
}
