<?php

/**
 * 😵 悪い例：普通に書いたプログラム
 *
 * 問題点：
 * - 全部が混ざっている
 * - テストしにくい
 * - 変更しにくい
 * - バグが起きやすい
 */

// コントローラー（のつもり）
class BadBookController
{
    public function registerBook($title, $author, $isbn)
    {
        // 😵 バリデーションがコントローラーに書いてある
        if (empty($title)) {
            return "タイトルが空です";
        }

        if (strlen($title) > 200) {
            return "タイトルが長すぎます";
        }

        if (empty($author)) {
            return "著者が空です";
        }

        // 😵 データベース接続もコントローラーに書いてある
        $pdo = new PDO('mysql:host=localhost;dbname=test', 'user', 'pass');

        // 😵 重複チェックもコントローラーに書いてある
        $stmt = $pdo->prepare("SELECT * FROM books WHERE isbn = ?");
        $stmt->execute([$isbn]);
        if ($stmt->fetch()) {
            return "既に登録されています";
        }

        // 😵 保存処理もコントローラーに書いてある
        $stmt = $pdo->prepare("INSERT INTO books (title, author, isbn) VALUES (?, ?, ?)");
        $stmt->execute([$title, $author, $isbn]);

        return "登録成功";
    }
}

// 使い方
$controller = new BadBookController();
echo $controller->registerBook("ハリーポッター", "J.K.ローリング", "123456789");
