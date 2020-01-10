<?php
/*
Plugin Name:  Baidu TextCensor
Plugin URI:   https://github.com/sy-records/wp-baidu-textcensor
Description:  在WordPress中加入百度AI文本内容审核，过滤敏感内容
Version:      1.0.0
Author:       沈唁
Author URI:   https://qq52o.me
License:      Apache 2.0
*/

// init plugin
add_action('admin_init', 'bdtc_submit_default_options');
function bdtc_submit_default_options()
{
    // 获取选项
    $default = get_option('BaiduTextCensor');
    if ($default == '') {
        // 设置默认数据
        $default = array(
            'app_id' => '',
            'api_key' => '',
            'secret_key' => '',
            'check_me' => '',
            'delete' => '',
        );
        //更新选项
        update_option('BaiduTextCensor', $default);
    }
}

// setting plugin
add_action('admin_menu', 'bdtc_submit_menu');
function bdtc_submit_menu()
{
    add_submenu_page('options-general.php', '百度内容审核设置', '百度内容审核设置', 'manage_options', 'Baidu_Text_Censor', 'bdtc_submit_options', '');
}

// add setting button
function bdtc_plugin_action_links($links, $file)
{
    if ($file == plugin_basename(dirname(__FILE__) . '/wp-baidu-textcensor.php')) {
        $links[] = '<a href="options-general.php?page=Baidu_Text_Censor">设置</a>';
    }
    return $links;
}
add_filter('plugin_action_links', 'bdtc_plugin_action_links', 10, 2);

// setting page
function bdtc_submit_options()
{
    //保存数据
    if (isset($_POST['bdtc_submit'])) {
        if (!current_user_can('level_10')) {
            echo '<div class="error" id="message"><p>暂无权限操作</p></div>';
            return;
        }
        $nonce = $_REQUEST['_bdtc_nonce'];
        if (!wp_verify_nonce($nonce, 'bdtcSubmit')) {
            echo '<div class="error" id="message"><p>非法操作</p></div>';
            return;
        }

        $app_id = sanitize_text_field($_POST['app_id']);
        $api_key = sanitize_text_field($_POST['api_key']);
        $secret_key = sanitize_text_field($_POST['secret_key']);
        $check_me = isset($_POST['check_me']) ? sanitize_text_field($_POST['check_me']) : false;
        $delete = isset($_POST['delete']) ? sanitize_text_field($_POST['delete']) : false;

        $check_status = bdtc_submit_check($app_id, $api_key, $secret_key);
        if ($check_status) {
            echo '<div class="error" id="message"><p>获取Access Token失败，请检查参数</p></div>';
        } else {
            $pwtwOption = array(
                'app_id' => $app_id,
                'api_key' => $api_key,
                'secret_key' => $secret_key,
                'check_me' => $check_me,
                'delete' => $delete,
            );
            $res = update_option('BaiduTextCensor', $pwtwOption);//更新选项
            if ($res) {
                $updated = '设置成功！';
            } else {
                $updated = '设置失败或未更新选项！';
            }
            echo '<div class="updated" id="message"><p>' . $updated . '</p></div>';
        }
    }
    // //获取选项
    $option = get_option('BaiduTextCensor');
    $check_me = $option['check_me'] !== false ? 'checked="checked"' : '';
    $delete = $option['delete'] !== false ? 'checked="checked"' : '';
    echo '<div class="wrap">';
    echo '<h2>百度内容审核设置</h2>';
    echo '<form method="post">';
    echo '<table class="form-table">';
    echo '<tr valign="top">';
    echo '<td><input class="all-options" type="hidden" name="_bdtc_nonce" value="' . wp_create_nonce('bdtcSubmit') . '"></td>';
    echo '</tr>';
    echo '<tr valign="top">';
    echo '<th scope="row">AppID</th>';
    echo '<td><input class="all-options" type="text" name="app_id" value="' . $option['app_id'] . '" /></td>';
    echo '</tr>';
    echo '<tr valign="top">';
    echo '<th scope="row">API Key</th>';
    echo '<td><input class="all-options" type="text" name="api_key" value="' . $option['api_key'] . '" /></td>';
    echo '</tr>';
    echo '<tr valign="top">';
    echo '<th scope="row">Secret Key</th>';
    echo '<td><input class="all-options" type="text" name="secret_key" value="' . $option['secret_key'] . '" /></td>';
    echo '</tr>';
    echo '<tr valign="top">';
    echo '<th scope="row">是否过滤博主</th>';
    echo '<td><label><input value="true" type="checkbox" name="check_me" ' . $check_me . '> 勾选后会跳过博主评论内容，不去验证。</label></td>';
    echo '</tr>';
    echo '<tr valign="top">';
    echo '<th scope="row">是否删除配置信息</th>';
    echo '<td><label><input value="true" type="checkbox" name="delete" ' . $delete . '> 勾选后停用插件会删除保存的配置信息，减少数据库垃圾数据！</label></td>';
    echo '</tr>';
    echo '</table>';
    echo '<p class="submit">';
    echo '<input type="submit" name="bdtc_submit" id="submit" class="button button-primary" value="保存更改" />';
    echo '</p>';
    echo '</form>';
    echo '<p><strong>使用提示</strong>：<br>
	1. AppID、API Key、Secret Key在百度Ai控制台的 <a target="_blank" href="https://console.bce.baidu.com/ai/?fromai=1#/ai/antiporn/app/list">产品服务 / 内容审核 - 应用列表</a> 创建应用后获取；<br>
	2. 百度有默认审核策略，如果误杀严重，请进入 <a target="_blank" href="https://ai.baidu.com/censoring#/strategylist">内容审核平台创建自定义规则</a> 进行修改策略；<br>
	3. 如有问题请至 <a target="_blank" href="https://github.com/sy-records/wp-baidu-textcensor">Github</a> 查看使用说明或至沈唁志博客 <a target="_blank" href="https://qq52o.me/guestbook.html">留言簿</a> 留言反馈。<br>
	</p>';
    echo '</div>';
}

function bdtc_submit_check($appId, $apiKey, $secretKey)
{
    if (!empty($appId) && !empty($apiKey) && !empty($secretKey)) {
        require_once dirname(__FILE__) . '/src/AipBase.php';
        $client = new \Luffy\TextCensor\AipBase($appId, $apiKey, $secretKey);
        $response = $client->auth();
        if (isset($response['error']) || isset($response['error_description'])) {
            return true;
        }
    }
    return false;
}

// 登录态无需验证
if (!function_exists('is_user_logged_in')) {
    require(ABSPATH . WPINC . '/pluggable.php');
}

// change comment status
function bdtc_3_4_comment_to_pending( $approved , $commentdata )
{
    $approved = 0;
    return $approved;
}

// check comment
function bdtc_refused_comments($comment_data)
{
    $option = get_option('BaiduTextCensor');
    if ($option['check_me'] && !is_user_logged_in()) {
        require_once dirname(__FILE__) . '/src/AipBase.php';
        $client = new \Luffy\TextCensor\AipBase($option['app_id'], $option['api_key'], $option['secret_key']);
            $res = $client->textCensorUserDefined($comment_data['comment_content']);
        // 1.合规，2.不合规，3.疑似，4.审核失败
        if ($res['conclusionType'] == 2) {
            wp_die("评论内容" . $res['data'][0]['msg'] . "，请重新评论");
        } elseif (in_array($res['conclusionType'], [3, 4])) {
            // 疑似和失败的写数据库，人工审核
            add_filter( 'pre_comment_approved' , 'bdtc_3_4_comment_to_pending' , '99', 2 );
        }
    }
    return( $comment_data );
}
add_filter('preprocess_comment', 'bdtc_refused_comments');
