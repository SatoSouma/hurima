<?php
session_start();

$flg = 0;
$new_data = 0;
$err = [];

$disa1 = "disabled";
$disa2 = "disabled";

$date1 = date("Y");
$date2 = date("m");
$date3 = date("d");
$date_comp = $date1.'-'.$date2.'-'.$date3;

$selected1 = "";
$selected2 = "";
$selected3 = "";
$selected4 = "";
$selected5 = "";
$selected6 = "";

$checked1 = "";
$checked2 = "";
$checked3 = "";
$checked4 = "";

$min_date = date("Y").'-'.date("m");
$max_y = date("Y") + 5;
$max_date = $max_y.'-'.date("m");

//集荷日----
// if (isset($_POST['delv_date']) && $_POST['delv_date'] != NULL) {
//   $delv_date = $_POST['delv_date'];
//   $flg = $flg + 1;
// }elseif (isset($_POST['delv_date']) && $_POST['delv_date'] == NULL) {
//   array_push($err,'配送日が未入力です<br>');
// }

//集荷時間----
// if (isset($_POST['delv_time']) && $_POST['delv_time'] != "未選択") {
//   $delv_time = $_POST['delv_time'];
//   $flg = $flg + 1;
//   if ($delv_time == "12:00~14:00") {
//     $selected1 = "selected";
//   }
//   if ($delv_time == "14:00~16:00") {
//     $selected2 = "selected";
//   }
//   if ($delv_time == "16:00~18:00") {
//     $selected3 = "selected";
//   }
//   if ($delv_time == "18:00~20:00") {
//     $selected4 = "selected";
//   }
//   if ($delv_time == "20:00~22:00") {
//     $selected5 = "selected";
//   }
//   if ($delv_time == "22:00~24:00") {
//     $selected6 = "selected";
//   }
// }elseif(isset($_POST['delv_time']) && $_POST['delv_time'] == "未選択"){
//   array_push($err,'集荷時間が未入力です<br>');
// }

//お届け先----
if (isset($_POST['address']) && $_POST['address'] == "not_sel") {
  $checked1 = "checked";
  $disa1 = "disabled";
  $flg = $flg + 1;
}
elseif (isset($_POST['address']) && $_POST['address'] == "sel") {
  $checked2 = "checked";
  $disa1 = "";
  $new_data = $new_data + 1;
}

//集荷先郵便番号----
if(isset($_POST['new_postal']) && $_POST['new_postal'] != ""){
  $new_postal = $_POST['new_postal'];
  if (mb_strlen($_POST['new_postal']) == 7) {
    $flg = $flg + 1;
  }
  else{
    array_push($err,'郵便番号を正しく入力');
  }
}elseif (isset($_POST['new_postal']) && $_POST['new_postal'] == "") {
  array_push($err,'配送先郵便番号未入力');
  $new_postal = "";
}
else {
  $new_postal = "";
}

//集荷先住所----
if(isset($_POST['new_address']) && $_POST['new_address'] != ""){
  $new_address = $_POST['new_address'];
  if (mb_strlen($_POST['new_address']) < 101) {
    $flg = $flg + 1;
  }
  else{
    array_push($err,'住所文字オーバー100文字以内');
  }
}elseif (isset($_POST['new_address']) && $_POST['new_address'] == "") {
  array_push($err,'集荷先住所未入力');
  $new_address = "";
}
else {
  $new_address = "";
}

//支払い方法----
if (isset($_POST['payment']) && $_POST['payment'] == "conb") {
  $checked3 = "checked";
  $disa2 = "disabled";
  $flg = $flg + 1;
}
elseif (isset($_POST['payment']) && $_POST['payment'] == "creca") {
  $checked4 = "checked";
  $disa2 = "";
  $new_data = $new_data + 2;
}

//クレカ情報----
//カード番号
if(isset($_POST['card_num']) && $_POST['card_num'] != ""){
  $card_num = $_POST['card_num'];
  if (14 <= strlen($_POST['card_num']) && strlen($_POST['card_num']) <= 16) {
    $flg = $flg + 1;
  }
  else{
    array_push($err,'クレカ番号は半角数字14桁〜16桁で入力');
  }
}elseif (isset($_POST['card_num']) && $_POST['card_num'] == "") {
  array_push($err,'クレカ番号未入力');
  $card_num = "";
}
else {
  $card_num = "";
}

//クレカ名義
if(isset($_POST['card_name']) && $_POST['card_name'] != ""){
  $card_name = $_POST['card_name'];
  if(preg_match("/[A-Z]/",$_POST['card_name'])){
    $flg = $flg + 1;
  }
  else {
    array_push($err,'使用できる文字は大文字アルファベト');
  }
}elseif (isset($_POST['card_name']) && $_POST['card_name'] == "") {
  array_push($err,'カード名義未入力');
  $card_name = "";
}
else {
  $card_name = "";
}

//有効期限
if (isset($_POST['card_deadline']) && $_POST['card_deadline'] != NULL) {
  $card_deadline = $_POST['card_deadline'];
  $flg = $flg + 1;
}elseif (isset($_POST['card_deadline']) && $_POST['card_deadline'] == NULL) {
  array_push($err,'有効期限が未入力です<br>');
  $card_deadline = "";
}

// //セキュリティコード
// if(isset($_POST['card_secnum']) && $_POST['card_secnum'] != ""){
//   $card_secnum = $_POST['card_secnum'];
//   if (strlen($_POST['card_secnum']) == 3) {
//     if (preg_match("/[0-9]/",$_POST['card_secnum'])) {
//       $flg = $flg + 1;
//     }else {
//       array_push($err,'セキュリティコードは半角数字で入力');
//     }
//   }else {
//     array_push($err,'セキュリティコードは半角数字３桁で入力');
//   }
// }elseif (isset($_POST['card_secnum']) && $_POST['card_secnum'] == "") {
//   array_push($err,'セキュリティコード未入力<br>');
//   $card_secnum = "";
// }
// else {
//   $card_secnum = "";
// }

//エラー無し処理----
//登録住所＆コンビニ
if ($flg == 2 && $new_data == 0) {
  //$_SESSION['delv_date'] = $delv_date;
  //$_SESSION['delv_time'] = $delv_time;
  $_SESSION['flg_data'] = $flg;
  header("Location: ./conf_purchase.php");
}
//新規住所＆コンビニ
if ($flg == 3 && $new_data == 1) {
  //$_SESSION['delv_date'] = $delv_date;
  //$_SESSION['delv_time'] = $delv_time;
  $_SESSION['flg_data'] = $flg;
  $_SESSION['new_postal'] = $new_postal;
  $_SESSION['new_address'] = $new_address;
  header("Location: ./conf_purchase.php");
}
//登録住所＆クレカ
if ($flg == 4 && $new_data == 2) {
  //$_SESSION['delv_date'] = $delv_date;
  //$_SESSION['delv_time'] = $delv_time;
  $_SESSION['flg_data'] = $flg;
  $_SESSION['card_num'] = $card_num;
  $_SESSION['card_name'] = $card_name;
  $_SESSION['card_deadline'] = $card_deadline;
  //$_SESSION['card_secnum'] = $card_secnum;
  header("Location: ./conf_purchase.php");
}
//新規住所＆クレカ
if ($flg == 5 && $new_data == 3) {
  //$_SESSION['delv_date'] = $delv_date;
  //$_SESSION['delv_time'] = $delv_time;
  $_SESSION['flg_data'] = $flg;
  $_SESSION['new_postal'] = $new_postal;
  $_SESSION['new_address'] = $new_address;
  $_SESSION['card_num'] = $card_num;
  $_SESSION['card_name'] = $card_name;
  $_SESSION['card_deadline'] = $card_deadline;
  //$_SESSION['card_secnum'] = $card_secnum;
  header("Location: ./conf_purchase.php");
}

 ?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>購入</title>
<meta content="タイトル" name="title">
<script>
function change_ad() {
  var element1,element2;
  if(document.getElementById("new_address").checked) {
      element1 = document.getElementById("new_pos");
      element2 = document.getElementById("new_add");
      element1.disabled = false;
      element2.disabled = false;
  }
  else if (document.getElementById("or_add").checked) {
    element1 = document.getElementById("new_pos");
    element2 = document.getElementById("new_add");
    element1.disabled = true;
    element2.disabled = true;
  }
}

function changepay() {
  var element1,element2,element3,element4;
  if(document.getElementById("creca").checked) {
      element1 = document.getElementById("card_num");
      element2 = document.getElementById("card_name");
      element3 = document.getElementById("card_deadline");
      element4 = document.getElementById("card_secnum");
      element1.disabled = false;
      element2.disabled = false;
      element3.disabled = false;
      element4.disabled = false;
  }
  else if (document.getElementById("conb").checked) {
    element1 = document.getElementById("card_num");
    element2 = document.getElementById("card_name");
    element3 = document.getElementById("card_deadline");
    element4 = document.getElementById("card_secnum");
    element1.disabled = true;
    element2.disabled = true;
    element3.disabled = true;
    element4.disabled = true;
  }
}

</script>
</head>
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/purchase.css">
<link rel="stylesheet" href="css/all.css">
<body>
  <h1 class="none">サトウのフリマ</h1>
  <!-- <header>
    <p><img></p>
  </header> -->

  <main>
    <div class="mainbox">
      <div class="main">
        <form method="POST" action="" enctype="multipart/form-data">
          <!-- <div class="form-group d-flex">
            <label class="label">配送日</label>
            <input class="form-control form-control-sm input" type="date" name="delv_date" min="<?php echo $date_comp; ?>" max="" value="<?php echo $delv_date; ?>">
          </div>

          <div class="form-group d-flex">
            <label class="label">配送時間</label>
            <select class="form-control form-control-sm input" name="delv_time">
              <option value="未選択"></option>
              <option value="12:00~14:00" <?php echo $selected1; ?>>12:00~14:00</option>
              <option value="14:00~16:00" <?php echo $selected2; ?>>14:00~16:00</option>
              <option value="16:00~18:00" <?php echo $selected3; ?>>16:00~18:00</option>
              <option value="18:00~20:00" <?php echo $selected4; ?>>18:00~20:00</option>
              <option value="20:00~22:00" <?php echo $selected5; ?>>20:00~22:00</option>
              <option value="22:00~24:00" <?php echo $selected6; ?>>22:00~24:00</option>
            </select>""
          </div> -->

          <p class="text-center">商品購入</p>
          <div class="form-group d-flex">
            <label class="label">お届け先</label>
            <div class="d-flex input">
              <div class="form-check ch custom-control">
                <label>登録住所</label>
                <input type="radio" name="address" value="not_sel" onclick="change_ad()" id="or_add" <?php echo $checked1; ?> required>
              </div>
              <div class="form-check ch custom-control">
                <label>新規住所</label>
                <input type="radio" name="address" value="sel" id="new_address" onclick="change_ad()" <?php echo $checked2; ?> required>
              </div>
            </div>
          </div>

          <div class="form-group d-flex">
            <label class="label">新規郵便番号</label>
            <input class="form-control form-control-sm input" type="text" name="new_postal" id="new_pos" <?php echo $disa1; ?> value="<?php echo $new_postal; ?>">
          </div>

          <div class="form-group d-flex">
            <label class="label">新規住所</label>
            <input class="form-control form-control-sm input" type="text" name="new_address" id="new_add" <?php echo $disa1; ?> value="<?php echo $new_address; ?>">
          </div>

          <div class="form-group d-flex">
            <label class="label">支払方法</label>
            <div class="d-flex input">
              <div class="form-check ch custom-control">
                <label>コンビニ</label>
                <input class="form-check-label" type="radio" name="payment" id="conb" value="conb" onclick="changepay()" <?php echo $checked3; ?> required>
              </div>
              <div class="form-check ch custom-control">
                <label>クレジットカード</label>
                <input class="form-check-label" type="radio" name="payment" id="creca" value="creca" onclick="changepay()" <?php echo $checked4; ?> required>
              </div>
            </div>
          </div>

          <div class="form-group d-flex">
            <label class="label">カード番号</label>
            <input class="form-control form-control-sm input" type="text" name="card_num" id="card_num" <?php echo $disa2; ?> value="<?php echo $card_num; ?>">
          </div>

          <div class="form-group d-flex">
            <label class="label">名義</label>
            <input class="form-control form-control-sm input" type="text" name="card_name" id="card_name" <?php echo $disa2; ?> value="<?php echo $card_name; ?>">
          </div>

          <div class="form-group d-flex">
            <label class="label">有効期限</label>
            <input class="form-control form-control-sm input" type="month" name="card_deadline" min="<?php echo $min_date; ?>" max="<?php echo $max_date; ?>" id="card_deadline" <?php echo $disa2; ?> value="<?php echo $card_deadline; ?>">
          </div>

          <div class="text-center">
            <button class="btn btn-primary" type="submit" name="listing">確認画面へ</button>
          </div>
        </form>

        <div class="">
          <ul>
          <?php foreach($err as $msg): ?>
            <li class="text-danger"><?php echo $msg;?></li>
          <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>

  </main>
</body>
</html>
