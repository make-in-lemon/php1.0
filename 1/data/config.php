<?php
/**
 * DouPHP
 * --------------------------------------------------------------------------------------------------
 * 版权所有 2013-2014 漳州豆壳网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.douco.com
 * --------------------------------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在遵守授权协议前提下对程序代码进行修改和使用；不允许对程序代码以任何形式任何目的的再发布。
 * 授权协议：http://www.douco.com/license.html
 * --------------------------------------------------------------------------------------------------
 * Author: DouCo
 * Release Date: 2014-06-05
 */

/*// database host
$dbhost   = "localhost";

// database name
$dbname   = "xm1";

// database username
$dbuser   = "root";

// database password
$dbpass   = "123456";

// table prefix
$prefix   = "dou_";

// charset
define('DOU_CHARSET','utf-8');

// administrator path
define('ADMIN_PATH','admin');

// mobile path
define('M_PATH','m');*/

	header("Content-type:text/html;charset=utf-8");
	$con=mysql_connect(SAE_MYSQL_HOST_M.":".SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);
    mysql_select_db(SAE_MYSQL_DB,$con);
	if(!$con){echo "错误!" .mysql_error();}
	
	//mysql_select_db("users",$con);  //数据库名字

	mysql_query("SET character_set_connection=utf8, character_set_results=utf8, character_set_client=utf8", $con);
	mysql_query("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO'", $con);



?>