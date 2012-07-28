<?php

$up_tmp_pass = '/var/www/html/shinrei/cake/app/webroot/img/1111.txt';

$string = "PHPwrite//".$_FILES['upload']['tmp_name']; // 書き込みたい文字列を変数に格納
$fp = fopen($up_tmp_pass, "w"); // 新規書き込みモードで開く
@fwrite( $fp, $string, strlen($string) ); // ファイルへの書き込み
fclose($fp);


  echo '-------------------------------------->';
    if ( is_uploaded_file( $_FILES['upload']['tmp_name'] ) ) {
        echo $_POST['now'] . 'にアップロードされたファイルです';
    } else {
        echo $_POST['now'] . 'にアップロードされたファイルではありません';
    }
    echo '<--------------------------------------';
?>