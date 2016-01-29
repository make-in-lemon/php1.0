<?php
/**
 * DouPHP
 * --------------------------------------------------------------------------------------------------
 * 版权所有 2013-2015 漳州豆壳网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.douco.com
 * --------------------------------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在遵守授权协议前提下对程序代码进行修改和使用；不允许对程序代码以任何形式任何目的的再发布。
 * 授权协议：http://www.douco.com/license.html
 * --------------------------------------------------------------------------------------------------
 * Author: DouCo
 * Release Date: 2015-06-10
 */
define('IN_DOUCO', true);

require (dirname(__FILE__) . '/include/init.php');

// rec操作项的初始化
$rec = $check->is_rec($_REQUEST['rec']) ? $_REQUEST['rec'] : 'default';

// 赋值给模板
$smarty->assign('rec', $rec);
$smarty->assign('cur', 'user');

/**
 * +----------------------------------------------------------
 * 会员列表
 * +----------------------------------------------------------
 */
if ($rec == 'default') {
    $smarty->assign('ur_here', $_LANG['user']);
    
    // 生成筛选条件
    $field = array('email', 'telphone', 'contact');
    foreach ($field as $value) {
        $v['value'] = $value;
        $v['name'] = $_LANG['user_' . $value];
        $v['cur'] = ($value == $_REQUEST['key']) ? true : false;
        $key[] = $v;
    }

    // 筛选关键词
    $keyword = isset($_REQUEST['keyword']) ? trim($_REQUEST['keyword']) : '';
    if ($keyword)
        $where = " WHERE $_REQUEST[key] LIKE '%$keyword%'";
    
    // 分页
    $page = $check->is_number($_REQUEST['page']) ? $_REQUEST['page'] : 1;
    $page_url = 'user.php' . ($_REQUEST['key'] ? '?key=' . $_REQUEST['key'] : '') . ($keyword ? '&keyword=' . $keyword : '');
    $limit = $dou->pager('user', 15, $page, $page_url, $where);
    
    $sql = "SELECT * FROM " . $dou->table('user') . $where . " ORDER BY user_id DESC" . $limit;
    $query = $dou->query($sql);
    while ($row = $dou->fetch_array($query)) {
        $sex = $row['sex'] ? $_LANG['user_man'] : $_LANG['user_woman'];
        $add_time = date("Y-m-d", $row['add_time']);
        $last_login = date("Y-m-d H:i:s", $dou->get_first_log($row['last_login'], true));
        
        $user_list[] = array (
                "user_id" => $row['user_id'],
                "email" => $row['email'],
                "telphone" => $row['telphone'],
                "contact" => $row['contact'],
                "sex" => $sex,
                "add_time" => $add_time,
                "login_count" => $row['login_count'],
                "last_login" => $last_login
        );
    }
    // 判断是否安装会员导出功能
    if (file_exists(ROOT_PATH . ADMIN_PATH . '/include/PHPExcel/excel.class.php'))
        $smarty->assign('excel', true);
    
    // 赋值给模板
    $smarty->assign('key', $key);
    $smarty->assign('keyword', $keyword);
    $smarty->assign('user_list', $user_list);
    
    $smarty->display('user.htm');
} 

/**
 * +----------------------------------------------------------
 * 会员编辑
 * +----------------------------------------------------------
 */
elseif ($rec == 'edit') {
    $smarty->assign('ur_here', $_LANG['user_edit']);
    $smarty->assign('action_link', array (
            'text' => $_LANG['user'],
            'href' => 'user.php' 
    ));
    
    // CSRF防御令牌生成
    $smarty->assign('token', $firewall->set_token('user_edit'));

    // 验证并获取合法的ID
    $user_id = $check->is_number($_REQUEST['user_id']) ? $_REQUEST['user_id'] : '';
    
    $query = $dou->select($dou->table('user'), '*', '`user_id` = \'' . $user_id . '\'');
    $user = $dou->fetch_array($query);

    // 格式化自定义参数
    if ($_CFG['defined_user'] || $user['defined']) {
        $defined = explode(',', $_CFG['defined_user']);
        foreach ($defined as $row) {
            $defined_user .= $row . "：\n";
        }
        // 如果商品中已经写入自定义参数则调用已有的
        $user['defined'] = $user['defined'] ? str_replace(",", "\n", $user['defined']) : trim($defined_user);
        $user['defined_count'] = count(explode("\n", $user['defined'])) * 2;
    }
    
    // 赋值给模板
    $smarty->assign('user', $user);
    
    $smarty->display('user.htm');
} 

elseif ($rec == 'update') {
    // 验证Email
    if (!$check->is_email($_POST['email']))
        $dou->dou_msg($_LANG['user_email_cue']);
    
    // 验证密码
    if (isset($_POST['password'])) {
        if ($check->is_password($_POST['password'])) {
            $password = ", password = '" . md5($_POST['password']) . "'";
        } else {
            $dou->dou_msg($_LANG['user_password_cue']);
        }
    }

    // 格式化自定义参数
    $_POST['defined'] = str_replace("\r\n", ',', $_POST['defined']);
    
    // CSRF防御令牌验证
    $firewall->check_token($_POST['token'], 'user_edit');
    
    $sql = "UPDATE " . $dou->table('user') . " SET email = '$_POST[email]'" . $password . ", nickname = '$_POST[nickname]', telphone = '$_POST[telphone]', contact = '$_POST[contact]', address = '$_POST[address]', postcode = '$_POST[postcode]', sex = '$_POST[sex]', defined = '$_POST[defined]' WHERE user_id = '$_POST[user_id]'";
    $dou->query($sql);
    
    $dou->create_admin_log($_LANG['user_edit'] . ': ' . $_POST['user_name']);
    $dou->dou_msg($_LANG['user_edit_succes'], 'user.php?rec=edit&user_id=' . $_POST['user_id']);
} 

/**
 * +----------------------------------------------------------
 * 会员删除
 * +----------------------------------------------------------
 */
elseif ($rec == 'del') {
    // 验证并获取合法的user_id
    $user_id = $check->is_number($_REQUEST['user_id']) ? $_REQUEST['user_id'] : $dou->dou_msg($_LANG['illegal'], 'user.php');
    $email = $dou->get_one("SELECT email FROM " . $dou->table('user') . " WHERE user_id = '$user_id'");
    
    if (isset($_POST['confirm']) ? $_POST['confirm'] : '') {
        $dou->create_admin_log($_LANG['user_del'] . ': ' . $email);
        $dou->delete($dou->table('user'), "user_id = $user_id", 'user.php');
    } else {
        $_LANG['del_check'] = preg_replace('/d%/Ums', $email, $_LANG['del_check']);
        $dou->dou_msg($_LANG['del_check'], 'user.php', '', '30', "user.php?rec=del&user_id=$user_id");
    }
} 

/**
 * +----------------------------------------------------------
 * 批量操作选择
 * +----------------------------------------------------------
 */
elseif ($rec == 'action') {
    // 判断是否安装会员导出功能
    if (file_exists($excel_file = ROOT_PATH . ADMIN_PATH . '/include/PHPExcel/excel.class.php')) {
        include_once($excel_file);
        $excel = new Excel();
    }
    
    if (is_array($_POST['checkbox'])) {
        if ($_POST['action'] == 'del_all') { // 批量删除会员
            $dou->del_all('user', $_POST['checkbox'], 'user_id');
        } elseif ($_POST['action'] == 'excel') { // 导出所选会员
												$excel->export_excel($_POST['checkbox']);
        } else {
            $dou->dou_msg($_LANG['select_empty']);
        }
    } else {
        if ($_POST['action'] == 'excel_all') { // 导出所有
												$excel->export_excel();
            exit;
        }
        
        $dou->dou_msg($_LANG['user_select_empty']);
    }
}
?>