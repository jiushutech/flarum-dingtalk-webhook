<?php

/*
 * This file is part of jiushutech/flarum-dingtalk-webhook.
 *
 * Copyright (c) JiushuTech.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JiushuTech\DingTalkWebhook\Adapters\DingTalk;

use Exception;
use Psr\Http\Message\ResponseInterface;

class DingTalkException extends Exception
{
    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var string
     */
    protected $webhookUrl;

    public function __construct(ResponseInterface $response, string $webhookUrl)
    {
        $this->response = $response;
        $this->webhookUrl = $webhookUrl;

        $body = json_decode($response->getBody()->getContents(), true);
        $errcode = $body['errcode'] ?? 'unknown';
        $errmsg = $body['errmsg'] ?? '未知错误';

        parent::__construct("钉钉 Webhook 错误 [{$errcode}]: {$errmsg}");
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function getWebhookUrl(): string
    {
        return $this->webhookUrl;
    }
}
