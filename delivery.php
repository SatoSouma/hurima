<?php
if(isset($_POST['id']) && $_POST['id'] !== '無し'){
    // DB接続　配送完了日の追加
    $date = date('Y/m/d');
    $date = explode('/',$date);
    $updata_date = $date[0].$date[1].$date[2];
    $updata_date = intval($updata_date);
    $link = mysqli_connect('localhost','root','','IH22');
    mysqli_set_charset($link,'utf8');
    $sql = "UPDATE delivery SET dl_completedate = ". $updata_date ." WHERE product_id = " . $_POST['id'] ;
    $result = mysqli_query($link,$sql);
    mysqli_close($link);

    // 購入者の会員id、商品の値段、サイズidの探索処理。
    $sb_message = 'プレミアサービスの利用無し';
    $link = mysqli_connect('localhost','root','','IH22');
    mysqli_set_charset($link,'utf8');
    $sql = "SELECT ls_member_id,product_price,size_id FROM product WHERE id = " . $_POST['id']  ;
    $result = mysqli_query($link,$sql);
    $row = mysqli_fetch_assoc($result);
    $member['ls_member_id'] = $row['ls_member_id'];
    $member['p_price'] = $row['product_price'];
    $member['size_id'] = $row['size_id'];

    // mail探索
    $sql = "SELECT mail FROM password WHERE pass_member_id = " . $member['ls_member_id'] ;
    $result = mysqli_query($link,$sql);
    $row = mysqli_fetch_assoc($result);
    $member['mail'] = $row['mail'];
    // ユーザーネームの探索
    $sql = "SELECT user_name FROM member WHERE id = " . $member['ls_member_id'] ;
    $result = mysqli_query($link,$sql);
    $row = mysqli_fetch_assoc($result);
    $member['user_name'] = $row['user_name'];
    // サイズ料金の探索
    $sql = "SELECT price FROM size WHERE id = " . $member['size_id'] ;
    $result = mysqli_query($link,$sql);
    $row = mysqli_fetch_assoc($result);
    $member['s_price'] = $row['price'];
    mysqli_close($link);
    // サイズが60サイズ未満の時プレミア会員の探索
    if($member['size_id'] == 1){
        $link = mysqli_connect('localhost','root','','IH22');
        mysqli_set_charset($link,'utf8');
        $sql = "SELECT sb_flug FROM subscription WHERE member_id = " . $member['ls_member_id'] . " AND sb_flug = 0 ";
        $result = mysqli_query($link,$sql);
        $row = mysqli_fetch_assoc($result);
        mysqli_close($link);
        // 出品者がプレミア会員でプレミアサービス利用フラグが0だった時
        if(isset($row)){
            // 無料
            $member['s_price'] = 0;
            $sb_message = 'プレミアサービスを利用しました。(60サイズ未満無料)';
            // プレミアサービス利用フラグに1を入れる。
            $link = mysqli_connect('localhost','root','','IH22');
            mysqli_set_charset($link,'utf8');
            $sql = "UPDATE subscription SET sb_flug = 1 WHERE member_id = " . $member['ls_member_id'] ;
            $result = mysqli_query($link,$sql);
            mysqli_close($link);
        }else{
            $sb_message = '今月は既に1度プレミアサービスを利用しています。';
        }
    }
    // 利用サービスの探索
    $link = mysqli_connect('localhost','root','','IH22');
    mysqli_set_charset($link,'utf8');
    $sql = "SELECT service_id,st_progresscnt FROM serviceSelect WHERE product_id = ".$_POST['id'];
    $result = mysqli_query($link,$sql);
    while($row = mysqli_fetch_assoc($result)){
        $calc_info[] = $row;
    }

    // 料金計算
    $pack = 0;
    $store = 0;
    $pickup = 0;
    if(isset($calc_info)){
        // サービス料金の探索
        foreach($calc_info as $key => $val){
            $sql = "SELECT calculation FROM service WHERE id = " . $val['service_id'];
            $result = mysqli_query($link,$sql);
            $row = mysqli_fetch_assoc($result);
            $calc_info[$key]['calculation'] = $row['calculation'];
        }
        mysqli_close($link);
        // サービス料の計算
        foreach($calc_info as $val){
            // 梱包料
            if($val['service_id'] <= 8 && $val['service_id'] >= 2){
                $pack = $val['calculation'];
            }
            // 初回保管料
            if($val['service_id'] <= 15 && $val['service_id'] >= 9){
                $store = $val['calculation'];
            }
            // 集荷料
            if($val['service_id'] == 1){
                $pickup = floor($member['p_price'] * ($val['calculation'] / 100));
            }
        }
    }

    // 手数料
    $fee = floor($member['p_price'] * 0.1);

    echo 'To'.':'.$member['mail'] ; ?><br><?php
    echo $member['user_name'].'様、'.'下記の入金が完了致しました。'; ?><br><br><?php

    echo $sb_message;?><br><?php
    echo '商品の値段'.':' . $member['p_price'] . '円';?><br><?php
    echo '-手数料'.':'. $fee . '円';?><br><?php
    echo '-梱包料'.':' . $pack . '円';?><br><?php
    echo '-集荷料'.':' . $pickup . '円';?><br><?php
    echo '-初月保管料'.':' . $store . '円';?><br><?php
    echo '-サイズ料'.':' . $member['s_price'] . '円';?><br><?php
    echo '-----------------------------------------------------------';?><br><?php
    echo '合計'. ($member['p_price'] - ($fee + $pack + $pickup + $store + $member['s_price'])) . '円<br><hr>';?><br><br><?php
    // メール送信
    // mb_language("Japanese");
    // mb_internal_encoding("UTF-8");
    // $to = 'souma6883@outlook.jp';
    // $subject = "TEST";
    // $message = "This is TEST.\r\nHow are you?";
    // $headers = "From: from@example.com";
    // mb_send_mail($to, $subject, $message, $headers);
}


// 上記の条件で尚且つ配送日、配送時間が決まっている配送テーブルの商品id,送付先id,配送日,配送時間を探索
$link = mysqli_connect('localhost','root','','IH22');
mysqli_set_charset($link,'utf8');

$sql = "SELECT product_id,destination_id,delivery_date,delivery_time FROM delivery WHERE delivery_date IS NOT NULL AND dl_completedate IS NULL";
$result = mysqli_query($link,$sql);
while($row = mysqli_fetch_assoc($result)){
    $id_info[] = $row;
}
mysqli_close($link);

if(isset($id_info)){
    // 探し出した商品idで商品テーブルの商品名を探索。
    $link = mysqli_connect('localhost','root','','IH22');
    mysqli_set_charset($link,'utf8');
    foreach($id_info as $key => $val){
        $sql = "SELECT product_name FROM product WHERE id = " . $id_info[$key]['product_id'] ;
        $result = mysqli_query($link,$sql);
        $row = mysqli_fetch_assoc($result);
        $id_info[$key]['product_name'] = $row['product_name'];
    }
    mysqli_close($link);

    // 探索した送付先idで送付先テーブルの住所と郵便番号の探索。
    $link = mysqli_connect('localhost','root','','IH22');
    mysqli_set_charset($link,'utf8');
    foreach($id_info as $key => $val){
        $sql = "SELECT address,postal_num FROM destination WHERE id = " . $id_info[$key]['destination_id'] ;
        $result = mysqli_query($link,$sql);
        $row = mysqli_fetch_assoc($result);
        $id_info[$key]['address'] = $row['address'];
        $id_info[$key]['postal_num'] = $row['postal_num'];
        // 郵便番号上三桁と下四桁を分けて間に'-'を入れる。
        $post_first = substr($id_info[$key]['postal_num'], 0, 3);
        $post_seccond = substr($id_info[$key]['postal_num'], 3, 6);
        $id_info[$key]['postal_num'] = $post_first . '-' . $post_seccond;
    }
    mysqli_close($link);
}else{
    $id_info[0]['product_id'] = '無し';
    $id_info[0]['destination_id'] = '無し';
    $id_info[0]['delivery_date'] = '無し';
    $id_info[0]['delivery_time'] = '無し';
    $id_info[0]['product_name'] = '無し';
    $id_info[0]['address'] = '無し';
    $id_info[0]['postal_num'] = '無し';
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
<link rel="stylesheet" href="css/delivery.css">
<link rel="stylesheet" href="css/all.css">
<body>
<!-- <header>
    <p><img src=""></p>
  </header> -->

  <main>
    <div class="main">
    <table class="container tbale table-bordered">
        <thead class="thead-dark">
            <tr class="d-flex">
                <th class="col-2 text-center bg-dark text-white">商品名</th>
                <th class="col-2 text-center bg-dark text-white">郵便番号</th>
                <th class="col-2 text-center bg-dark text-white">住所</th>
                <th class="col-2 text-center bg-dark text-white">配送日</th>
                <th class="col-2 text-center bg-dark text-white">配送時間</th>
                <th class="col-2 text-center bg-dark text-white">配送完了</th>
            </tr>
        </thead>
        <?php foreach($id_info as $val){ ?>
        <tr class="d-flex">
            <td class="col-2 text-center"><?php echo $val['product_name']; ?></td>
            <td class="col-2 text-center"><?php echo $val['postal_num']; ?></td>
            <td class="col-2 text-center"><?php echo $val['address']; ?></td>
            <td class="col-2 text-center"><?php echo $val['delivery_date']; ?></td>
            <td class="col-2 text-center"><?php echo $val['delivery_time']; ?></td>
            <td class="col-2 d-flex justify-content-center align-items-center"><form action="" method="POST"><button class="btn btn-primary" type="submit" name="id" value=<?php echo $val['product_id'] ?>>配送完了</button></form></td>
        </tr>
        <?php } ?>
    </table>
    </div>
  </main>
</body>
</html>
