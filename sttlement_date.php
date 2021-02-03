<?php

// ダイレクトアクセス禁止
// if(!isset($_GET['id'])){
//     header('Location:./top.php');
// }

if(isset($_POST['product_id'])){
    $date = date('Y/m/d');
    $date = explode('/',$date);
    $updata_date = $date[0].$date[1].$date[2];
    $updata_date = intval($updata_date);
    $link = mysqli_connect('localhost','root','','ih22');
    mysqli_set_charset($link,'utf8');
    $sql = "SELECT sttlement_date FROM transaction WHERE tr_product_id = " . $_POST['product_id'] ;
    $result = mysqli_query($link,$sql);
    $row = mysqli_fetch_assoc($result);

    if($row['sttlement_date'] == NULL){
        $sql = "UPDATE transaction SET sttlement_date = ". $updata_date ." WHERE tr_product_id = " . $_POST['product_id'] ;
        $result = mysqli_query($link,$sql);
        mysqli_close($link);
    }else{
        echo '既に入金が完了しています。';
    }

    $link = mysqli_connect('localhost','root','','ih22');
    mysqli_set_charset($link,'utf8');
    $sql = "SELECT sttlement_date FROM transaction WHERE tr_product_id = " . $_POST['product_id'] ;
    $result = mysqli_query($link,$sql);
    $row = mysqli_fetch_assoc($result);
    mysqli_close($link);
    //var_dump($_POST['product_id']);
    if($row['sttlement_date'] !== NULL){
        echo '入金処理が完了したので、配送日時を決定してください。';?><br><?
        ?><a href="./delivery_time.php?id=<?php echo $_POST['product_id'] ?>">配送日時入力へ</a><?php
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
<link rel="stylesheet" href="css/sttlement_date.css">
<link rel="stylesheet" href="css/all.css">
<body>
<form action='' method="POST">
    <div class="text-center mt-5">
        <button class="btn btn-primary" type='submit' name='product_id' value=<?php echo $_GET['id'] ?>>入金</button>
    </div>
</form>
</body>
</html>
