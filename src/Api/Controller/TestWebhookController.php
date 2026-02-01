<?php

/*
 * This file is part of jiushutech/flarum-dingtalk-webhook.
 *
 * Copyright (c) JiushuTech.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JiushuTech\DingTalkWebhook\Api\Controller;

use Carbon\Carbon;
use Flarum\Http\RequestUtil;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Support\Arr;
use JiushuTech\DingTalkWebhook\Adapters\DingTalk\Adapter;
use JiushuTech\DingTalkWebhook\Models\Webhook;
use JiushuTech\DingTalkWebhook\Response;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class TestWebhookController implements RequestHandlerInterface
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $actor = RequestUtil::getActor($request);
        $actor->assertAdmin();

        $id = Arr::get($request->getQueryParams(), 'id');
        $webhook = Webhook::findOrFail($id);

        try {
            // 创建测试响应
            $testResponse = $this->createTestResponse($webhook);
            $testResponse->withWebhook($webhook);

            // 发送测试消息
            $adapter = new Adapter($this->settings);
            $adapter->send($webhook->url, $testResponse, $webhook->secret);

            return new JsonResponse([
                'success' => true,
                'message' => '测试消息发送成功！',
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => '发送失败：' . $e->getMessage(),
            ], 500);
        }
    }

    protected function createTestResponse(Webhook $webhook): Response
    {
        $response = new Response(null);
        $response->title = '【测试消息】Webhook 配置测试';
        $response->url = $this->settings->get('forum_title') ? 
            resolve(\Flarum\Http\UrlGenerator::class)->to('forum')->base() : '';
        $response->description = '这是一条测试消息，用于验证您的钉钉 Webhook 配置是否正确。如果您收到此消息，说明配置成功！';
        $response->timestamp = Carbon::now();

        return $response;
    }
}
