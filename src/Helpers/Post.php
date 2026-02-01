<?php

/*
 * This file is part of jiushutech/flarum-dingtalk-webhook.
 *
 * Copyright (c) JiushuTech.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JiushuTech\DingTalkWebhook\Helpers;

use JiushuTech\DingTalkWebhook\Models\Webhook;
use s9e\TextFormatter\Utils;

class Post
{
    /**
     * Get the content of a post.
     *
     * @param \Flarum\Post\Post|null $post
     * @param Webhook                $webhook
     *
     * @return string|null
     */
    public static function getContent($post, Webhook $webhook): ?string
    {
        if (!$post) {
            return null;
        }

        $content = $post->content;

        if (!$content) {
            return null;
        }

        // 如果使用纯文本模式
        if ($webhook->use_plain_text) {
            $content = self::toPlainText($content);
        } else {
            // 转换为简单文本，保留基本格式
            $content = self::toSimpleText($content);
        }

        // 限制内容长度
        $maxLength = $webhook->max_post_content_length;
        if ($maxLength > 0 && mb_strlen($content) > $maxLength) {
            $content = mb_substr($content, 0, $maxLength) . '...';
        }

        return $content;
    }

    /**
     * Convert HTML/XML content to plain text.
     *
     * @param string $content
     *
     * @return string
     */
    protected static function toPlainText(string $content): string
    {
        // 尝试使用 s9e TextFormatter 解析 XML 格式的内容
        $text = self::parseXmlContent($content);
        
        if (!empty($text)) {
            // 清理多余的空白
            $text = preg_replace('/\s+/', ' ', $text);
            return trim($text);
        }

        // 如果 XML 解析失败，尝试直接处理
        return self::fallbackParse($content);
    }

    /**
     * Convert content to simple text with basic formatting.
     *
     * @param string $content
     *
     * @return string
     */
    protected static function toSimpleText(string $content): string
    {
        // 尝试使用 s9e TextFormatter 解析 XML 格式的内容
        $text = self::parseXmlContent($content);
        
        if (!empty($text)) {
            // 清理多余的空白行
            $text = preg_replace('/\n{3,}/', "\n\n", $text);
            return trim($text);
        }

        // 如果 XML 解析失败，尝试直接处理
        return self::fallbackParse($content);
    }

    /**
     * Parse XML content using s9e TextFormatter.
     *
     * @param string $content
     *
     * @return string
     */
    protected static function parseXmlContent(string $content): string
    {
        // 检查是否是 XML 格式（Flarum 存储格式）
        if (strpos($content, '<') === 0 || strpos($content, '<?xml') === 0) {
            try {
                // 使用 s9e TextFormatter 的 Utils 类解析
                $text = Utils::removeFormatting($content);
                if (!empty(trim($text))) {
                    return $text;
                }
            } catch (\Exception $e) {
                // 解析失败，继续尝试其他方法
            }

            // 尝试使用 unparse 方法
            try {
                $text = \s9e\TextFormatter\Unparser::unparse($content);
                if (!empty(trim($text))) {
                    return $text;
                }
            } catch (\Exception $e) {
                // 解析失败
            }
        }

        return '';
    }

    /**
     * Fallback parsing method when XML parsing fails.
     *
     * @param string $content
     *
     * @return string
     */
    protected static function fallbackParse(string $content): string
    {
        // 移除 XML/HTML 标签
        $text = preg_replace('/<[^>]+>/', ' ', $content);
        
        // 解码 HTML 实体
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        
        // 清理多余的空白
        $text = preg_replace('/\s+/', ' ', $text);
        
        return trim($text);
    }
}
