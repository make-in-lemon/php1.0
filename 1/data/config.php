<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gbk" />
</head>
<body>
    <table border="1">
        <tr>
            <td>�û�ID</td>
            <td>�û�����</td>
        </tr>
        <tr>
            <?php
            /*
             * SAE_MYSQL_USER:�û��� 
             * SAE_MYSQL_PASS�����룺 
             * SAE_MYSQL_HOST_M����������
             * SAE_MYSQL_HOST_S���ӿ����� 
             * SAE_MYSQL_PORT���˿ڣ� 
             * SAE_MYSQL_DB���ݿ���
             * 
             * ��ϸ˵����ҳ��ı���Ҫ�����ݿ�ı���һ������Ȼ���������
             * �������������ݿ�ʱ����mysql_set_charset()
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
                echo "���ݿ�����KO";
            }
            ?>
        </tr>

    </table>
</body>
</html>