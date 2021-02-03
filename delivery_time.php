<?php

// ダイレクトアクセス禁止
if(!isset($_GET['id'])){
    header('Location:./top.php');
}

$product_id = $_GET['id'];

//集荷日取得
$link = mysqli_connect('localhost','root','','ih22');
mysqli_set_charset($link,'utf8');
$result = mysqli_query($link,"SELECT pickup_date FROM pickup WHERE product_id = $product_id;");
$p_date = mysqli_fetch_assoc($result);
$d_array = explode('-',$p_date['pickup_date']);
$d_array['2'] = $d_array['2'] + 7;
$max_date = $d_array['0'].'-'.$d_array['1'].'-'.$d_array['2'];

if(isset($_POST['data'])){
    // 時間と日付が選択されているかチェック
    if($_POST['time'] == ''){
        echo '時間が選択されていません。';
    }

    if($_POST['date'] == ''){
        echo '日付が選択されていません。';
    }
    // 配送日が既にあるかチェック

    $sql = "SELECT delivery_time FROM delivery WHERE product_id = " . $_POST['data'] ;
    $result = mysqli_query($link,$sql);
    $row = mysqli_fetch_assoc($result);
    mysqli_close($link);
    if($_POST['time'] !== '' && $_POST['date'] !== '' && $row['delivery_time'] == NULL){
        $ary = explode('-',$_POST['date']);
        $_POST['date'] = $ary[0].$ary[1].$ary[2];
        $link = mysqli_connect('localhost','root','','IH22');
        mysqli_set_charset($link,'utf8');
        $sql = "UPDATE delivery SET delivery_date = ". $_POST['date'] ." , delivery_time = '". $_POST['time'] ."' WHERE product_id = " . $_POST['data'] ;
        $result = mysqli_query($link,$sql);
        mysqli_close($link);
        header('Location:./top.php');
    }
}

// UPDATE delivery SET delivery_date = '20201216' , delivery_time = "12:00~14:00" WHERE product_id = 1000002



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>配送日時入力</title>
</head>
<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/delivery_time.css">
<link rel="stylesheet" href="css/all.css">
<body>
  <main>
    <div class="mainbox">
        <div class="main">
            <form action='' method="POST">
                <div class="form-group">
                    <label>配送日</label>
                    <input class="form-control" type='date' name='date' min="<?php echo $p_date['pickup_date']; ?>" max="<?php echo $max_date; ?>">
                </div>
                <div class="form-group">
                    <label>配送時間</label>
                    <select class="form-control" name='time'>
                        <option value=""></option>
                        <option value="12:00~14:00">12:00~14:00</option>
                        <option value="14:00~16:00">14:00~16:00</option>
                        <option value="16:00~18:00">16:00~18:00</option>
                        <option value="18:00~20:00">18:00~20:00</option>
                        <option value="20:00~22:00">20:00~22:00</option>
                        <option value="22:00~24:00">22:00~24:00</option>
                    </select>
                </div>
                <div class="text-center">
                    <button class="btn btn-primary" type="submit" name='data' value=<?php echo $_GET['id']; ?>>確定</button>
                </div>
            </form>
        </div>
    </div>
  </main>

</body>
</html>
