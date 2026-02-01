<?php

/*
 * This file is part of jiushutech/flarum-dingtalk-webhook.
 *
 * Copyright (c) JiushuTech.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JiushuTech\DingTalkWebhook\Adapters;

use Flarum\Foundation\ErrorHandling\LogReporter;
use Flarum\Foundation\ErrorHandling\Reporter;
use Flarum\Settings\SettingsRepositoryInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Container\Container;
use JiushuTech\DingTalkWebhook\Listener\TriggerListener;
use JiushuTech\DingTalkWebhook\Models\Webhook;
use JiushuTech\DingTalkWebhook\Response;
use Psr\Http\Message\ResponseInterface;
use ReflectionClass;
use ReflectionException;
use Throwable;

abstract class Adapter
{
    /**
     * Adapter name.
     *
     * @var string|null
     */
    const NAME = null;

    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * @var Client
     */
    protected $client;

    /**
     * Exception to use on request errors.
     *
     * @var string
     */
    protected $exception;

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;

        $this->client = new Client();
    }

    /**
     * @param Webhook  $webhook
     * @param Response $response
     *
     * @throws ReflectionException
     */
    public function handle(Webhook $webhook, Response $response)
    {
        try {
            $this->send($webhook->url, $response, $webhook->secret);

            TriggerListener::debug(get_class($response->event).": webhook $webhook->id --> sent");

            if (isset($webhook->error)) {
                $webhook->setAttribute('error', null);
            }
        } catch (RequestException $e) {
            $clazz = new ReflectionClass($this->exception);

            if ($e->hasResponse()) {
                $e = $clazz->newInstance($e->getResponse(), $webhook->url);
            }

            TriggerListener::debug(get_class($response->event).": webhook $webhook->id --> request error");

            $this->logException($webhook, $response, $e, true);

            $webhook->setAttribute(
                'error',
                $e->getMessage()
            );
        } catch (Throwable $e) {
            $handled = $e instanceof $this->exception;

            TriggerListener::debug(get_class($response->event).": webhook $webhook->id --> other error");

            $this->logException($webhook, $response, $e, $handled);

            $webhook->setAttribute(
                'error',
                $handled ? $e->getMessage() : $e->getMessage()
            );
        }

        $webhook->save();
    }

    /**
     * Sends a message through the webhook.
     *
     * @param string      $url
     * @param Response    $response
     * @param string|null $secret
     *
     * @throws RequestException
     */
    abstract public function send(string $url, Response $response, ?string $secret = null);

    /**
     * @param Response $response
     *
     * @return array
     */
    abstract public function toArray(Response $response): array;

    /**
     * @param string $url
     *
     * @return bool
     */
    abstract public static function isValidURL(string $url): bool;

    /**
     * @param string $url
     * @param array  $json
     *
     * @throws GuzzleException
     *
     * @return ResponseInterface
     */
    protected function request(string $url, array $json): ResponseInterface
    {
        return $this->client->request('POST', $url, [
            'json'            => mb_convert_encoding($json, 'UTF-8', 'auto'),
            'allow_redirects' => false,
            'headers'         => [
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Get the title of the webhook.
     *
     * @param Response $response
     *
     * @return string
     */
    protected function getTitle(Response $response): string
    {
        $webhookTitle = trim($response->getWebhookName() ?? '');

        return $webhookTitle ?: $this->settings->get('forum_title');
    }

    private function logException(Webhook $webhook, Response $response, Throwable $e, $handled = false)
    {
        resolve('log')->error(
            sprintf(
                "[dingtalk-webhook] %s: %s > %s error
\t- \$webhook = %s
\t- \$response = %s \n
\t%s",
                static::NAME,
                get_class($response->event),
                $handled ? 'webhook' : 'unknown',
                $webhook->url,
                $response,
                $handled
                    ? $e->getMessage()
                    : sprintf("%s\n%s", $e->getMessage(), $e->getTraceAsString())
            )
        );

        // Use reporters (e.g. Sentry) if it's an "unhandled" exception
        if (!($e instanceof $this->exception)) {
            /** @var Reporter[] $reporters */
            $reporters = Container::getInstance()->tagged(Reporter::class);

            foreach ($reporters as $reporter) {
                if ($reporter instanceof LogReporter) {
                    continue;
                }

                $reporter->report($e);
            }
        }
    }
}
