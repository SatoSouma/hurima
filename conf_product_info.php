<?php
session_start();
$i = 1;

$member_id = $_SESSION['member_id'];
$product_id = $_SESSION['product_id'];
$product_name = $_SESSION['product_name'];
$explain = $_SESSION['explain'];
$photo_url1 = $_SESSION['photo_url1'];
if (isset($_SESSION['photo_url2'])) {
  $photo_url2 = $_SESSION['photo_url2'];
}
if (isset($_SESSION['photo_url3'])) {
  $photo_url3 = $_SESSION['photo_url3'];
}
$status = $_SESSION['status'];
$price = $_SESSION['price'];
$size = $_SESSION['size'];
$sizeid = $_SESSION['sizeid'];
$ser_pad = $_SESSION['ser_pad'];
$se_array = $_SESSION['service'];

//倉庫割当
$pref_array = [0,1,2,2,2,2,2,2,4,4,4,4,4,4,4,3,3,3,3,3,3,3,3,3,5,5,5,5,5,5,5,7,7,7,7,7,6,6,6,6,8,8,8,8,8,8,8];
$pref_num = substr($member_id,0,2);
$pref_int = intval($pref_num);
$storehouse_id = $pref_array[$pref_int];


if (isset($_POST['sub_flg'])) {

  //INSERT---------------------------------------------------
  $link=mysqli_connect('localhost','root','','ih22');
  mysqli_set_charset($link,'utf8');
  //product挿入---
   mysqli_query($link,"INSERT INTO product (id,product_name,product_price,product_status,product_explain,size_id,ls_member_id)
   VALUES ($product_id,'$product_name',$price,'$status','$explain',$sizeid,$member_id);");

  //serviceselect挿入---
  $date = date("Ymd");
  //集荷
  if($ser_pad >= '100'){
    mysqli_query($link,"INSERT INTO serviceselect (product_id,service_id,select_date,st_progresscnt,st_cancelcnt)
    VALUES ($product_id,1,$date,NULL,NULL);");
    //集荷＆保管
    if ($ser_pad == '101') {
      $service_id = $sizeid + 8;
      mysqli_query($link,"INSERT INTO serviceselect (product_id,service_id,select_date,st_progresscnt,st_cancelcnt)
      VALUES ($product_id,$service_id,$date,0,0);");
    }
    //集荷＆梱包
    if($ser_pad == '110'){
      $service_id = $sizeid + 1;
      mysqli_query($link,"INSERT INTO serviceselect (product_id,service_id,select_date,st_progresscnt,st_cancelcnt)
      VALUES ($product_id,$service_id,$date,NULL,NULL);");
    }
    //集荷＆梱包＆保管
    if($ser_pad == '111'){
      $service_id = $sizeid + 1;
      mysqli_query($link,"INSERT INTO serviceselect (product_id,service_id,select_date,st_progresscnt,st_cancelcnt)
      VALUES ($product_id,$service_id,$date,NULL,NULL);");
      $service_id = $sizeid + 8;
      mysqli_query($link,"INSERT INTO serviceselect (product_id,service_id,select_date,st_progresscnt,st_cancelcnt)
      VALUES ($product_id,$service_id,$date,0,0);");
    }
  }elseif ($ser_pad < '100') {
    //保管
    if ($ser_pad == '001') {
      $service_id = $sizeid + 8;
      mysqli_query($link,"INSERT INTO serviceselect (product_id,service_id,select_date,st_progresscnt,st_cancelcnt)
      VALUES ($product_id,$service_id,$date,0,0);");
    }
    //梱包
    if ($ser_pad == '010') {
      $service_id = $sizeid + 1;
      mysqli_query($link,"INSERT INTO serviceselect (product_id,service_id,select_date,st_progresscnt,st_cancelcnt)
      VALUES ($product_id,$service_id,$date,NULL,NULL);");
    }
    //梱包＆保管
    if ($ser_pad == '011') {
      $service_id = $sizeid + 1;
      mysqli_query($link,"INSERT INTO serviceselect (product_id,service_id,select_date,st_progresscnt,st_cancelcnt)
      VALUES ($product_id,$service_id,$date,NULL,NULL);");
      $service_id = $sizeid + 8;
      mysqli_query($link,"INSERT INTO serviceselect (product_id,service_id,select_date,st_progresscnt,st_cancelcnt)
      VALUES ($product_id,$service_id,$date,0,0);");
    }
  }

  //集荷＆保管選択時の住所挿入
  if ($se_array != NULL) {
    if ($se_array[0] == '集荷' && $se_array[1] == '保管') {
      $address = $_SESSION['address'];
      $postal_num = $_SESSION['postal_num'];
      //pickupdestination挿入
      mysqli_query($link,"INSERT INTO pickupdestination (pu_address,pu_postal_num,member_id)
      VALUES ('$address','$postal_num',$member_id);");
      $result = mysqli_query($link,"SELECT MAX(id) AS maxid FROM pickupdestination;");
      //pickup挿入
      $pickup_date = $_SESSION['pick_date'];
      $pickup_time = $_SESSION['pickup'];
      $pick_dist = mysqli_fetch_assoc($result);
      $pick_dist_id = $pick_dist['maxid'];
      $pic_date_array = explode("-",$pickup_date);
      $pickup_date = $pic_date_array[0].$pic_date_array[1].$pic_date_array[2];
      $int_date = intval($pickup_date);
      $int_dist_id = intval($pick_dist_id);
      mysqli_query($link,"INSERT INTO pickup (pickupdestination_id,storehouse_id,product_id,pickup_date,pickup_time)
      VALUES ($int_dist_id,$storehouse_id,$product_id,$pickup_date,'$pickup_time');");
    }
    if($se_array[0] == '保管'){
      echo "保管のみ";
      //pickupdestination挿入
      mysqli_query($link,"INSERT INTO pickupdestination (pu_address,pu_postal_num,member_id)
      VALUES ('コンビニ集荷','0000000',$member_id);");
      $result = mysqli_query($link,"SELECT MAX(id) AS maxid FROM pickupdestination;");
      //pickup挿入
      $pick_dist = mysqli_fetch_assoc($result);
      var_dump($pick_dist);
      $pick_dist_id = $pick_dist['maxid'];
      $int_dist_id = intval($pick_dist_id);
      $pickup_date = date("Y").date("m").date("d")+1;
      //var_dump($int_dist_id,$storehouse_id,$product_id,$pickup_date);
      mysqli_query($link,"INSERT INTO pickup (pickupdestination_id,storehouse_id,product_id,pickup_date,pickup_time)
      VALUES ($int_dist_id,$storehouse_id,$product_id,$pickup_date,'コンビニ集荷');");
    }
    if($se_array[0] == '梱包' && $se_array[1] == '保管'){
      echo "保管のみ";
      //pickupdestination挿入
      mysqli_query($link,"INSERT INTO pickupdestination (pu_address,pu_postal_num,member_id)
      VALUES ('コンビニ集荷','0000000',$member_id);");
      $result = mysqli_query($link,"SELECT MAX(id) AS maxid FROM pickupdestination;");
      //pickup挿入
      $pick_dist = mysqli_fetch_assoc($result);
      var_dump($pick_dist);
      $pick_dist_id = $pick_dist['maxid'];
      $int_dist_id = intval($pick_dist_id);
      $pickup_date = date("Y").date("m").date("d")+1;
      //var_dump($int_dist_id,$storehouse_id,$product_id,$pickup_date);
      mysqli_query($link,"INSERT INTO pickup (pickupdestination_id,storehouse_id,product_id,pickup_date,pickup_time)
      VALUES ($int_dist_id,$storehouse_id,$product_id,$pickup_date,'コンビニ集荷');");
    }
  }


  //画像挿入ーーー
  //一枚目
  mysqli_query($link,"INSERT INTO photo (product_id,photo_no,photo_url)
  VALUES ($product_id,1,'$photo_url1');");
  //二枚目
  if (isset($_SESSION['photo_url2'])) {
    mysqli_query($link,"INSERT INTO photo (product_id,photo_no,photo_url)
    VALUES ($product_id,2,'$photo_url2');");
  }
  //三枚目
  if (isset($_SESSION['photo_url3'])) {
    $photo_url3 = $_SESSION['photo_url3'];
    mysqli_query($link,"INSERT INTO photo (product_id,photo_no,photo_url)
    VALUES ($product_id,3,'$photo_url3');");
  }

  mysqli_close($link);
  session_destroy();
  session_start();
  $_SESSION['member_id'] = $member_id;
  header("Location: ./top.php");
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>商品情報確認</title>
<meta content="タイトル" name="title">

</head>
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/conf_product_info.css">
<link rel="stylesheet" href="css/all.css">
<body>
  <h1 class="none">サトウのフリマ</h1>
  <!-- <header>
    <p><img></p>
  </header> -->
  <main>
    <div class="mainbox">
      <div class="main">
        <table class="table table-bordered">
          <tr class="d-flex"><th class="text-center col-3 bg-dark text-white">商品名</th><td class="col-9"><?php echo $_SESSION['product_name']; ?></td></tr>
          <tr class="d-flex"><th class="text-center col-3 bg-dark text-white">商品説明</th><td class="col-9"><?php echo $_SESSION['explain']; ?></td></tr>
          <tr class="d-flex"><th class="text-center col-3 bg-dark text-white">商品画像1</th><td class="col-9"><?php echo $_SESSION['photo1']['name']; ?></td></tr>
          <?php if(isset($_SESSION['photo2'])) :?>
            <tr class="d-flex"><th class="text-center col-3 bg-dark text-white">商品画像2</th><td class="col-9"><?php echo $_SESSION['photo2']['name']; ?></td></tr>
          <?php endif; ?>
          <?php if(isset($_SESSION['photo3'])) :?>
            <tr class="d-flex"><th class="text-center col-3 bg-dark text-white">商品画像3</th><td class="col-9"><?php echo $_SESSION['photo3']['name']; ?></td></tr>
          <?php endif; ?>
          <tr class="d-flex"><th class="text-center col-3 bg-dark text-white">商品状態</th><td class="col-9"><?php echo $_SESSION['status']; ?></td></tr>
          <tr class="d-flex"><th class="text-center col-3 bg-dark text-white">価格</th><td class="col-9"><?php echo $_SESSION['price']; ?></td></tr>
          <tr class="d-flex"><th class="text-center col-3 bg-dark text-white">サイズ</th><td class="col-9"><?php echo $_SESSION['size']; ?></td></tr>
          <?php if(isset($_SESSION['service'])): ?>
            <?php foreach($_SESSION['service'] as $serv): ?>
                <tr class="d-flex"><th class="text-center col-3 bg-dark text-white">サービス<?php echo $i;?></th><td class="col-9"><?php echo $serv; ?></td></tr>
                <?php $i++; ?>
            <?php endforeach; ?>
          <?php endif; ?>
          <?php if(isset($_SESSION['address'])): ?>
            <tr class="d-flex"><th class="text-center col-3 bg-dark text-white">集荷日</th><td class="col-9"><?php echo $_SESSION['pick_date']; ?></td></tr>
            <tr class="d-flex"><th class="text-center col-3 bg-dark text-white">集荷時間</th><td class="col-9"><?php echo $_SESSION['pickup']; ?></td></tr>
            <tr class="d-flex"><th class="text-center col-3 bg-dark text-white">集荷先郵便番号</th><td class="col-9"><?php echo $_SESSION['postal_num']; ?></td></tr>
            <tr class="d-flex"><th class="text-center col-3 bg-dark text-white">集荷先住所</th><td class="col-9"><?php echo $_SESSION['address']; ?></td></tr>
          <?php endif; ?>
          <tr class="d-flex">
            <td class="col-12 text-center">
            <form method="POST" action="">
            <input type="hidden" name='sub_flg'>
            <button class="btn btn-primary" type="submit" name="listing">出品</button>
          </form>
            </td>
          </tr>
        </table>

      </div>
    </div>
  </main>
</body>
</html>
