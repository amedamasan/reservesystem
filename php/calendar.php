<?php
session_start(); // セッションを開始

// セッションからユーザー名を取得
if(isset($_SESSION['username'])){
    $username = $_SESSION['username'];
} else {
    // ユーザー名がない場合は、ログインページにリダイレクト
    header("Location: login.php");
    exit;
}

//タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');



// 表示させる年月を設定
if(isset($_GET['ym'])){
    $ym = $_GET['ym'];
    $year = substr($ym, 0, 4);
    $month = substr($ym, 4, 2);
} else {
    // 現在の年月を取得
    $year = date('Y');
    $month = date('m');
    $ym = $year . $month;
}

// 翌月と前月の年月を取得
$next_year = date('Y', strtotime($year . '-' . $month . ' +1 month'));
$next_month = date('m', strtotime($year . '-' . $month . ' +1 month'));
$prev_year = date('Y', strtotime($year . '-' . $month . ' -1 month'));
$prev_month = date('m', strtotime($year . '-' . $month . ' -1 month'));

//月末日を取得
$end_month = date('t', strtotime($year . $month . '01'));
//1日の曜日を取得
$first_week = date('w', strtotime($year . $month . '01'));
//月末日の曜日を取得
$last_week = date('w', strtotime($year . $month . $end_month));

$aryCalendar = [];
$j = 0;

//1日開始曜日までの穴埋め
for($i = 0; $i < $first_week; $i++){
    $aryCalendar[$j][] = '';
}

//1日から月末日までループ
for($i = 1; $i <= $end_month; $i++){
    //日曜日まで進んだら改行
    if(isset($aryCalendar[$j]) && count($aryCalendar[$j]) === 7){
        $j++;
    }
    $aryCalendar[$j][] = $i; 
}

//月末曜日の穴埋め
for($i = count($aryCalendar[$j]); $i < 7; $i++){
    $aryCalendar[$j][] = '';
}

$aryWeek = ['日', '月', '火', '水', '木', '金', '土'];
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/calendar.css" type="text/css" >
  <title>予約カレンダー</title>
</head>
<body>
<main>
<input id="room1" type="radio" name="tab_item" value="roomA" checked><label class="tab_item" for="room1">面談室A</label>
<input id="room2" type="radio" name="tab_item" value="roomB"><label class="tab_item" for="room2">面談室B</label>
<input id="room3" type="radio" name="tab_item" value="roomC"><label class="tab_item" for="room3">面談室C</label>

<div class="tab_content" id="room1_content" data-room="roomA">
    <!-- カレンダーなどのコンテンツ -->
    <div>
        <h3><?php echo $year . "年" . $month . "月"; ?></h3>
        <h4>ようこそ<?php echo $username; ?>さん</h4>
        <div class="month">
            <a class="prev_month" href="?ym=<?php echo $prev_year . $prev_month; ?>">前月</a>
            <a class="next_month" href="?ym=<?php echo $next_year . $next_month; ?>">翌月</a>
        </div>
        <table class="calendar">
            <caption>面談室A</caption>
            <tr>
                <?php foreach($aryWeek as $week){ ?>
                    <th><?php echo $week ?></th>
                <?php } ?>
            </tr>
            <?php foreach($aryCalendar as $tr){ ?>
                <tr>
                    <?php foreach($tr as $td){ ?>
                        <?php if($td != '' && ($year.$month.str_pad($td, 2, '0', STR_PAD_LEFT)) >= date('Ymd')){ ?>
                            <td>
                                <?php if($td != date('j')){ ?>
                                    <a href="#" class="date-link" data-room="roomA" data-date="<?php echo $year . '-' . $month . '-' . $td; ?>"><?php echo $td ?></a>
                                <?php }else{ ?>
                                    <div class="today"><a href="#" class="date-link" data-room="roomA" data-date="<?php echo $year . '-' . $month . '-' . $td; ?>"><?php echo $td ?></a></div>
                                <?php } ?>
                            </td>
                        <?php }else{ ?>
                            <td><?php echo $td ?></td>
                        <?php } ?>
                    <?php } ?>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>


<div class="tab_content" id="room2_content">
    <div>
        <h3><?php echo $year . "年" . $month . "月"; ?></h3>
        <h4>ようこそ<?php echo $username; ?>さん</h4>
        <div class="month">
            <a class="prev_month" href="?ym=<?php echo $prev_year . $prev_month; ?>">前月</a>
            <a class="next_month" href="?ym=<?php echo $next_year . $next_month; ?>">翌月</a>
        </div>
        <table class="calendar">
            <caption>面談室B</caption>
            <tr>
                <?php foreach($aryWeek as $week){ ?>
                    <th><?php echo $week ?></th>
                <?php } ?>
            </tr>
            <?php foreach($aryCalendar as $tr){ ?>
                <tr>
                    <?php foreach($tr as $td){ ?>
                        <?php if($td != '' && ($year.$month.str_pad($td, 2, '0', STR_PAD_LEFT)) >= date('Ymd')){ ?>
                            <td>
                                <?php if($td != date('j')){ ?>
                                    <a href="#" class="date-link" data-room="roomB" data-date="<?php echo $year . '-' . $month . '-' . $td; ?>"><?php echo $td ?></a>
                                <?php }else{ ?>
                                    <a href="#" class="date-link today" data-room="roomB" data-date="<?php echo $year . '-' . $month . '-' . $td; ?>"><?php echo $td ?></a>
                                <?php } ?>
                            </td>
                        <?php }else{ ?>
                            <td><?php echo $td ?></td>
                        <?php } ?>
                    <?php } ?>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>

<div class="tab_content" id="room3_content">
    <div>
        <h3><?php echo $year . "年" . $month . "月"; ?></h3>
        <h4>ようこそ<?php echo $username; ?>さん</h4>
        <div class="month">
            <a class="prev_month" href="?ym=<?php echo $prev_year . $prev_month; ?>">前月</a>
            <a class="next_month" href="?ym=<?php echo $next_year . $next_month; ?>">翌月</a>
        </div>
        <table class="calendar">
            <caption>面談室C</caption>
            <tr>
                <?php foreach($aryWeek as $week){ ?>
                    <th><?php echo $week ?></th>
                <?php } ?>
            </tr>
            <?php foreach($aryCalendar as $tr){ ?>
                <tr>
                    <?php foreach($tr as $td){ ?>
                        <?php if($td != '' && ($year.$month.str_pad($td, 2, '0', STR_PAD_LEFT)) >= date('Ymd')){ ?>
                            <td>
                                <?php if($td != date('j')){ ?>
                                    <a href="#" class="date-link" data-room="roomC" data-date="<?php echo $year . '-' . $month . '-' . $td; ?>"><?php echo $td ?></a>
                                <?php }else{ ?>
                                    <a href="#" class="date-link today" data-room="roomC" data-date="<?php echo $year . '-' . $month . '-' . $td; ?>"><?php echo $td ?></a>
                                <?php } ?>
                            </td>
                        <?php }else{ ?>
                            <td><?php echo $td ?></td>
                        <?php } ?>
                    <?php } ?>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>

</main>

<form id="reservationForm" method="POST" action="./reserve.php">
    <h2>日時</h2>
    <div class="situation">
        <p>空き状況</p>
        <label data-value="10:00:00">10:00～10:50<span class="add">◯</span></label>
        <label data-value="11:00:00">11:00～11:50<span class="add">◯</span></label>
        <label data-value="12:00:00">12:00～12:50<span class="add">◯</span></label>
        <label data-value="13:00:00">13:00～13:50<span class="add">◯</span></label>
        <label data-value="14:00:00">14:00～14:50<span class="add">◯</span></label>
    </div>
    <div class="addmin">
        <p>予約</p>
        <label>10:00～10:50<input type="radio" name="time" value="10:00～10:50"></label>
        <label>11:00～11:50<input type="radio" name="time" value="11:00～11:50"></label>
        <label>12:00～12:50<input type="radio" name="time" value="12:00～12:50"></label>
        <label>13:00～13:50<input type="radio" name="time" value="13:00～13:50"></label>
        <label>14:00～14:50<input type="radio" name="time" value="14:00～14:50"></label>
    
        <p>コメント記入</p>
        <textarea name="comment" cols="50" rows="5" placeholder="入力欄"></textarea>
        <button type="button" id="openModal">予約する</button>
    </div>
</form>

    
    <!-- 予約確認モーダル -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <h2>予約確認</h2>
            <p>予約日付: <span id="reservation-date"></span></p>
            <p>予約時間: <span id="reservation-time"></span></p>
            <p>部屋:<span id="reservation-room"></span></p>
            <button class="close">キャンセル</button>
            <button class="confirm" id="confirm">確定</button>
        </div>
    </div>

    <div class="logout">
        <a href="logout.html">ログアウト</a>
    </div>

    <!-- JavaScriptファイルのリンク -->
    <script src="../js/calendar.js"></script>
</body>
</html>