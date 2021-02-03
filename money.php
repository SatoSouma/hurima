<?php

//データベース接続
$size = [];
$link=mysqli_connect('localhost','root','','ih22');
mysqli_set_charset($link,'utf8');
// 配送込みサイズ料取り出し
$sql = "SELECT * FROM size";
$result = mysqli_query($link,$sql);
while($row = mysqli_fetch_assoc($result)){
    $size[] = $row;
}

// サービス料取り出し
$service = [];
$sql = "SELECT * FROM service";
$result = mysqli_query($link,$sql);
while($row = mysqli_fetch_assoc($result)){
    $service[] = $row;
}

// calculationをサービス毎の値段に置き換え。
foreach($service as $key => $val){
    if($val['id'] == 1){
        $service[$key]['calculation'] = '出品物の価格の10%';
    }
    if($val['id'] >= 2 && $val['id'] <= 15){
        $service[$key]['calculation'] = $val['calculation'] . '円';
    }
}

mysqli_close($link);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/money.css">
    <link rel="stylesheet" href="css/all.css">
    <title>Document</title>
</head>
<body>
<!-- <header>
    <p><img class="logo_img" src="./logo/satou.jpg"></p>
  </header> -->
<!-- 配送込みサイズ料の料金表 -->
    <h2 class="text-center bg-dark text-white">料金表</h2>
<div class="d-flex main ">
    <div class="tb tb1">
        <table class="table">
            <tr>
                <th class="bg-dark text-white">サイズ</th>
                <th class="bg-dark text-white">配送料金</th>
            </tr>
            <?php foreach($size as $val){ ?>
            <tr>
                <td class=""><?php echo $val['size_name'] ?></td>
                <td class=""><?php echo $val['price'] ?>円</td>
            </tr>
            <?php } ?>
        </table>
        <a href="user.php"><button class="btn btn-secondary back">戻る</button></a>
    </div>

    <!-- 各サービス料の料金表 -->
    <div class="tb">
        <table class="table ">
            <tr>
                <th class="bg-dark text-white">サイズ</th>
                <th class="bg-dark text-white">サービス料金</th>
            </tr>
            <?php foreach($service as $val){ ?>
            <tr>
                <td class=""><?php echo $val['service_name'] ?></td>
                <td class=""><?php echo $val['calculation'] ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>
</div>



</body>
</html>
