<?php

// データベース接続情報を設定
$servername = "localhost";
$dbname = "login_database";
$db_username = "root";
$db_password = "rootpass";
$dsn = "mysql:host=$servername;dbname=$dbname;charset=utf8";

try {
    // データベースに接続
    $pdo = new PDO($dsn, $db_username, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // 接続エラーの場合はエラーメッセージを出力して終了
    echo json_encode(['error' => 'データベースに接続できませんでした。']);
    exit;
}

try {
    // 予約情報を取得するクエリを実行
    $sql = "SELECT date, start_time, end_time, room_name FROM reserve";
    $stmt = $pdo->query($sql);

    // 予約情報を取得してJSON形式で出力
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($reservations);

} catch (PDOException $e) {
    // エラーログを出力し、エラーメッセージをJSON形式で出力
    error_log("フェッチエラー: " . $e->getMessage());
    echo json_encode(['error' => '予約情報の取得中にエラーが発生しました。']);
    exit;
}
