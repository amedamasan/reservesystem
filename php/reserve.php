<?php

// セッション開始
session_start();

// ユーザー名をセッションから取得
$username = $_SESSION['username'] ?? null;

if ($username === null) {
    // ログインしていない場合はログインページにリダイレクト
    header("Location: login.php");
    exit;
}

// フォームから送信された予約情報を取得
$date = $_POST['date'] ?? null;
$start_time = $_POST['start_time'] ?? null;
$end_time = $_POST['end_time'] ?? null;
$room_name = $_POST['room_name'] ?? null;
$comment = $_POST['comment'] ?? null;

// データベース接続情報
$servername = "localhost";
$dbname = "login_database";
$db_username = "root";
$db_password = "rootpass";
$dsn = "mysql:host=$servername;dbname=$dbname;charset=utf8";

try {
    // データベース接続
    $pdo = new PDO($dsn, $db_username, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "データベース接続に失敗しました: " . $e->getMessage();
    exit;
}

try {
    // 予約情報の重複をチェック
    $sql = "SELECT COUNT(*) as count FROM reserve WHERE date = :date AND ((:start_time BETWEEN start_time AND end_time) OR (:end_time BETWEEN start_time AND end_time)) AND room_name = :room_name";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['date' => $date, 'start_time' => $start_time, 'end_time' => $end_time, 'room_name' => $room_name]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // すでに予約されている場合
    if ($row['count'] > 0) {
        http_response_code(400);
        echo json_encode(['error' => 'すでに予約されています。別日を選択してください。']);
        exit;
    }

    // 予約可能な場合
    $sql = "INSERT INTO reserve (username, date, start_time, end_time, room_name, comment) VALUES (:username, :date, :start_time, :end_time, :room_name, :comment)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'username' => $username,
        'date' => $date,
        'start_time' => $start_time,
        'end_time' => $end_time,
        'room_name' => $room_name,
        'comment' => $comment
    ]);

    echo json_encode(['success' => '予約が完了しました。']);
} catch (PDOException $e) {
    echo json_encode(['error' => '予約の処理中にエラーが発生しました。']);
    exit;
}
?>
