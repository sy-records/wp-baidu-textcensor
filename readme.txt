=== Baidu TextCensor For Comments ===
Contributors: shenyanzhi
Donate link: https://qq52o.me/sponsor.html
Tags: baidu,textcensor,comments,文本内容审核,评论过滤
Requires at least: 4.2
Tested up to: 5.3.2
Requires PHP: 5.6.0
Stable tag: 1.0.4
License: Apache 2.0
License URI: http://www.apache.org/licenses/LICENSE-2.0.html

基于百度文本内容审核技术来提供WordPress评论内容审核

== Description ==

基于百度文本内容审核技术来提供WordPress评论内容审核，对网站用户的评论信息检测，一旦发现用户提交恶意垃圾内容，可以做到文本的自动审核与实时过滤。

## 依赖第三方服务

* 依赖百度AI文本审核技术：https://ai.baidu.com/tech/textcensoring
* 使用说明：https://ai.baidu.com/ai-doc/ANTIPORN/Vk3h6xaga
* 即在WordPress中有新的评论时，将会调用百度文本审核接口进行验证，验证结果分为4种：1. 合规、2. 不合规、3. 疑似、4. 审核失败
* 不改变原有的讨论规则，不合规时提示重新评论；疑似和审核失败时写数据库，人工二次审核

## 主要功能

1. 基于百度Api，一站式检测文本中夹杂的色情、推广、辱骂、违禁、涉政、灌水等垃圾内容，净化网络环境；
2. 用户可以在平台上自助选择审核维度、审核标签，审核松紧度、自定义文本黑白名单，让文本按照勾选的维度、松紧度进行审核。
3. 插件更多详细介绍和安装：[https://github.com/sy-records/wp-baidu-textcensor](https://github.com/sy-records/wp-baidu-textcensor)

## 作者博客

[沈唁志](https://qq52o.me "沈唁志")

接受定制开发 WordPress 插件，如有定制开发需求可以[联系QQ](ttp://wpa.qq.com/msgrd?v=3&uin=85464277&site=qq&menu=yes)。

## 相关插件

**文章内容审核**：[GitHub](https://github.com/sy-records/textcensor-for-articles)，[WordPress Plugins](https://wordpress.org/plugins/textcensor-for-articles)

== Installation ==

1. 把 wp-baidu-textcensor 文件夹上传到 /wp-content/plugins/ 目录下
2. 在后台插件列表中激活 wp-baidu-textcensor
3. 在“百度内容审核设置”菜单中输入百度文本内容审核相关的 AppID、API Key、Secret Key 信息
4. 开始使用吧~

== Screenshots ==

1. screenshot-1.png

== Changelog ==

= 1.0.4 =

* 修复add_submenu_page参数错误提示

= 1.0.3 =

* 更新readme为markdown格式
* 更新插件名称为Baidu TextCensor For Comments
* 修复停用插件删除配置

= 1.0.2 =

* Optimization baiduWpRequest method

= 1.0.1 =

* Updated readme.txt
* Updated pre_comment_approved filter hook

= 1.0.0 =

* First version