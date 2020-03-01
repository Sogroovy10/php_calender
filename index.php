<?php
//月遷移ボタンからの値を受けているか判断した上でDateTime関数を設定
if(isset($_POST['action'])){
  $date = new DateTime($_POST['action']);
}else{
  $date = new DateTime();
}
// 当月以外の日付は薄いグレーで出力するために、当月の値を退避
$month = $date->format('M');
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>Calender</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <!-- 年月を表示 -->
  <h2><?php echo $date->format('Y'); ?></h2>
  <h1><?php echo $date->format('M'); ?></h1>
<div id="container">
  <!-- 先月、今月、当月のボタンを設定するために情報を取得 -->
  <?php
  $prev = $date->modify('-1 month')->format('Y-m-d');
  $next = $date->modify('+2 month')->format('Y-m-d');
  // 後の計算元の月に戻す
  $date->modify('-1 month');
  //今月の取得
  $newdate = new Datetime();
  $thismonth = $newdate->format('Y-m-d');
  ?>
<!-- ボタンの設定 -->
  <form action="index.php" method="post">
    <button type='submit' name='action' value=<?php echo $prev; ?>>先月</button>
    <button type='submit' name='action' value=<?php echo $thismonth; ?> >今月</button>
    <button type='submit' name='action' value=<?php echo $next; ?> >翌月</button>
  </form>
<!-- カレンダーの表示準備 -->
  <?php
  //当月1日取得
  $date->modify("first day of this month");
  //日付開始位置修正用
    $array = [
        "Sunday" => 0,
        "Monday" => 1,
        "Tuesday"=> 2,
        "Wednesday" => 3,
        "Thursday" => 4,
        "Friday" => 5,
        "Saturday" => 6,
    ];
  //カレンダー開始日付設定
  $modification = - $array[$date->format("l")];
  $date->modify("+$modification day");
   ?>
   <!-- テーブルにカレンダーを設定 -->
  <table>
    <!-- 曜日を出力 日曜日は赤字、土曜日は黒字で出力-->
    <th style="color:red">Sun</th>
    <th>Mon</th>
    <th>Tue</th>
    <th>Wen</th>
    <th>Thu</th>
    <th>Fri</th>
    <th style="color:grey">Sut</th>
    <!-- ５週間の前提で上で設定した開始日から順に設定 -->
    <?php for ($i=0; $i<5; $i++): ?>
    <tr>
      <?php for ($j=0; $j<7; $j++): ?>
      <!-- 当月以外の日付は薄いグレーで出力 -->
        <?php if($date->format("M")==$month): ?>
          <td style="color:black"><?php echo $date->format("d"); ?></td>
        <?php else: ?>
          <td style="color:#F2F2F2"><?php echo $date->format("d"); ?></td>
        <?php endif; ?>
    <!-- 日付カウントアップ -->
        <?php $date->modify('+1 day'); ?>
      <?php endfor; ?>
    </tr>
    <?php endfor; ?>
  </table>
</div>
</body>
</html>
