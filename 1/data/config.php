<?php
/**
 * DouPHP
 * --------------------------------------------------------------------------------------------------
 * ��Ȩ���� 2013-2014 ���ݶ�������Ƽ����޹�˾������������Ȩ����
 * ��վ��ַ: http://www.douco.com
 * --------------------------------------------------------------------------------------------------
 * �ⲻ��һ�������������ֻ����������ȨЭ��ǰ���¶Գ����������޸ĺ�ʹ�ã�������Գ���������κ���ʽ�κ�Ŀ�ĵ��ٷ�����
 * ��ȨЭ�飺http://www.douco.com/license.html
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
	if(!$con){echo "����!" .mysql_error();}
	
	//mysql_select_db("users",$con);  //���ݿ�����

	mysql_query("SET character_set_connection=utf8, character_set_results=utf8, character_set_client=utf8", $con);
	mysql_query("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO'", $con);



?>