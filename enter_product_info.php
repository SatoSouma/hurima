<?php
session_start();
$flg = 0;
$pickflg = 0;
$service_price = [0,100,200,250,300,350,400,800,100,200,250,300,350,400,800];
$total_minus = 0;
$minus_flg = 0;
$date1 = date("Y");
$date2 = date("m");
$date3 = date("d");
$date_comp = $date1.'-'.$date2.'-'.$date3;

$selected1 = "";
$selected2 = "";
$selected3 = "";

$selected11 = "";
$selected12 = "";
$selected13 = "";
$selected14 = "";
$selected15 = "";
$selected16 = "";
$selected17 = "";

$selected21 = "";
$selected22 = "";
$selected23 = "";

$checked1 = "";
$checked2 = "";
$checked3 = "";

$pick_date = "";

$disa = "disabled";

$err = [];

//入力チェック-----------------------------------------------------
//商品名
if(isset($_POST['product_name']) && $_POST['product_name'] != ""){
  $product_name = $_POST['product_name'];
  if (strlen($_POST['product_name']) < 51) {
    $flg = $flg + 1;
  }else {
    array_push($err,'商品名は20文字以内で入力<br>');
  }
}elseif (isset($_POST['product_name']) && $_POST['product_name'] == "") {
  array_push($err,'商品名未入力<br>');
  $product_name = "";
}
else {
  $product_name = "";
}

//商品説明
if(isset($_POST['explain']) && $_POST['explain'] != ""){
  $explain = $_POST['explain'];
  if (strlen($_POST['explain']) < 501) {
    $flg = $flg + 1;
  }else {
    array_push($err,'商品説明文字オーバー500文字以内');
  }
}elseif (isset($_POST['explain']) && $_POST['explain'] == "") {
  array_push($err,'商品説明未入力<br>');
  $explain = "";
}
else {
  $explain = "";
}

//画像
if(isset($_FILES['photo1']) && $_FILES['photo1']['size'] != 0) {
  $photo1 = $_FILES['photo1'];
  $_SESSION['photo1'] = $photo1;
  $flg = $flg + 1;
}
if (isset($_FILES['photo2']) && $_FILES['photo2']['size'] != 0) {
  $photo2 = $_FILES['photo2'];
  $_SESSION['photo2'] = $photo2;
}
if (isset($_FILES['photo3']) && $_FILES['photo3']['size'] != 0) {
  $photo3 = $_FILES['photo3'];
  $_SESSION['photo2'] = $photo2;
}
if (isset($_FILES['photo1']) && $_FILES['photo1']['size'] == 0) {
  array_push($err,'画像を最低一枚選択<br>');
}


//商品状態
if (isset($_POST['status']) && $_POST['status'] != "未選択") {
  $status = $_POST['status'];
  $flg = $flg + 1;
  $imgflg = 1;
  if ($status == "新品") {
    $selected1 = "selected";
  }
  if ($status == "中古") {
    $selected2 = "selected";
  }
  if ($status == "キズあり") {
    $selected3 = "selected";
  }
}elseif(isset($_POST['status'])){
  if ($_POST['status'] == "未選択") {
    array_push($err,'商品状態が未入力です<br>');
  }
}

//サイズ
if (isset($_POST['size']) && $_POST['size'] != "未選択") {
  $size = $_POST['size'];
  $sizeid = 0;
  $flg = $flg + 1;
  if ($size == "60サイズ未満") {
    $selected11 = "selected";
    $sizeid = 1;
  }
  if ($size == "80サイズ未満") {
    $selected12 = "selected";
    $sizeid = 2;
  }
  if ($size == "100サイズ未満") {
    $selected13 = "selected";
    $sizeid = 3;
  }
  if ($size == "120サイズ未満") {
    $selected14 = "selected";
    $sizeid = 4;
  }
  if ($size == "140サイズ未満") {
    $selected15 = "selected";
    $sizeid = 5;
  }
  if ($size == "160サイズ未満") {
    $selected16 = "selected";
    $sizeid = 6;
  }
  if ($size == "160サイズ以上") {
    $selected17 = "selected";
    $sizeid = 7;
  }
}elseif(isset($_POST['size']) && $_POST['size'] == "未選択"){
    array_push($err,'サイズが未入力です<br>');
}

//価格
if(isset($_POST['price']) && $_POST['price'] != ""){
  $price = $_POST['price'];
  if (mb_strlen($_POST['price']) < 7) {
    $flg = $flg + 1;
  }else {
    array_push($err,'価格が文字オーバー６桁以内');
  }
}elseif (isset($_POST['price']) && $_POST['price'] == "") {
  array_push($err,'価格未入力');
  $price = "";
}
else {
  $price = "";
}

//サービス&サービス料金加算
if (isset($_POST['service'])) {
  $se_array = [];
  $se_array = $_POST['service'];
  if ($se_array[0] == "集荷") {
    $checked1 = "checked";
    $ser_id = 100;
    $total_minus = $total_minus + ($price*0.1);
    if (isset($se_array[1]) && $se_array[1] == "梱包") {
      $checked2 = "checked";
      $ser_id = $ser_id + 10;
      $total_minus = $total_minus + $service_price[$sizeid];
      if (isset($se_array[2]) && $se_array[2] == "保管") {
        $checked3 = "checked";
        $ser_id = $ser_id + 1;
        $total_minus = $total_minus + $service_price[$sizeid+7];
      }
    }
    elseif (isset($se_array[1]) && $se_array[1] == "保管") {
      $checked3 = "checked";
      $ser_id = $ser_id + 1;
      $disa = "";
      $total_minus = $total_minus + $service_price[$sizeid+7];
    }
  }
  elseif (isset($se_array[0]) && $se_array[0] == "梱包") {
    $checked2 = "checked";
    $ser_id = 10;
    $total_minus = $total_minus + $service_price[$sizeid];
    if (isset($se_array[1]) && $se_array[1] == "保管") {
      $checked3 = "checked";
      $ser_id = $ser_id + 1;
      $total_minus = $total_minus + $service_price[$sizeid+7];
    }
  }
  elseif (isset($se_array[0]) && $se_array[0] == "保管") {
    $checked3 = "checked";
    $ser_id = 1;
    $total_minus = $total_minus + $service_price[$sizeid+7];
  }

  //料金チェック
  if ($price < $total_minus) {
    array_push($err,'売上金が0円以下になります<br>');
    $minus_flg = 1;
  }

}

if(isset($ser_id)){
  $ser_pad = str_pad($ser_id,3,'0',STR_PAD_LEFT);
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


//エラーなし処理------
//product_id取得＆ +1
if($minus_flg == 0){
  if ($flg == 6 || $flg == 10) {
    $fp = fopen('id/product_num.txt','r');
    $last_id = fgets($fp);
    $new_id = $last_id + 1;
    $pad_id = str_pad($new_id,4,'0',STR_PAD_LEFT);
    echo $pad_id;
    $fp = fopen('id/product_num.txt','w');
    fputs($fp,$pad_id);
    fclose($fp);
    //product_id生成
    $product_id = $ser_pad.$pad_id;

    //画像をファイルに入れる
    $photo_url1 = $pad_id.'1.jpg';
    $_SESSION['photo_url1'] = $photo_url1;
    //１枚目
    move_uploaded_file($photo1['tmp_name'],'./img/'.$photo_url1);
    //２枚目
    if (isset($photo2)) {
      $photo_url2 = $pad_id.'2.jpg';
      $_SESSION['photo_url2'] = $photo_url2;
      move_uploaded_file($photo2['tmp_name'],'./img/'.$photo_url2);
    }
    //３枚目
    if (isset($photo3)) {
      $photo_url3 = $pad_id.'3.jpg';
      $_SESSION['photo_url3'] = $photo_url3;
      move_uploaded_file($photo3['tmp_name'],'./img/'.$photo_url3);
    }
  }


  //集荷未選択時
  if ($flg == 6) {
    $_SESSION['product_id'] = $product_id;
    $_SESSION['product_name'] = $product_name;
    $_SESSION['explain'] = $explain;
    $_SESSION['status'] = $status;
    $_SESSION['size'] = $size;
    $_SESSION['sizeid'] = $sizeid;
    $_SESSION['service'] = $se_array;
    $_SESSION['ser_pad'] = $ser_pad;
    $_SESSION['price'] = $price;
    header("Location: ./conf_product_info.php");
  }
  //集荷&梱包選択時
  if ($flg == 10 && isset($address)) {
    $_SESSION['product_id'] = $product_id;
    $_SESSION['product_name'] = $product_name;
    $_SESSION['explain'] = $explain;
    $_SESSION['status'] = $status;
    $_SESSION['size'] = $size;
    $_SESSION['sizeid'] = $sizeid;
    $_SESSION['price'] = $price;
    $_SESSION['service'] = $se_array;
    $_SESSION['ser_pad'] = $ser_pad;
    $_SESSION['pickup'] = $pickup;
    $_SESSION['pick_date'] = $pick_date;
    $_SESSION['postal_num'] = $postal_num;
    $_SESSION['address'] = $address;
    header("Location: ./conf_product_info.php");
  }
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>商品情報入力</title>
<meta content="タイトル" name="title">
<script>
function change() {
  var element1,element2,element3,element4;

  if(document.getElementById("collect").checked && document.getElementById("keep").checked) {
      element1 = document.getElementById("pickup");
      element2 = document.getElementById("postal_num");
      element3 = document.getElementById("address");
      element4 = document.getElementById("pick_date");
      element1.disabled = false;
      element2.disabled = false;
      element3.disabled = false;
      element4.disabled = false;
  }else {
      element1 = document.getElementById("pickup");
      element2 = document.getElementById("postal_num");
      element3 = document.getElementById("address");
      element4 = document.getElementById("pick_date");
      element1.disabled = true;
      element2.disabled = true;
      element3.disabled = true;
      element4.disabled = true;
  }

}
</script>
</head>
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/enter_product_info.css">
<link rel="stylesheet" href="css/all.css">
<body>
  <h1 class="none">サトウのフリマ</h1>
  <!-- <header>
    <p><img></p>
  </header> -->

  <main>
    <div class="mainbox">
      <div class="main">
        <p class="text-center">商品情報入力</p>
        <form method="POST" action="" enctype="multipart/form-data">
          <div class="form-group d-flex">
            <label  class="label">商品名</label>
            <input type="text" name="product_name" value="<?php echo $product_name; ?>" class="form-control form-control-sm input">
          </div>

          <div class="form-group d-flex">
            <label class="label">商品説明</label>
            <textarea name="explain" rows="4" cols="40" class="form-control form-control-sm input"><?php echo $explain; ?></textarea>
          </div>

          <div class="form-group d-flex">
            <label class="label">商品画像</label>
            <div class="input">
              <div class="custom-file f">
                <input type="file" accept="image/*" name="photo1" class="custom-file-input" required>
                <label class="custom-file-label" for="inputFile" data-browse="参照">ファイルを選択</label>
              </div>
              <div class="custom-file f">
                <input type="file" accept="image/*" name="photo2" class="custom-file-input">
                <label class="custom-file-label" for="inputFile" data-browse="参照">ファイルを選択</label>
              </div>
              <div class="custom-file f">
                <input type="file" accept="image/*" name="photo3" class="custom-file-input">
                <label class="custom-file-label" for="inputFile" data-browse="参照">ファイルを選択</label>
              </div>
            </div>
          </div>

          <div class="form-group d-flex">
            <label class="label">商品状態</label>
            <div class="input d-flex pp">
              <select name="status" class="form-control form-control-sm select st">
                <option value="未選択"></option>
                <option value="新品" <?php echo $selected1; ?>>新品</option>
                <option value="中古" <?php echo $selected2; ?>>中古</option>
                <option value="キズあり" <?php echo $selected3; ?>>キズあり</option>
              </select>
              <div class="sz d-flex align-items-center">
                <label class="szl text-center">サイズ</label>
              </div>
              <select name="size" class="form-control form-control-sm select szz">
                <option value="未選択"></option>
                <option value="60サイズ未満" <?php echo $selected11; ?>>60サイズ未満</option>
                <option value="80サイズ未満" <?php echo $selected12; ?>>80サイズ未満</option>
                <option value="100サイズ未満" <?php echo $selected13; ?>>100サイズ未満</option>
                <option value="120サイズ未満" <?php echo $selected14; ?>>120サイズ未満</option>
                <option value="140サイズ未満" <?php echo $selected15; ?>>140サイズ未満</option>
                <option value="160サイズ未満" <?php echo $selected16; ?>>160サイズ未満</option>
                <option value="160サイズ以上" <?php echo $selected17; ?>>160サイズ以上</option>
              </select>
            </div>
          </div>

          <div class="form-group d-flex">
            <label class="label">価格</label>
            <input type="text" name="price" value="<?php echo $price; ?>" class="form-control form-control-sm input">
          </div>

          <div class="form-group d-flex">
          <label class="label">サービス</label>
          <div class="input d-flex">
            <div class="form-check ch custom-control d-flex">
              <label class="form-check-label">集荷</label>
              <input class="form-check-input cb" type="checkbox" name="service[]" value="集荷" <?php echo $checked1; ?> id="collect" onchange="change()">
            </div>
            <div class="form-check ch custom-control d-flex">
              <label class="form-check-label">梱包</label>
              <input class="form-check-input cb" type="checkbox" name="service[]" value="梱包" <?php echo $checked2; ?>>
            </div>
            <div class="form-check ch custom-control d-flex">
              <label class="form-check-label">保管</label>
              <input class="form-check-input cb" type="checkbox" name="service[]" value="保管" <?php echo $checked3; ?> id="keep" onchange="change()">
            </div>
          </div>
        </div>

        <div class="form-group d-flex">
          <label class="label">集荷日</label>
          <input class="form-control form-control-sm input" type="date" name="pick_date" id="pick_date" min="<?php echo $date_comp; ?>" max="" value="<?php echo $pick_date; ?>"<?php echo $disa;?>>
        </div>

          <div class="form-group d-flex">
            <label class="label">集荷時間</label>
            <select name="pickup" id="pickup" <?php echo $disa; ?> class="form-control form-control-sm input">
              <option value="未選択"></option>
              <option value="12:00~14:00" <?php echo $selected21; ?>>12:00~14:00</option>
              <option value="14:00~16:00" <?php echo $selected22; ?>>14:00~16:00</option>
              <option value="16:00~18:00" <?php echo $selected23; ?>>16:00~18:00</option>
            </select>
          </div>

          <div class="form-group d-flex">
            <label class="label">集荷先郵便番号</label>
            <input type="text" name="postal_num" value="<?php echo $postal_num; ?>" id="postal_num" <?php echo $disa; ?> class="form-control form-control-sm input">
          </div>

          <div class="form-group d-flex">
            <label class="label">集荷先住所</label>
            <input type="text" name="address" value="<?php echo $address; ?>" id="address" <?php echo $disa; ?> class="form-control form-control-sm input">
          </div>


          <div class="text-center">
          <button type="submit" name="listing" class="btn btn-primary">出品</button>
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

  <script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.js"></script>

<script>
  $(function() {
  var $textarea = $('#textarea');
  var lineHeight = parseInt($textarea.css('lineHeight'));
  $textarea.on('input', function(e) {
    var lines = ($(this).val() + '\n').match(/\n/g).length;
    $(this).height(lineHeight * lines);
  });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.js"></script>
<script>
  bsCustomFileInput.init();
</script>

</body>
</html>
