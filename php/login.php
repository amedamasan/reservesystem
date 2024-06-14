<?php
// セッションを開始
session_start();

// フォームが送信されたかどうかを確認
if(isset($_POST['user']) && isset($_POST['pass'])) {
    // フォームから送信されたユーザー名とパスワードを取得
    $username = $_POST['user'];
    $password = $_POST['pass'];

    // データベース接続の設定
    $servername = "localhost";
    $dbname = "login_database";
    $username_db = "root";
    $password_db = "rootpass";

    try {
        // PDOオブジェクトを作成してデータベースに接続
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username_db, $password_db);
        
        // エラーモードを例外に設定
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // ユーザーの認証を行うクエリを準備
        $query = "SELECT * FROM users WHERE username=:username AND password=:password";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->execute();

        // クエリの結果をチェックし、ログインを処理
        $authenticated = false;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // ユーザー名とパスワードが一致した場合、ログイン成功
            $authenticated = true;
            $_SESSION['username'] = $username; // ユーザー名をセッションに保存
            header("Location: calendar.php"); // 予約申請ページにリダイレクト
            exit;
        }
        
        // ユーザー名とパスワードが一致しない場合
        if (!$authenticated) {
            echo "ユーザー名またはパスワードが間違っています。";
        }
    } catch(PDOException $e) {
        echo "データベース接続エラー: " . $e->getMessage();
    }
}

// ログインフォームが表示されるHTMLなどのコードをここに追加

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/login.css" type="text/css" >
    <title>ログイン画面</title>
</head>
<body>
    <div id="form">
    <p class="form-title">Login</p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <p>ユーザー名</p>
        <p class="text"><input type="text" name="user"/></p>
        <p>パスワード</p>
        <p class="pass"><input type="password" name="pass"/></p>
        <p class="submit"><input type="submit" value="ログイン" /></p>
    </form>
    </div>
</body>
</html>