<?php
//パラメータ妥当性を調べるためのチェック関数を定義
function validate_date($date)
{
  //フォーマットが正しいことを確認する。
  if(preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/',$date,$matches)) {
    //checkdate関数を用いて日付として許容かチェック
    //checkdateの引数はint型でmonth,day,year。preg_matchで得られた$matchesの配列を利用する
    return checkdate((int) $matches[2],(int) $matches[3],(int) $matches[1]);
  }else{
    return false;
  }
}

//月遷移ボタンから正しい値を受け取っているか判断した上でDateTimeクラスを設定
//不正な値を受け取っている場合は現在の月を表示させ、合わせてエラーメッセージも設定する。
if(isset($_GET['action'])){
  if(validate_date($_GET['action'])){
    $date = new DateTime($_GET['action']);
  }else{
    $date = new DateTime();
    $error_message='※日付に不正な値が入力されています。';
  }
}else{
  $date = new DateTime();
}

// 日付開始位置修正用配列
// 第1週には前月分最終週日付が入ることを考慮。
// 例　日:29 月:30 火:31 水:1 木:2 金:3 土:4
// この配列で、第1週当月1日までに前月が何日含まれるかを導出し、日付開始位置の調整に用いる
// 例えば、上記のように1日が水曜日だった場合、日～火には前月の最後の3日を設定する。
$week_array = [
  'Sunday' => 0,
  'Monday' => 1,
  'Tuesday'=> 2,
  'Wednesday' => 3,
  'Thursday' => 4,
  'Friday' => 5,
  'Saturday' => 6,
];
//当月1日取得(この時点で1日を設定しておく。)
$date->modify('first day of this month');
//カレンダー開始日を補正するための日数を取得
$start_date_correction = - $week_array[$date->format('l')];

// 当月以外の日付は薄いグレーで出力するために、当月の値を退避
$current_month = $date->format('M');
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
      $previous_month = $date->modify('-1 month')->format('Y-m-d');
      $next_month = $date->modify('+2 month')->format('Y-m-d');
      // 後の計算元の月に戻す
      $date->modify('-1 month');
      //今月の取得
      $new_date = new Datetime();
      $this_month = $new_date->format('Y-m-d');
      ?>
    <!-- ボタンの設定 -->
      <form action="index.php" method="get">
        <button type="submit" name="action" value=<?php echo $previous_month; ?>>先月</button>
        <button type="submit" name="action" value=<?php echo $this_month; ?> >今月</button>
        <button type="submit" name="action" value=<?php echo $next_month; ?> >翌月</button>
      </form>
      <!-- カレンダー日付の表示 -->
      <?php
      //カレンダー開始位置日付の取得
      $date->modify("+$start_date_correction day");
       ?>
       <!-- テーブルにカレンダーを設定 -->
      <table>
        <!-- 曜日を出力 日曜日は赤字、土曜日は黒字で出力-->
        <th class="sun">Sun</th>
        <th>Mon</th>
        <th>Tue</th>
        <th>Wen</th>
        <th>Thu</th>
        <th>Fri</th>
        <th class="sat">Sut</th>
        <!-- 最大6週間の前提で上で設定した開始日から順に設定 -->
        <?php for($i=0; $i<6; $i++): ?>
          <tr>
          <?php for($j=0; $j<7; $j++): ?>
          <!-- 当月以外の日付は薄いグレーで出力 -->
            <?php if($date->format('M')===$current_month): ?>
              <!-- 日曜日は赤字 -->
              <?php if($date->format('l')==='Sunday'): ?>
                <td class="sun"><?php echo $date->format('j'); ?></td>
              <?php else: ?>
                <td><?php echo $date->format('j'); ?></td>
              <?php endif; ?>
            <?php else: ?>
              <td class="grey"><?php echo $date->format('j'); ?></td>
            <?php endif; ?>
            <!-- 日付カウントアップ -->
            <?php $date->modify('+1 day'); ?>
          <?php endfor; ?>
          <!-- 翌月に切り替わっていたらそこでループから抜ける。
          当月の日付が含まれない週を出力しない。 -->
          <?php if($date->format('M')!==$current_month){
            break;
          } ?>
          </tr>
        <?php endfor; ?>
      </table>
      <!-- エラーメッセージがある場合、ここに出力 -->
      <?php if(isset($error_message)): ?>
        <p><?php echo $error_message; ?><p>
      <?php endif; ?>
    </div>
  </body>
</html>
