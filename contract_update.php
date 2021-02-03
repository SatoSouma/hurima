<?php
// $_POST['sbsc']が送られてきたときプレミア契約を更新する
if(isset($_POST['sbsc'])){
    // サブスクテーブルのdelete_dateがないidを探索
    $link = mysqli_connect('localhost','root','','IH22');
    mysqli_set_charset($link,'utf8');
    $sql = "SELECT member_id FROM subscription WHERE delete_date IS NULL" ;
    $result = mysqli_query($link,$sql);
    while($row = mysqli_fetch_assoc($result)){
        $all[] = $row['member_id'];
    }
    mysqli_close($link);

    // 探し出したidのsb_flugを0にする。
    foreach($all as $val){
        $link = mysqli_connect('localhost','root','','IH22');
        mysqli_set_charset($link,'utf8');
        $sql = "UPDATE subscription SET sb_flug = 0 WHERE member_id = " . $val ;
        $result = mysqli_query($link,$sql);
        mysqli_close($link);
    }
}

// $_POST['sbsc']が送られてきたとき保管サービス契約を更新する
if(isset($_POST['store'])){
    // 購入フラグのないidをproductから探す。
    $link = mysqli_connect('localhost','root','','IH22');
    mysqli_set_charset($link,'utf8');
    $sql = "SELECT id FROM product WHERE purchase_flug = 0 " ;
    $result = mysqli_query($link,$sql);
    while($row = mysqli_fetch_assoc($result)){
        $all[] = $row;
    }
    mysqli_close($link);

    // サービス選択テーブルにあるservice_idが9~15であるもののproduct_idを探し出す。
    foreach($all as $val){
        $link = mysqli_connect('localhost','root','','IH22');
        mysqli_set_charset($link,'utf8');
        // 何か知らんけど1になる。
        $sql = "SELECT product_id FROM serviceSelect WHERE service_id >= 9 AND product_id = '" . $val['id'] . "'" ;
        $result = mysqli_query($link,$sql);
        $row = mysqli_fetch_assoc($result);  
        mysqli_close($link);
        $up[]['product_id'] = $row['product_id']; 
    }

    // 探し出したproduct_idの経過フラグを全て+1する。(お金を引き落とす)
    foreach($up as $val){
        $link = mysqli_connect('localhost','root','','IH22');
        mysqli_set_charset($link,'utf8');
        // service_idが9~15の時だけ。
        if($val['product_id'] !== NULL){
            $sql = "UPDATE serviceSelect SET st_progresscnt = st_progresscnt + 1 WHERE product_id = '" . $val['product_id'] . "'" ;
            $result = mysqli_query($link,$sql);
            mysqli_close($link);
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/contract_update.css">
<link rel="stylesheet" href="css/all.css">
<body>
<form action="" method="POST">
    <div class="main text-center">
        <button class="btn btn-primary" type="submit" name='sbsc'>プレミア会員契約更新</button><br>
        <button class="btn btn-primary" type="submit" name='store'>保管サービス契約更新</button>
    </div>
</form>
</body>
</html>