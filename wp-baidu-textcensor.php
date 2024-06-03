<?php
/*
Plugin Name:  Baidu TextCensor For Comments
Plugin URI:   https://github.com/sy-records/wp-baidu-textcensor
Description:  基于百度文本内容审核技术来提供WordPress评论内容审核
Version:      1.1.1
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
            'check_roles' => '',
        );
        //更新选项
        update_option('BaiduTextCensor', $default);
    }
}

// stop plugin
function bdtc_stop_option()
{
    $option = get_option('BaiduTextCensor');
    if ($option['delete']) {
        delete_option("BaiduTextCensor");
    }
}

register_deactivation_hook(__FILE__, 'bdtc_stop_option');

// setting plugin
add_action('admin_menu', 'bdtc_submit_menu');
function bdtc_submit_menu()
{
    add_submenu_page('options-general.php', '评论内容审核设置', '评论内容审核设置', 'manage_options', 'Baidu_Text_Censor', 'bdtc_submit_options');
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

function bdtc_get_user_roles()
{
    $res = [];

    $editable_roles = array_reverse( get_editable_roles() );
    foreach ( $editable_roles as $role => $details ) {
        $res[$role] = translate_user_role( $details['name'] );
    }

    return $res;
}

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
        $check_roles = isset($_POST['check_roles']) ? implode(',', $_POST['check_roles']) : '';

        $check_status = bdtc_submit_check($app_id, $api_key, $secret_key);
        if ($check_status) {
            echo '<div class="error" id="message"><p>获取Access Token失败，请检查参数</p></div>';
        } else {
            $bdtcOption = array(
                'app_id' => $app_id,
                'api_key' => $api_key,
                'secret_key' => $secret_key,
                'check_me' => $check_me,
                'delete' => $delete,
                'check_roles' => $check_roles,
            );
            $res = update_option('BaiduTextCensor', $bdtcOption);//更新选项
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
    $roles = bdtc_get_user_roles();
    $check_roles = explode(',', $option['check_roles']);
    echo '<div class="wrap">';
    echo '<h2>评论内容审核设置</h2>';
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
    echo '<th scope="row">跳过登录态验证</th>';
    echo '<td><label><input value="true" type="checkbox" name="check_me" ' . $check_me . '> 勾选后如果是登录态则会跳过该用户评论内容，不去验证</label></td>';
    echo '</tr>';
    echo '<tr valign="top">';
    echo '<th scope="row">需要验证的登录角色</th>';

    echo '<td>';
    foreach($roles as $role => $name) {
        $check = '';
        if (in_array($role, $check_roles)) {
            $check = 'checked="checked"';
        }
        echo '<input type="checkbox" name="check_roles[]" value="' . $role . '" ' . $check . '>' . $name . '<br>';
    }
    echo '<br><label>选择需要在登录态下验证的角色，选择后即使选择了<strong>跳过登录态验证</strong>，属于该角色的用户评论也会进行验证</label>';
    echo '</td>';

    echo '</tr>';
    echo '<tr valign="top">';
    echo '<th scope="row">是否删除配置信息</th>';
    echo '<td><label><input value="true" type="checkbox" name="delete" ' . $delete . '> 勾选后停用插件时会删除保存的配置信息</label></td>';
    echo '</tr>';
    echo '</table>';
    echo '<p class="submit">';
    echo '<input type="submit" name="bdtc_submit" id="submit" class="button button-primary" value="保存更改" />';
    echo '</p>';
    echo '</form>';
    echo '<p><strong>使用提示</strong>：<br>
	1. AppID、API Key、Secret Key在百度 AI 控制台的 <a target="_blank" href="https://console.bce.baidu.com/ai/?fromai=1#/ai/antiporn/app/list">产品服务 / 内容审核 - 应用列表</a> 创建应用后获取；<br>
	2. 百度有默认审核策略，如果误杀严重，请进入 <a target="_blank" href="https://ai.baidu.com/censoring#/strategylist">内容审核平台创建自定义规则</a> 进行修改策略；<br>
	3. 如有问题请至 <a target="_blank" href="https://github.com/sy-records/wp-baidu-textcensor">GitHub</a> 查看使用说明或至沈唁志博客 <a target="_blank" href="https://qq52o.me/2720.html">插件发布页</a> 留言反馈。<br>
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

// check comment
function bdtc_refused_comments($comment_data)
{
    $option = get_option('BaiduTextCensor');
    // 跳过登录态验证
    if ($option['check_me']) {
        if (!is_user_logged_in()) { // 没登录
            bdtc_request_check($option, $comment_data);
        } else { // 登录
            $roles = explode(',', $option['check_roles']);
            global $current_user;
            foreach ($roles as $role) {
                if(in_array($role, $current_user->roles)){
                    bdtc_request_check($option, $comment_data);
                    break;
                }
            }
        }
    } else {
        bdtc_request_check($option, $comment_data);
    }
    return $comment_data;
}
add_filter('preprocess_comment', 'bdtc_refused_comments');

function bdtc_request_check($option, $comment_data)
{
    require_once dirname(__FILE__) . '/src/AipBase.php';
    $client = new \Luffy\TextCensor\AipBase($option['app_id'], $option['api_key'], $option['secret_key']);
    $result = $client->textCensorUserDefined($comment_data['comment_content'], $comment_data['comment_author_email'], $comment_data['comment_author_IP']);

    if (isset($result['error_code'])) {
        add_filter('pre_comment_approved' , '__return_zero');
        return;
    }

    // 1.合规，2.不合规，3.疑似，4.审核失败
    switch ($result['conclusionType']) {
        case 2:
            wp_die("评论内容{$result['data'][0]['msg']}，请重新评论", 409);
            break;
        case 3:
        case 4:
            // 疑似和失败的写数据库，人工审核
            add_filter('pre_comment_approved' , '__return_zero');
            break;
    }
}
