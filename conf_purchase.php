<?php
//購入時ーコンビニ集荷(no_service,梱包)の時にpickupのdate,timeをコンビニ
//集荷のみと集荷梱包時はurl getでproduct_id.pickup_time.php

session_start();

$date1 = date("Y");
$date2 = date("m");
$date3 = date("d");
$date_comp = $date1.'-'.$date2.'-'.$date3;

$product_id = $_SESSION['product_id'];
$member_id = $_SESSION['member_id'];
//$delv_date = $_SESSION['delv_date'];
//$delv_time = $_SESSION['delv_time'];
$flg_data = $_SESSION['flg_data'];
$payment = 'コンビニ支払い';
$mail_flg = 0;
$pu_cn = 0;

$ser_id = substr($product_id,0,3);
// var_dump($ser_id);

//新規住所＆コンビニ
if ($flg_data == 3) {
  $new_postal = $_SESSION['new_postal'];
  $new_address = $_SESSION['new_address'];
}
//登録住所＆クレカ
if ($flg_data == 4) {
  $card_num = $_SESSION['card_num'];
  $card_name = $_SESSION['card_name'];
  $card_deadline = $_SESSION['card_deadline'];
  //$card_secnum = $_SESSION['card_secnum'];
  $payment = 'クレジットカード';
}
//新規住所＆クレカ
if ($flg_data == 5) {
  $new_postal = $_SESSION['new_postal'];
  $new_address = $_SESSION['new_address'];
  $card_num = $_SESSION['card_num'];
  $card_name = $_SESSION['card_name'];
  $card_deadline = $_SESSION['card_deadline'];
  //$card_secnum = $_SESSION['card_secnum'];
  $payment = 'クレジットカード';
}
//倉庫割当
$pref_array = [0,1,2,2,2,2,2,2,4,4,4,4,4,4,4,3,3,3,3,3,3,3,3,3,5,5,5,5,5,5,5,7,7,7,7,7,6,6,6,6,8,8,8,8,8,8,8];
$pref_num = substr($member_id,0,2);
$pref_int = intval($pref_num);
$storehouse_id = $pref_array[$pref_int];

//DB接続
$link=mysqli_connect('localhost','root','','ih22');
mysqli_set_charset($link,'utf8');

//登録住所取得
if ($flg_data == 2 || $flg_data == 4) {
  $result = mysqli_query($link,"SELECT postal_num,address FROM member WHERE id = $member_id;");
  $or_add = mysqli_fetch_assoc($result);
  $new_postal = $or_add['postal_num'];
  $new_address = $or_add['address'];
}

//購入ボタンが押された時
if (isset($_POST['buy'])) {

  // 集荷完了日が存在するか確認
  $sql = "SELECT pu_completedate FROM pickup WHERE product_id = '" . $product_id . "'";
  $result = mysqli_query($link,$sql);
  $row_cn = mysqli_fetch_assoc($result);
  if($row_cn['pu_completedate'] !== NULL){
    $pu_cn++;
  }

  //productにflg挿入
  mysqli_query($link,"UPDATE product SET purchase_flug = 1 WHERE id = $product_id;");

  //destination挿入
  mysqli_query($link,"INSERT INTO destination (address,postal_num,member_id)
  VALUES ('$new_address','$new_postal',$member_id);");
  $result = mysqli_query($link,"SELECT MAX(id) AS maxid FROM destination;");
  $dist_data = mysqli_fetch_assoc($result);
  $dist_id = $dist_data['maxid'];

  //delivery挿入
  mysqli_query($link,"INSERT INTO delivery (storehouse_id,destination_id,product_id,delivery_date,delivery_time,dl_completedate)
  VALUES ($storehouse_id,$dist_id,$product_id,NULL,NULL,NULL);");

  //transaction挿入
  if($payment == 'クレジットカード'){
    $dead_line_array = explode("-",$card_deadline);
    $year = substr($dead_line_array[0],2,2);
    $card_dead = $dead_line_array[1].$year;
    mysqli_query($link,"INSERT INTO transaction (tr_product_id,purchase_date,card_num,card_date,card_name,pu_member_id)
    VALUES ($product_id,'$date_comp',$card_num,'$card_dead','$card_name',$member_id);");
  }
  if($payment == 'コンビニ支払い'){
    mysqli_query($link,"INSERT INTO transaction (tr_product_id,purchase_date,pu_member_id)
    VALUES ($product_id,'$date_comp',$member_id);");
  }


  //集荷のみと集荷梱包時
  if ($ser_id == 100 || $ser_id == 110) {
    $mail_flg = 1;
  }

  //no_service,梱包のみ時
  if ($ser_id == 000 || $ser_id == 010) {
    $mail_flg = 2;
  }

  mysqli_close($link);
  session_destroy();
  session_start();
  $_SESSION['member_id'] = $member_id;
  $_SESSION['mail_flg'] = $mail_flg;
  // if ($mail_flg == 0) {
  //   header("Location: ./top.php");
  // }

}

 ?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>購入確認</title>
<meta content="タイトル" name="title">

</head>
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/conf_purchase.css">
<link rel="stylesheet" href="css/all.css">
<body>
  <h1 class="none">サトウのフリマ</h1>
  <!-- <header>
    <p><img></p>
  </header> -->
  <main>
    <div class="mainbox">
      <div class="main">
        <?php if($mail_flg == 0 && $pu_cn == 0): ?>
        <table class="table table-bordered">
          <!-- <tr class="d-flex"><th class="text-center col-3 bg-dark text-white">配送日</th><td class="col-9 text-center"><?php echo $delv_date; ?></td></tr>
          <tr class="d-flex"><th class="text-center col-3 bg-dark text-white">配送時間</th><td class="col-9 text-center"><?php echo $delv_time; ?></td></tr> -->
          <tr class="d-flex"><th class="text-center col-3 bg-dark text-white">お届け先郵便番号</th><td class="col-9 text-center"><?php echo $new_postal; ?></td></tr>
          <tr class="d-flex"><th class="text-center col-3 bg-dark text-white">お届け先住所</th><td class="col-9 text-center"><?php echo $new_address; ?></td></tr>
          <tr class="d-flex"><th class="text-center col-3 bg-dark text-white">支払方法</th><td class="col-9 text-center"><?php echo $payment; ?></td></tr>
          <?php if(($flg_data == 6 || $flg_data == 7) && $pn_cn == 0): ?>
          <tr class="d-flex"><th class="text-center col-3 bg-dark text-white">カード番号</th><td class="col-9 text-center"><?php echo $card_num; ?></td></tr>
          <tr class="d-flex"><th class="text-center col-3 bg-dark text-white">名義</th><td class="col-9 text-center"><?php echo $card_name; ?></td></tr>
          <tr class="d-flex"><th class="text-center col-3 bg-dark text-white">有効期限</th><td class="col-9 text-center"><?php echo $card_deadline; ?></td></tr>
          <?php endif; ?>
          <tr class="d-flex">
            <td class="col-12 text-center">
              <form action="" method="POST">
                <input class="btn btn-secondary" type="button" onclick="history.back()" value="戻る">
                <button class="btn btn-primary" type="submit" name="buy">購入</button>
              </form>
            </td>
          </tr>
        </table>

        <?php endif; ?>

        <?php if($mail_flg == 1 && $pu_cn == 0): ?>
          <div class="ok">
            <div class="okk">
          <p class="text-secondary">[仮定]出品者への通知メール</p><hr>
          <p class="text-secondary">出品している商品が購入されました。集荷情報を入力して下さい。</p>
          <p class="text-secondary"><a href="./pickup_data.php?product_id=<?php echo $product_id ?>">入力へ進む</a></p>
        </div>
        </div>
        <?php endif; ?>
        <?php if($mail_flg == 2 && $pu_cn == 0): ?>
          <div class="ok">
            <div class="okk">
          <p class="text-secondary">[仮定]出品者への通知メール</p><hr>
          <p class="text-secondary">出品している商品が購入されました。コンビニへの持ち込み日を入力して下さい。</p>
          <p class="text-secondary"><a href="./pickup_data.php?product_id=<?php echo $product_id ?>">入力へ進む</a></p>
        </div>
        </div>
        <?php endif; ?>
        <?php if($pu_cn == 1): ?>
          <div class="ok">
            <div class="okk">
          <p class="text-secondary">[仮定]購入者への通知メール</p><hr>
          <p class="text-secondary">配送の準備が整いました。入金を完了させてください。</p>
          <p class="text-secondary"><a href="./sttlement_date.php?id=<?php echo $product_id ?>">入金へ進む</a></p>
        </div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </main>
</body>
</html>
