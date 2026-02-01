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

use Carbon\Carbon;
use JiushuTech\DingTalkWebhook\Adapters\Adapter as BaseAdapter;
use JiushuTech\DingTalkWebhook\Response;

class Adapter extends BaseAdapter
{
    /**
     * {@inheritdoc}
     */
    const NAME = 'dingtalk';

    protected $exception = DingTalkException::class;

    /**
     * Sends a message through the webhook.
     *
     * @param string      $url
     * @param Response    $response
     * @param string|null $secret
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send(string $url, Response $response, ?string $secret = null)
    {
        // 如果有加签密钥，添加签名参数
        if ($secret) {
            $url = $this->appendSignature($url, $secret);
        }

        $this->request($url, [
            'msgtype'  => 'markdown',
            'markdown' => [
                'title' => $this->getTitle($response),
                'text'  => $this->buildMarkdownText($response),
            ],
        ]);
    }

    /**
     * 构建 Markdown 格式的消息文本
     *
     * @param Response $response
     *
     * @return string
     */
    protected function buildMarkdownText(Response $response): string
    {
        // 检查是否有自定义模板
        $customTemplate = $response->getMessageTemplate();
        if ($customTemplate) {
            return $this->parseTemplate($customTemplate, $response);
        }

        // 使用默认模板
        return $this->buildDefaultMarkdownText($response);
    }

    /**
     * 解析自定义模板
     *
     * @param string   $template
     * @param Response $response
     *
     * @return string
     */
    protected function parseTemplate(string $template, Response $response): string
    {
        $tags = $response->getTags();
        $tagsString = !empty($tags) ? implode(', ', $tags) : '';

        $replacements = [
            '{title}' => $response->title ?? '',
            '{url}' => $response->url ?? '',
            '{description}' => $response->description ?? '',
            '{author}' => $response->author ? $response->author->display_name : '',
            '{author_url}' => $response->getAuthorUrl() ?? '',
            '{timestamp}' => $response->timestamp instanceof Carbon 
                ? $response->timestamp->format('Y-m-d H:i:s') 
                : (string) ($response->timestamp ?? ''),
            '{extra_text}' => $response->getExtraText() ?? '',
            '{tags}' => $tagsString,
            '{event_type}' => $response->eventType ?? '',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    /**
     * 构建默认 Markdown 格式的消息文本
     *
     * @param Response $response
     *
     * @return string
     */
    protected function buildDefaultMarkdownText(Response $response): string
    {
        $lines = [];

        // 事件类型标题
        $lines[] = "### {$response->title}";
        $lines[] = '';

        // 操作人信息
        if ($response->author && $response->author->exists) {
            $authorUrl = $response->getAuthorUrl();
            $authorName = $response->author->display_name;
            $lines[] = "**操作人**: [{$authorName}]({$authorUrl})";
            $lines[] = '';
        }

        // 时间
        if ($response->timestamp) {
            $time = $response->timestamp instanceof Carbon
                ? $response->timestamp->format('Y-m-d H:i:s')
                : (string) $response->timestamp;
            $lines[] = "**时间**: {$time}";
            $lines[] = '';
        }

        // 内容描述
        if ($response->description) {
            $maxLength = $response->getMaxPostContentLength();
            $description = $response->description;

            if ($maxLength > 0 && mb_strlen($description) > $maxLength) {
                $description = mb_substr($description, 0, $maxLength) . '...';
            }

            $lines[] = "**内容**: {$description}";
            $lines[] = '';
        }

        // 标签信息
        if ($response->getIncludeTags()) {
            $tags = $response->getTags();
            if (!empty($tags)) {
                $lines[] = "**标签**: " . implode(', ', $tags);
                $lines[] = '';
            }
        }

        // 附加文本
        $extraText = $response->getExtraText();
        if ($extraText) {
            $lines[] = "> {$extraText}";
            $lines[] = '';
        }

        // 链接
        if ($response->url) {
            $lines[] = "[查看详情]({$response->url})";
        }

        return implode("\n", $lines);
    }

    /**
     * 添加签名参数到 URL
     *
     * @param string $url
     * @param string $secret
     *
     * @return string
     */
    protected function appendSignature(string $url, string $secret): string
    {
        $timestamp = time() * 1000;
        $stringToSign = $timestamp . "\n" . $secret;
        $sign = urlencode(base64_encode(hash_hmac('sha256', $stringToSign, $secret, true)));

        $separator = strpos($url, '?') !== false ? '&' : '?';

        return $url . $separator . "timestamp={$timestamp}&sign={$sign}";
    }

    /**
     * @param Response $response
     *
     * @return array
     */
    public function toArray(Response $response): array
    {
        return [
            'msgtype'  => 'markdown',
            'markdown' => [
                'title' => $this->getTitle($response),
                'text'  => $this->buildMarkdownText($response),
            ],
        ];
    }

    /**
     * @param string $url
     *
     * @return bool
     */
    public static function isValidURL(string $url): bool
    {
        return (bool) preg_match('/^https:\/\/oapi\.dingtalk\.com\/robot\/send\?access_token=.+$/', $url);
    }

    /**
     * 获取消息标题
     *
     * @param Response $response
     *
     * @return string
     */
    protected function getTitle(Response $response): string
    {
        return $response->title ?: parent::getTitle($response);
    }
}
