# Flarum DingTalk Webhook Plugin

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![Version](https://img.shields.io/badge/version-1.0.0-green.svg)](https://github.com/jiushutech/flarum-dingtalk-webhook)
[![Flarum](https://img.shields.io/badge/flarum-%5E1.7.0-orange.svg)](https://flarum.org)

[English](#english) | [中文](#中文)

---

## English

Push Flarum forum events to DingTalk group robots for real-time community notifications.

### Features

- 🔔 **Multiple Events**: Support for discussions, posts, users, and groups events
- 📝 **Markdown Format**: Messages are formatted in DingTalk Markdown for clear presentation
- 🔐 **Signature Security**: Support for DingTalk robot signature verification
- 🏷️ **Tag Filtering**: Limit webhooks to trigger only for specific tags
- 👥 **Group Permissions**: Configure to push only content visible to specific user groups
- 🎨 **Card-style Management**: Intuitive card-based admin interface
- ✏️ **Custom Templates**: Support custom message templates for flexible formatting
- 🌐 **Bilingual Support**: Full English and Chinese interface

### Installation

Install via Composer:

```bash
composer require jiushutech/flarum-dingtalk-webhook
```

### DingTalk Robot Configuration

#### Step 1: Create DingTalk Group Robot

1. Open DingTalk and enter the group chat where you want to receive notifications
2. Click Group Settings (top right `...`) → **Smart Group Assistant** → **Add Robot**
3. Select **Custom** robot
4. Set robot name (e.g., Forum Notifications) and avatar

#### Step 2: Configure Security Settings

DingTalk robots support three security settings. **Signature mode is recommended**:

##### Option 1: Signature (Recommended) ✅

1. Check **Signature** in security settings
2. System will generate a key starting with `SEC`, e.g., `SECxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx`
3. **Copy and save this key** for later configuration in Flarum admin

##### Option 2: Custom Keywords

1. Check **Custom Keywords**
2. Set keywords (e.g., `forum`, `notification`)
3. Messages must contain at least one keyword to be sent successfully

##### Option 3: IP Address (Range)

1. Check **IP Address (Range)**
2. Enter your server's public IP address
3. Only requests from specified IPs will be accepted

#### Step 3: Get Webhook URL

1. After completing security settings, click **Finish**
2. System will display the Webhook URL in this format:
   ```
   https://oapi.dingtalk.com/robot/send?access_token=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
   ```
3. **Copy and save this URL**

#### Step 4: Configure in Flarum Admin

1. Log in to Flarum admin panel
2. Find **DingTalk Webhook** extension in the left menu
3. Click **Add Webhook** button
4. Fill in configuration:

| Setting | Description |
|---------|-------------|
| **Name** | Display name for the webhook |
| **Webhook URL** | Paste the Webhook URL from DingTalk |
| **Signature Key** | If using signature security, enter the `SEC` key |
| **Events** | Select event types to trigger notifications |
| **Tag Filter** | Optional, limit to specific tags |
| **Group Filter** | Optional, limit to content visible to specific groups |

5. Click **Save** to complete configuration
6. Click **Test Send** to verify configuration

### Supported Events

| Category | Event | Description |
|----------|-------|-------------|
| **Discussion** | discussion.started | New discussion created |
| | discussion.renamed | Discussion renamed |
| | discussion.hidden | Discussion hidden |
| | discussion.restored | Discussion restored |
| | discussion.deleted | Discussion deleted |
| **Post** | post.posted | New reply posted |
| | post.revised | Reply edited |
| | post.hidden | Reply hidden |
| | post.restored | Reply restored |
| | post.deleted | Reply deleted |
| | post.approved | Reply approved (requires flarum/approval) |
| **User** | user.registered | User registered |
| | user.renamed | User renamed |
| | user.deleted | User deleted |
| **Group** | group.created | Group created |
| | group.renamed | Group renamed |
| | group.deleted | Group deleted |

### Custom Message Templates

#### Available Variables

| Variable | Description | Example |
|----------|-------------|---------|
| `{title}` | Message title | `【New Post】Tech Discussion` |
| `{url}` | Resource URL | `https://forum.example.com/d/123` |
| `{description}` | Content description | `This is the post content...` |
| `{author}` | Author name | `John` |
| `{author_url}` | Author profile URL | `https://forum.example.com/u/john` |
| `{timestamp}` | Event time | `2026-02-01 20:00:00` |
| `{extra_text}` | Additional text | `Notification from Tech Forum` |
| `{tags}` | Related tags | `Tech, Discussion, Announcement` |
| `{event_type}` | Event type | `discussion.started` |

#### Template Examples

**Simple Notification Template**

```markdown
### {title}

**Author**: [{author}]({author_url})
**Time**: {timestamp}

{description}

[👉 View Details]({url})
```

**Minimal Template**

```markdown
**{title}**

{description}

[View Details]({url}) | {author} | {timestamp}
```

### Development

#### Frontend Build

```bash
cd js
npm install
npm run dev    # Development mode
npm run build  # Production build
```

### License

MIT License

### Author

[JiushuTech](https://github.com/jiushutech)

### Links

- [GitHub Repository](https://github.com/jiushutech/flarum-dingtalk-webhook)
- [Issue Tracker](https://github.com/jiushutech/flarum-dingtalk-webhook/issues)

---

## 中文

将 Flarum 论坛事件推送到钉钉群机器人，实现社区动态的实时通知。

### 功能特性

- 🔔 **多事件支持**：支持主题、回复、用户、用户组等多种事件类型
- 📝 **Markdown 格式**：推送消息采用钉钉 Markdown 格式，排版清晰
- 🔐 **加签安全**：支持钉钉机器人加签安全验证
- 🏷️ **标签过滤**：可限制 Webhook 只在特定标签下触发
- 👥 **组群权限**：可配置只推送特定组群可见的内容
- 🎨 **卡片式管理**：后台采用卡片式界面，配置直观便捷
- ✏️ **自定义模板**：支持自定义消息模板，灵活控制推送格式
- 🌐 **双语支持**：完整的中英文界面

### 安装

使用 Composer 安装：

```bash
composer require jiushutech/flarum-dingtalk-webhook
```

### 钉钉机器人配置

#### 第一步：创建钉钉群机器人

1. 打开钉钉，进入需要接收通知的群聊
2. 点击群设置（右上角 `...`）→ **智能群助手** → **添加机器人**
3. 选择 **自定义** 机器人
4. 设置机器人名称（如：论坛通知）和头像

#### 第二步：配置安全设置

钉钉机器人支持三种安全设置方式，**推荐使用"加签"方式**：

##### 方式一：加签（推荐）✅

1. 在安全设置中勾选 **加签**
2. 系统会生成一个以 `SEC` 开头的密钥，如：`SECxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx`
3. **复制并保存此密钥**，稍后需要在 Flarum 后台配置

##### 方式二：自定义关键词

1. 勾选 **自定义关键词**
2. 设置关键词（如：`论坛`、`通知`）
3. 发送的消息必须包含至少一个关键词才能发送成功

##### 方式三：IP 地址（段）

1. 勾选 **IP 地址（段）**
2. 填写服务器的公网 IP 地址
3. 只有来自指定 IP 的请求才会被接受

#### 第三步：获取 Webhook 地址

1. 完成安全设置后，点击 **完成**
2. 系统会显示 Webhook 地址，格式如下：
   ```
   https://oapi.dingtalk.com/robot/send?access_token=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
   ```
3. **复制并保存此地址**

#### 第四步：在 Flarum 后台配置

1. 登录 Flarum 管理后台
2. 在左侧菜单找到 **钉钉 Webhook** 扩展
3. 点击 **添加 Webhook** 按钮
4. 填写配置信息：

| 配置项 | 说明 |
|--------|------|
| **名称** | Webhook 的显示名称，便于识别 |
| **Webhook 地址** | 粘贴从钉钉获取的 Webhook URL |
| **加签密钥** | 如果使用加签安全设置，填写 `SEC` 开头的密钥 |
| **事件** | 选择要触发推送的事件类型 |
| **标签过滤** | 可选，限制只在特定标签下触发 |
| **用户组过滤** | 可选，限制只推送特定用户组可见的内容 |

5. 点击 **保存** 完成配置
6. 可点击 **测试发送** 验证配置是否正确

### 支持的事件

| 分类 | 事件 | 说明 |
|------|------|------|
| **主题** | discussion.started | 新帖发布 |
| | discussion.renamed | 主题重命名 |
| | discussion.hidden | 主题隐藏 |
| | discussion.restored | 主题恢复 |
| | discussion.deleted | 主题删除 |
| **回复** | post.posted | 新回复 |
| | post.revised | 回复编辑 |
| | post.hidden | 回复隐藏 |
| | post.restored | 回复恢复 |
| | post.deleted | 回复删除 |
| | post.approved | 回复审核通过（需安装 flarum/approval） |
| **用户** | user.registered | 用户注册 |
| | user.renamed | 用户改名 |
| | user.deleted | 用户删除 |
| **用户组** | group.created | 用户组创建 |
| | group.renamed | 用户组重命名 |
| | group.deleted | 用户组删除 |

### 自定义消息模板

#### 可用变量

| 变量 | 说明 | 示例值 |
|------|------|--------|
| `{title}` | 消息标题 | `【新帖发布】技术讨论区` |
| `{url}` | 资源链接 | `https://forum.example.com/d/123` |
| `{description}` | 内容描述 | `这是帖子的正文内容...` |
| `{author}` | 操作人名称 | `张三` |
| `{author_url}` | 操作人主页 | `https://forum.example.com/u/zhangsan` |
| `{timestamp}` | 事件时间 | `2026-02-01 20:00:00` |
| `{extra_text}` | 附加文本 | `来自技术论坛的通知` |
| `{tags}` | 相关标签 | `技术, 讨论, 公告` |
| `{event_type}` | 事件类型 | `discussion.started` |

#### 模板示例

**简洁通知模板**

```markdown
### {title}

**操作人**: [{author}]({author_url})
**时间**: {timestamp}

{description}

[👉 点击查看详情]({url})
```

**极简模板**

```markdown
**{title}**

{description}

[查看详情]({url}) | {author} | {timestamp}
```

### 开发

#### 前端构建

```bash
cd js
npm install
npm run dev    # 开发模式
npm run build  # 生产构建
```

### 常见问题

#### Q: 消息发送失败怎么办？

1. 检查 Webhook 地址是否正确
2. 如果使用加签，确认密钥是否正确
3. 如果使用关键词，确认消息内容包含关键词
4. 检查服务器是否能访问钉钉 API（`oapi.dingtalk.com`）

#### Q: 如何测试配置是否正确？

在 Webhook 卡片上点击"测试发送"按钮，会发送一条测试消息到钉钉群。

#### Q: 为什么某些事件没有触发？

1. 检查是否勾选了对应的事件类型
2. 检查标签过滤设置是否正确
3. 检查用户组权限设置

### 许可证

MIT License

### 作者

[JiushuTech](https://github.com/jiushutech)

### 链接

- [GitHub 仓库](https://github.com/jiushutech/flarum-dingtalk-webhook)
- [问题反馈](https://github.com/jiushutech/flarum-dingtalk-webhook/issues)
