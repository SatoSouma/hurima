<?php
session_start();
$product_id = $_GET['product_id'];
$member_id = $_SESSION['member_id'];
$mail_flg = $_SESSION['mail_flg'];

$err = [];
$flg = 0;

$date1 = date("Y");
$date2 = date("m");
$date3 = date("d");
//$date3 = intval($date3);
$m_date3 = $date3 + 7;
// var_dump($m_date3);
$date_comp = $date1.'-'.$date2.'-'.$date3;
$max_date = $date1.'-'.$date2.'-'.$m_date3;

$selected21 = "";
$selected22 = "";
$selected23 = "";

$address = "";
$postal_num = "";

//倉庫割当
$pref_array = [0,1,2,2,2,2,2,2,4,4,4,4,4,4,4,3,3,3,3,3,3,3,3,3,5,5,5,5,5,5,5,7,7,7,7,7,6,6,6,6,8,8,8,8,8,8,8];
$pref_num = substr($member_id,0,2);
$pref_int = intval($pref_num);
$storehouse_id = $pref_array[$pref_int];

//集荷のみと集荷梱包時
if ($mail_flg == 1) {

  //集荷先住所
  if(isset($_POST['address']) && $_POST['address'] != ""){
    $address = $_POST['address'];
    if (mb_strlen($_POST['address']) < 101) {
      $flg = $flg + 1;
    }
    else{
      array_push($err,'住所文字オーバー100文字以内');
    }
  }elseif (isset($_POST['address']) && $_POST['address'] == "") {
    array_push($err,'集荷先住所未入力');
    $address = "";
  }
  else {
    $address = "";
  }

  //集荷先郵便番号
  if(isset($_POST['postal_num']) && $_POST['postal_num'] != ""){
    $postal_num = $_POST['postal_num'];
    if (mb_strlen($_POST['postal_num']) == 7) {
      $flg = $flg + 1;
    }
    else{
      array_push($err,'郵便番号を正しく入力');
    }
  }elseif (isset($_POST['postal_num']) && $_POST['postal_num'] == "") {
    array_push($err,'集荷先郵便番号未入力');
    $postal_num = "";
  }
  else {
    $postal_num = "";
  }

  //集荷日
  if (isset($_POST['pick_date']) && $_POST['pick_date'] != NULL) {
    $pick_date = $_POST['pick_date'];
    $flg = $flg + 1;
  }elseif (isset($_POST['pick_date']) && $_POST['pick_date'] == NULL) {
    array_push($err,'集荷日が未入力です<br>');
  }

  //集荷時間
  if (isset($_POST['pickup']) && $_POST['pickup'] != "未選択") {
    $pickup = $_POST['pickup'];
    $flg = $flg + 1;
    if ($pickup == "12:00~14:00") {
      $selected21 = "selected";
    }
    if ($pickup == "14:00~16:00") {
      $selected22 = "selected";
    }
    if ($pickup == "16:00~18:00") {
      $selected23 = "selected";
    }
  }elseif(isset($_POST['pickup']) && $_POST['pickup'] == "未選択"){
    array_push($err,'集荷時間が未入力です<br>');
  }
}

//no_service,梱包のみ時
if ($mail_flg == 2) {
  //コンビニ持ち込み日
  if (isset($_POST['pick_date']) && $_POST['pick_date'] != NULL) {
    $pick_date = $_POST['pick_date'];
    $flg = $flg + 1;
  }elseif (isset($_POST['pick_date']) && $_POST['pick_date'] == NULL) {
    array_push($err,'コンビニ持ち込み日が未入力です<br>');
  }
}


//エラー無し処理----
////集荷のみと集荷梱包時
if ($flg == 4 && $mail_flg == 1) {
  // echo $address."<br>";
  // echo $postal_num."<br>";
  // echo $member_id."<br>";
  // echo $pick_date."<br>";
  // echo $pickup."<br>";
  $link=mysqli_connect('localhost','root','','ih22');
  mysqli_set_charset($link,'utf8');

  //pickupdestination挿入
  mysqli_query($link,"INSERT INTO pickupdestination (pu_address,pu_postal_num,member_id)
  VALUES ('$address','$postal_num',$member_id);");
  $result = mysqli_query($link,"SELECT MAX(id) AS maxid FROM pickupdestination;");

  //pickup挿入
  $pick_dist = mysqli_fetch_assoc($result);
  $pick_dist_id = $pick_dist['maxid'];
  $pic_date_array = explode("-",$pick_date);
  $pickup_date = $pic_date_array[0].$pic_date_array[1].$pic_date_array[2];
  $int_date = intval($pickup_date);
  $int_dist_id = intval($pick_dist_id);
  mysqli_query($link,"INSERT INTO pickup (pickupdestination_id,storehouse_id,product_id,pickup_date,pickup_time)
  VALUES ($int_dist_id,$storehouse_id,$product_id,$pickup_date,'$pickup');");

  mysqli_close($link);
  session_destroy();
  session_start();
  $_SESSION['member_id'] = $member_id;
  header("Location: ./top.php");
}

//no_service,梱包のみ時
if ($flg == 1 && $mail_flg == 2) {
  $link=mysqli_connect('localhost','root','','ih22');
  mysqli_set_charset($link,'utf8');

  //pickupdestination挿入
  mysqli_query($link,"INSERT INTO pickupdestination (pu_address,pu_postal_num,member_id)
  VALUES ('コンビニ集荷','0000000',$member_id);");
  $result = mysqli_query($link,"SELECT MAX(id) AS maxid FROM pickupdestination;");

  //pickup挿入
  $pick_dist = mysqli_fetch_assoc($result);
  $pick_dist_id = $pick_dist['maxid'];
  $pic_date_array = explode("-",$pick_date);
  $pickup_date = $pic_date_array[0].$pic_date_array[1].$pic_date_array[2];
  $int_date = intval($pickup_date);
  $int_dist_id = intval($pick_dist_id);
  mysqli_query($link,"INSERT INTO pickup (pickupdestination_id,storehouse_id,product_id,pickup_date,pickup_time)
  VALUES ($int_dist_id,$storehouse_id,$product_id,$pickup_date,'コンビニ集荷');");

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
<title>集荷情報入力</title>
<meta content="タイトル" name="title">

</head>
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/pickup_data.css">
<link rel="stylesheet" href="css/all.css">
<body>
  <h1 class="none">サトウのフリマ</h1>
  <!-- <header>
    <p><img></p>
  </header> -->

  <main>
    <div class="mainbox">
      <div class="main">
        <form method="POST">
        <?php if($mail_flg == 1): ?>
        <p class="text-success">出品中の商品が購入されました。情報を入力してください。</p>
          <div class="d-flex form-group">
            <label>集荷先住所</label>
            <input class="form-control" type="text" name="address" value="<?php echo $address; ?>" required>
          </div>

          <div class="d-flex form-group">
            <label>集荷先郵便番号</label>
            <input class="form-control" type="text" name="postal_num" value="<?php echo $postal_num; ?>" required>
          </div>

          <div class="d-flex form-group">
            <label>集荷日</label>
            <input class="form-control" type="date" min="<?php echo $date_comp; ?>" max="<?php echo $max_date; ?>" name="pick_date" value="<?php echo $pick_date; ?>" required>
          </div>

          <div class="d-flex form-group">
            <label>集荷時間</label>
            <select class="form-control" name="pickup" required>
              <option value="未選択"></option>
              <option value="12:00~14:00" <?php echo $selected21; ?>>12:00~14:00</option>
              <option value="14:00~16:00" <?php echo $selected22; ?>>14:00~16:00</option>
              <option value="16:00~18:00" <?php echo $selected23; ?>>16:00~18:00</option>
            </select>
          </div>

          <div class="text-center">
            <input class="btn btn-primary" type="submit" name="sub">
          </div>
          </form>
          <div class="">
            <ul>
            <?php foreach($err as $msg): ?>
              <li class="text-danger"><?php echo $msg;?></li>
            <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <?php if($mail_flg == 2): ?>
          <p class="text-success">出品中の商品が購入されました。コンビニへの持ち込み日を入力してください。</p>
          <div class="d-flex form-group">
            <label>集荷日</label>
            <input class="form-control" type="date" name="pick_date" min="<?php echo $date_comp; ?>" max="<?php echo $max_date; ?>" value="<?php echo $pick_date; ?>" required>
          </div>

          <div class="text-center">
            <input class="btn btn-primary" type="submit" name="sub">
          </div>
          </form>
          <div class="">
            <ul>
            <?php foreach($err as $msg): ?>
              <li class="text-danger"><?php echo $msg;?></li>
            <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>
      </div>
    </div>

  </main>
</body>
</html>
