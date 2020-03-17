<?php

//最初に、当カレンダープロジェクトで使用する関数validate_date,get_dow_css_classを定義する

/**
 * 入力された日付文字列の妥当性を調べるためのチェック関数
 *
 * @param String $date 'YYYY-MM-DD'形式
 *
 * @return boolean
 */
function validate_date($date)
{
  //フォーマットが正しいことを確認する。
  if(preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/',$date,$matches)){
    //preg_matchで得られた$matchesの配列を利用して日付として許容かチェック
    return checkdate((int) $matches[2],(int) $matches[3],(int) $matches[1]);
  }else{
    return false;
  }
}

/**
 * 曜日のcssクラス名を返す関数
 *
 * @param String $day 曜日(英字3桁表記)
 *
 * @return String cssクラス名
 */
function get_dow_css_class($day)
{
  if($day === "Sun"){
    $class = "Sun";
  }elseif($day === "Sat"){
    $class = "Sat";
  }else{
    $class = "week";
  }
    return $class;
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

// 曜日表示用配列
$week_array = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];

//当月1日取得(この時点で1日を設定しておく。)
$date->modify('first day of this month');
//カレンダー開始日を日曜日とするために、補正するための日数を取得。
$start_date_correction = -$date->format('w');
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
        <!-- 曜日を出力-->
        <?php
        foreach($week_array as $day){
          $class = get_dow_css_class($day);
          echo "<th class='{$class}'>{$day}</th>";
        }
        ?>
        <!-- 最大6週間の前提で上で設定した開始日から順に設定 -->
        <?php for($i = 0; $i < 6; $i++): ?>
          <tr>
          <?php
          for($j = 0; $j < 7; $j++){
            //当月以外の日付は薄いグレーで出力。
            if($date->format('M') === $current_month){
              //当月の日曜日は赤字で表示
              if($date->format('w') === "0"){
                echo "<td class='Sun'>{$date->format('j')}</td>";
              }else{
                echo "<td>{$date->format('j')}</td>";
              }
            }else{
              echo "<td class='grey'>{$date->format('j')}</td>";
            }
            //日付カウントアップ
            $date->modify('+1 day');
          }
          // 翌月に切り替わっていたらそこでループから抜ける。
          // 当月の日付が含まれない週を出力しない
          if($date->format('M') !== $current_month){
            break;
          }
          ?>
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
