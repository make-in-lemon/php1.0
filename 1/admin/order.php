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

// 载入并实例化类库
require (ROOT_PATH . 'include/order.class.php');
$dou_order = new Order();

// 赋值给模板
$smarty->assign('rec', $rec);
$smarty->assign('cur', 'order');

/**
 * +----------------------------------------------------------
 * 订单列表
 * +----------------------------------------------------------
 */
if ($rec == 'default') {
    $smarty->assign('ur_here', $_LANG['order']);

    // 生成筛选条件
    $field = array('order_sn', 'telphone', 'contact');
    foreach ($field as $value) {
        $v['value'] = $value;
        $v['name'] = $_LANG['order_' . $value];
        $v['cur'] = ($value == $_REQUEST['key']) ? true : false;
        $key[] = $v;
    }

    // 筛选关键词
    $keyword = isset($_REQUEST['keyword']) ? trim($_REQUEST['keyword']) : '';
    if ($keyword)
        $where = " WHERE $_REQUEST[key] LIKE '%$keyword%'";
    
    // 分页
    $page = $check->is_number($_REQUEST['page']) ? $_REQUEST['page'] : 1;
    $page_url = 'order.php' . ($_REQUEST['key'] ? '?key=' . $_REQUEST['key'] : '') . ($keyword ? '&keyword=' . $keyword : '');
    $limit = $dou->pager('order', 15, $page, $page_url, $where);
    
    $query = $dou->query("SELECT * FROM " . $dou->table('order') . $where . " ORDER BY order_id DESC" . $limit);
    while ($row = $dou->fetch_array($query)) {
        $email = $dou->get_one("SELECT email FROM " . $dou->table('user') . " WHERE user_id = '$row[user_id]'");
        $add_time = date("Y-m-d", $row['add_time']);
        $status_format = $_LANG['order_status_' . $row['status']];
        $order_amount = $dou->price_format($row['order_amount']);
        
        $order_list[] = array (
                "order_id" => $row['order_id'],
                "order_sn" => $row['order_sn'],
                "email" => $email,
                "telphone" => $row['telphone'],
                "contact" => $row['contact'],
                "order_amount" => $order_amount,
                "status" => $row['status'],
                "status_format" => $status_format,
                "add_time" => $add_time
        );
    }
    
    // 赋值给模板
    $smarty->assign('key', $key);
    $smarty->assign('keyword', $keyword);
    $smarty->assign('order_list', $order_list);
    
    $smarty->display('order.htm');
} 

/**
 * +----------------------------------------------------------
 * 订单编辑
 * +----------------------------------------------------------
 */
elseif ($rec == 'view') {
    $smarty->assign('ur_here', $_LANG['order_view']);
    $smarty->assign('action_link', array (
            'text' => $_LANG['order_list'],
            'href' => 'order.php' 
    ));
    
    // 验证并获取合法的ID
    $order_id = $check->is_number($_REQUEST['order_id']) ? $_REQUEST['order_id'] : '';

    // 获取配送方式
    $shipping = $dou->get_plugin('shipping');
    
    $query = $dou->select($dou->table('order'), '*', '`order_id` = \'' . $order_id . '\'');
    $order = $dou->fetch_array($query);
    $order['pay_name'] = $dou->get_one("SELECT name FROM " . $dou->table('plugin') . " WHERE unique_id = '$order[pay_id]'");
    $order['shipping_name'] = $dou->get_one("SELECT name FROM " . $dou->table('plugin') . " WHERE unique_id = '$order[shipping_id]'");
    $order['product_amount_format'] = $dou->price_format($order['product_amount']);
    $order['shipping_fee_format'] = $dou->price_format($order['shipping_fee']);
    $order['order_amount_format'] = $dou->price_format($order['order_amount']);

    /* 获取产品列表 */
    $query = $dou->query("SELECT product_id, name, price, product_number, defined FROM " . $dou->table('order_product') . " WHERE order_id = '$order_id' ORDER BY id DESC");

    while ($row = $dou->fetch_array($query)) {
        // 格式化价格
        $price = $dou->price_format($row['price']);
        
        $product_list[] = array (
                "product_id" => $row['product_id'],
                "name" => $row['name'],
                "product_number" => $row['product_number'],
                "url" => $dou->rewrite_url('product', $row['product_id']),
                "price" => $price,
                "defined" => $defined
        );
    }
    
    // 格式化订单信息
    $order['email'] = $dou->get_one("SELECT email FROM " . $dou->table('user') . " WHERE user_id = '$order[user_id]'");
    $order['add_time'] = date("Y-m-d", $order['add_time']);
    $order['status_format'] = $_LANG['order_status_' . $order['status']];
    $order['product_list'] = $product_list;
    
    // 赋值给模板
    $smarty->assign('order', $order);
    $smarty->assign('shipping_list', $dou_order->get_shipping_list());
    
    $smarty->display('order.htm');
} 

/**
 * +----------------------------------------------------------
 * 快递单号填写
 * +----------------------------------------------------------
 */
elseif ($rec == 'tracking') {
    // 插入订单号并在订单状态设定为已发货
    $dou->query("UPDATE " . $dou->table('order') . " SET shipping_id = '$_POST[shipping_id]', tracking_no = '$_POST[tracking_no]', status = '10' WHERE order_id = '$_POST[order_id]'");
    
    $dou->dou_header('order.php?rec=view&order_id=' . $_POST['order_id']);
} 

/**
 * +----------------------------------------------------------
 * 订单删除
 * +----------------------------------------------------------
 */
elseif ($rec == 'del') {
    // 验证并获取合法的ID
    $order_id = $check->is_number($_REQUEST['order_id']) ? $_REQUEST['order_id'] : '';
    $order_sn = $dou->get_one("SELECT order_sn FROM " . $dou->table('order') . " WHERE order_id = '$order_id'");
    
    if (isset($_POST['confirm']) ? $_POST['confirm'] : '') {
        $dou->create_admin_log($_LANG['order_del'] . ': ' . $order_sn);
        $dou->delete($dou->table('order'), "order_id = $order_id", 'order.php');
    } else {
        $_LANG['del_check'] = preg_replace('/d%/Ums', $order_sn, $_LANG['del_check']);
        $dou->dou_msg($_LANG['del_check'], 'order.php', '', '30', "order.php?rec=del&order_id=$order_id");
    }
} 

/**
 * +----------------------------------------------------------
 * 批量操作选择
 * +----------------------------------------------------------
 */
elseif ($rec == 'action') {
    if (is_array($_POST['checkbox'])) {
        if ($_POST['action'] == 'del_all') {
            // 批量订单删除
            $dou->del_all('order', $_POST['checkbox'], 'order_id');
        } elseif ($_POST['action'] == 'cancel_all') {
            // 订单批量删除
            $dou_order->cancel_all($_POST['checkbox']);
        } else {
            $dou->dou_msg($_LANG['select_empty']);
        }
    } else {
        $dou->dou_msg($_LANG['order_select_empty']);
    }
}
?>