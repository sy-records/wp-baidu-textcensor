=== Baidu TextCensor ===
Contributors: shenyanzhi
Donate link: https://qq52o.me/sponsor.html
Tags: baidu,textcensor,文本内容审核,评论过滤
Requires at least: 4.2
Tested up to: 5.3.2
Stable tag: 4.3
Requires PHP: 5.4.0
Stable tag: 1.0.1
License: Apache 2.0
License URI: http://www.apache.org/licenses/LICENSE-2.0.html

基于百度文本内容审核过滤WordPress评论中的一些敏感内容

== Description ==

<strong>在WordPress中加入百度文本内容审核，过滤评论敏感内容</strong>

<strong>依赖第三方服务</strong>
<ul>
    <li>依赖百度AI文本审核技术：https://ai.baidu.com/tech/textcensoring</li>
    <li>使用说明：https://ai.baidu.com/ai-doc/ANTIPORN/Vk3h6xaga</li>
    <li>即在WordPress中有新的评论时，将会调用百度文本审核接口进行验证，验证结果分为4种，分别为1.合规，2.不合规，3.疑似，4.审核失败</li>
    <li>不改变原有的讨论规则，不合规时提示重新评论；疑似和审核失败时写数据库，人工二次审核</li>
</ul>

<strong>主要功能：</strong>
1. 基于百度Api，一站式检测文本中夹杂的色情、推广、辱骂、违禁、涉政、灌水等垃圾内容，净化网络环境；
2. 用户可以在平台上自助选择审核维度、审核标签，审核松紧度、自定义文本黑白名单，让文本按照勾选的维度、松紧度进行审核。
3. 插件更多详细介绍和安装：https://github.com/sy-records/wp-baidu-textcensor

<strong>支持网站/博主：</strong>
支持网站：https://qq52o.me/sponsor.html [沈唁志](https://qq52o.me/sponsor.html "沈唁志")

== Installation ==

1. 把 wp-baidu-textcensor 文件夹上传到 /wp-content/plugins/ 目录下<br />
2. 在后台插件列表中激活 wp-baidu-textcensor<br />
3. 在“百度内容审核设置”菜单中输入百度文本内容审核相关的 AppID、API Key、Secret Key 信息<br />
4. 开始使用吧~

== Screenshots ==

1. screenshot-1.png

== Changelog ==

= 1.0.1 =
* Updated readme.txt
* Updated pre_comment_approved filter hook

= 1.0.0 =
* First version