<?php

/**
 * 🎬 実際の処理を追いかけよう
 * 「ハリーポッター」という本を登録する場合
 */

echo "=== 🎬 「ハリーポッター」を登録する処理の流れ ===\n\n";

echo "【あなたがブラウザで操作】\n";
echo "1. タイトル: 「ハリーポッター」\n";
echo "2. 著者: 「J.K.ローリング」\n";
echo "3. ISBN: 「9784915512377」\n";
echo "4. 「登録」ボタンをクリック！\n\n";

echo "↓↓↓ ここから自動で処理が始まる ↓↓↓\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "ステップ1: ブラウザ → サーバーへ送信\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "ファイル: なし（HTTPリクエスト）\n";
echo "場所: インターネット\n";
echo "\n";
echo "送信内容:\n";
echo "{\n";
echo "  \"title\": \"ハリーポッター\",\n";
echo "  \"author\": \"J.K.ローリング\",\n";
echo "  \"isbn\": \"9784915512377\"\n";
echo "}\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "ステップ2: コントローラーが受け取る\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "ファイル: app/Http/Controllers/BookController.php\n";
echo "場所: プレゼンテーション層（玄関）\n";
echo "\n";
echo "処理内容:\n";
echo "1. HTTPリクエストを受け取る\n";
echo "2. データを取り出す\n";
echo "   - title = 「ハリーポッター」\n";
echo "   - author = 「J.K.ローリング」\n";
echo "   - isbn = 「9784915512377」\n";
echo "3. RegisterBookCommandを作る\n";
echo "4. RegisterBookUseCaseに渡す\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "ステップ3: ユースケースが処理する\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "ファイル: app/Application/Book/UseCase/RegisterBookUseCase.php\n";
echo "場所: アプリケーション層（管理人）\n";
echo "\n";
echo "処理内容:\n";
echo "1. 値オブジェクトを作る\n";
echo "   - new Title('ハリーポッター')\n";
echo "   - new Author('J.K.ローリング')\n";
echo "   - new ISBN('9784915512377')\n";
echo "2. 重複チェック\n";
echo "   - 同じISBNの本が既にないか確認\n";
echo "3. Bookエンティティを作る\n";
echo "4. リポジトリで保存\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "ステップ4: 値オブジェクトがチェック\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "ファイル: app/Domain/Book/ValueObject/Title.php\n";
echo "場所: ドメイン層（ビジネスルール）\n";
echo "\n";
echo "処理内容:\n";
echo "1. 空文字列じゃないかチェック → OK\n";
echo "2. 200文字以内かチェック → OK\n";
echo "3. 前後の空白を削除\n";
echo "4. Titleオブジェクト完成！\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "ステップ5: エンティティを作る\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "ファイル: app/Domain/Book/Entity/Book.php\n";
echo "場所: ドメイン層（ビジネスルール）\n";
echo "\n";
echo "処理内容:\n";
echo "1. 値オブジェクトを組み合わせる\n";
echo "2. 初期状態を「利用可能」に設定\n";
echo "3. Bookオブジェクト完成！\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "ステップ6: リポジトリで保存\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "ファイル: app/Infrastructure/Book/Repository/EloquentBookRepository.php\n";
echo "場所: インフラ層（データベース）\n";
echo "\n";
echo "処理内容:\n";
echo "1. BookエンティティをEloquentモデルに変換\n";
echo "2. データベースに保存\n";
echo "   INSERT INTO books (title, author, isbn, is_available)\n";
echo "   VALUES ('ハリーポッター', 'J.K.ローリング', '9784915512377', true)\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "ステップ7: 結果を返す\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "逆順で結果が返っていく:\n";
echo "リポジトリ → ユースケース → コントローラー → ブラウザ\n\n";

echo "ブラウザに表示:\n";
echo "{\n";
echo "  \"success\": true,\n";
echo "  \"message\": \"本が正常に登録されました\",\n";
echo "  \"data\": {\n";
echo "    \"id\": 1,\n";
echo "    \"title\": \"ハリーポッター\",\n";
echo "    \"author\": \"J.K.ローリング\",\n";
echo "    \"isbn\": \"9784915512377\"\n";
echo "  }\n";
echo "}\n\n";

echo "=== 🎉 完了！ ===\n";
