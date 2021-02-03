<?php 
// 商品DBから集荷日が入力されており集荷完了日が登録されていない商品id、商品名、と集荷完了ボタンをテーブルで表示

// 集荷完了ボタンを押すと商品idに対応した集荷完了日に日が入る。
if(isset($_POST['id']) && $_POST['id'] !== '無し'){


    $link = mysqli_connect('localhost','root','','IH22');
    mysqli_set_charset($link,'utf8');

    // 購入フラグの確認
    $sql = "SELECT purchase_flug FROM product WHERE id = " . $_POST['id'] ;
    $result = mysqli_query($link,$sql);
    $row_p = mysqli_fetch_assoc($result);
    
    // DB接続　集荷完了日の追加
    $date = date('Y/m/d');
    $date = explode('/',$date);
    $updata_date = $date[0].$date[1].$date[2];
    $updata_date = intval($updata_date);
    $sql = "UPDATE pickup SET pu_completedate = ". $updata_date ." WHERE product_id = " . $_POST['id'] ;
    $result = mysqli_query($link,$sql);

    if($row_p['purchase_flug'] == 1){
        // 配送準備の完了通知。(入金を促す)
        // 購入者の会員idの探索処理。
        $sql = "SELECT pu_member_id FROM transaction WHERE tr_product_id = ". $_POST['id'] ;
        $result = mysqli_query($link,$sql);
        $row = mysqli_fetch_assoc($result);
        $member['pu_member_id'] = $row['pu_member_id'];

        $sql = "SELECT mail FROM password WHERE pass_member_id = " . $member['pu_member_id'] ;
        $result = mysqli_query($link,$sql);
        $row = mysqli_fetch_assoc($result);
        $member['mail'] = $row['mail'];

        $sql = "SELECT user_name FROM member WHERE id = " . $member['pu_member_id'] ;
        $result = mysqli_query($link,$sql);
        $row = mysqli_fetch_assoc($result);
        $member['user_name'] = $row['user_name'];
        mysqli_close($link);

        echo 'To'.':'.$member['mail'] ; ?><br><?php
        echo $member['user_name'].'様、'.'配送の準備が整いましたので、ご入金の程よろしくお願いします。'; ?><br><?php
        echo '↓疑似入金処理に飛ぶurl'; ?><br><?php
        ?> <p><a href="./sttlement_date.php?id=<?php echo $_POST['id'] ?>">URL</a></p> <?php
    }
    // メール送信
    // mb_language("Japanese");
    // mb_internal_encoding("UTF-8");
    // $to = 'souma6883@outlook.jp';
    // $subject = "TEST";
    // $message = "This is TEST.\r\nHow are you?";
    // $headers = "From: from@example.com";
    // mb_send_mail($to, $subject, $message, $headers); 
}

// DB接続 商品id,商品名,購入者id,出品者id,購入者名,出品者名の取得。
$link = mysqli_connect('localhost','root','','IH22');
mysqli_set_charset($link,'utf8');
$sql = "SELECT pi.product_id,pr.product_name,pr.ls_member_id FROM pickup AS pi 
INNER JOIN product AS pr ON pi.product_id = pr.id 
WHERE pi.pu_completedate IS NULL AND pi.pickup_date IS NOT NULL";
$result = mysqli_query($link,$sql);
while($row = mysqli_fetch_assoc($result)){
    $all[] = $row;
}

// 出品者名取り出し
if(isset($all)){
    foreach($all as $key =>$val){
        $val['ls_member_id'] = intval($val['ls_member_id']);
        $sql = "SELECT member_name FROM member  
        WHERE id = " . $val['ls_member_id'] ;
        $result = mysqli_query($link,$sql);
        $row = mysqli_fetch_assoc($result);
        $all[$key]['ls_member_name'] = $row['member_name'];
    }
    mysqli_close($link);
}else{
    $all[0]['product_id'] = '無し';
    $all[0]['product_name'] = '無し';
    $all[0]['ls_member_name'] = '無し';
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
<link rel="stylesheet" href="css/receive_product.css">
<link rel="stylesheet" href="css/all.css">
<body>
<table class="container tbale table-bordered">
    <thead class="thead-dark">
        <tr class="d-flex"><th class="col-4 bg-dark text-white text-center">商品id</th><th class="col-3 bg-dark text-white text-center">商品名</th></th><th class="col-3 bg-dark text-white text-center">出品者名</th></th><th class="col-2 bg-dark text-white text-center">集荷完了</th></tr>
    </thead>
    <?php foreach($all as $val){ ?>
    <tr class="d-flex">
        <td class="col-4"><?php echo $val['product_id'] ?></td>
        <td class="col-3"><?php echo $val['product_name'] ?></td>
        <td class="col-3"><?php echo $val['ls_member_name'] ?></td>
        <td class="col-2"><div class="text-center"><form action="" method='POST'><button class="btn btn-primary" type='submit' value=<?php echo $val['product_id']?> name='id'>集荷完了</button></form></div></td>
    </tr>
    <?php } ?>
</table>

</body>
</html>