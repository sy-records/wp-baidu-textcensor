<h2 align="center">Baidu TextCensor For Comments</h2>

基于百度文本内容审核技术来提供 WordPress 评论内容审核，对网站用户的评论信息检测，一旦发现用户提交恶意垃圾内容，可以做到文本的自动审核与实时过滤。

## 百度文本内容审核

[百度文本内容审核](https://ai.baidu.com/tech/textcensoring) 能一站式检测文本中夹杂的色情、推广、辱骂、违禁、涉政、灌水等垃圾内容，净化网络环境，为您的应用提供更可靠的内容安全保障，运用业界领先的深度学习技术，判断一段文本内容是否符合网络发文规范，实现自动化、智能化的文本审核，大幅节省内容审核的人力成本，为您的产品体验保驾护航

### 能力介绍

- 文本色情：对文本中的色情行为描述、色情资源链接、低俗交友、污秽文爱等内容进行识别
- 暴恐违禁：对暴力行为、恐怖描述、赌博、毒品、枪支弹药等违禁内容进行识别
- 政治敏感：对文本中的敏感事件、涉政人物、散布谣言、反动宣传等内容进行识别
- 恶意推广：对文本中带有售卖意向的软文广告，微信、QQ 等个人联系方式等违规内容及变体进行识别
- 低俗辱骂：对文本中的侮辱谩骂、人身攻击、消极宣泄等内容进行识别
- 低质灌水：对网络社区常见的乱码、水帖、刷屏等无意义的灌水信息进行识别

## 安装

### 推荐使用

在 WordPress 后台安装插件页面搜索 `Baidu TextCensor For Comments`

### 下载源码

从 GitHub 或 [WordPress Plugins](https://wordpress.org/plugins/baidu-textcensor/) 下载源码，通过 WordPress 后台上传安装，或者直接将源码上传到 WordPress 插件目录 `wp-content/plugins`，然后在后台启用

## 设置

在百度 Ai 控制台的 [产品服务 / 内容审核 - 应用列表 创建应用](https://console.bce.baidu.com/ai/?fromai=1#/ai/antiporn/app/list) 后获取 AppID、API Key、Secret Key

![](.wordpress-org/screenshot-1.png)

### Redis 配置

增加了 Redis 缓存，用于限制 IP 请求次数，避免浪费检测次数，可以在 `wp-config.php` 中配置进行启用：

| 常量                  | 说明             |
| :-------------------- |:---------------|
| `BDTC_ENABLE_REDIS`   | 启用 Redis 配置    |
| `BDTC_REDIS_HOST`     | Redis 主机地址     |
| `BDTC_REDIS_PORT`     | Redis 端口       |
| `BDTC_REDIS_PASSWORD` | Redis 密码       |
| `BDTC_REDIS_DB`       | Redis 数据库      |
| `BDTC_LIMIT`          | 限制次数，默认 `5` 次  |
| `BDTC_EXPIRE`         | 过期时间，默认 `60` 秒 |

```php
define('BDTC_ENABLE_REDIS', true);
define('BDTC_REDIS_HOST', '127.0.0.1');
define('BDTC_REDIS_PORT', 6379);
define('BDTC_REDIS_PASSWORD', '');
define('BDTC_REDIS_DB', 1);

// 以下配置表示：同一个 IP 1 分钟内最多请求 10 次
define('BDTC_LIMIT', 10);
define('BDTC_EXPIRE', 60);
```

## 文章内容审核

请使用`TextCensor For Articles`插件。

[GitHub](https://github.com/sy-records/textcensor-for-articles)、[WordPress Plugins](https://wordpress.org/plugins/textcensor-for-articles)

## 其他

### Typecho

下载源码，将源码上传到 Typecho 插件目录 `usr/plugins`，然后在后台启用

项目地址：[https://github.com/sy-records/ty-baidu-textcensor](https://github.com/sy-records/ty-baidu-textcensor)

### 其他博客系统

在其他博客系统中，如 ThinkPHP、Laravel 等，可以直接使用 composer 包，自行调用处理

项目地址：[https://github.com/sy-records/baidu-textcensor](https://github.com/sy-records/baidu-textcensor)

## License

Apache-2.0
