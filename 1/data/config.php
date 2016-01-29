<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gbk" />
</head>
<body>
    <table border="1">
        <tr>
            <td>用户ID</td>
            <td>用户名称</td>
        </tr>
        <tr>
            <?php
            /*
             * SAE_MYSQL_USER:用户名 
             * SAE_MYSQL_PASS：密码： 
             * SAE_MYSQL_HOST_M：主库域名
             * SAE_MYSQL_HOST_S：从库域名 
             * SAE_MYSQL_PORT：端口： 
             * SAE_MYSQL_DB数据库名
             * 
             * 详细说明：页面的编码要和数据库的编码一样，不然会出现乱码
             * 或者在连接数据库时设置mysql_set_charset()
             * 
             */
            $link = mysql_connect ( SAE_MYSQL_HOST_M . ':' . SAE_MYSQL_PORT, SAE_MYSQL_USER, SAE_MYSQL_PASS );
            if ($link) {
                mysql_select_db ( SAE_MYSQL_DB, $link );
                mysql_set_charset("gbk");
                $sql = "select UID,UNAME from Base_User";
                $result = mysql_query ( $sql );
                while ( $row = mysql_fetch_array ( $result, MYSQL_NUM ) ) {
                    echo ("<td>$row[0]</td><td>$row[1]</td>");
                }
                mysql_free_result ( $result );
            } else {
                echo "数据库连接KO";
            }
            ?>
        </tr>

    </table>
</body>
</html>