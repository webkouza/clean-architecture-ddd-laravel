<?php

/**
 * 🎮 実際の動作確認用デバッグ
 */

echo "=== 🎮 実際に動かして確認しよう ===\n\n";

echo "【手順】\n";
echo "1. ブラウザで http://localhost:8080 を開く\n";
echo "2. 以下の情報を入力:\n";
echo "   - タイトル: デバッグ本\n";
echo "   - 著者: テスト著者\n";
echo "   - ISBN: 1234567890123\n";
echo "3. 「本を登録」ボタンを押す\n\n";

echo "【何が起きるか予想】\n\n";

echo "ステップ1: BookController.php\n";
echo "→ HTTPリクエストを受け取る\n";
echo "→ RegisterBookCommandを作る\n";
echo "→ RegisterBookUseCaseを呼ぶ\n\n";

echo "ステップ2: RegisterBookUseCase.php\n";
echo "→ new Title('デバッグ本')\n";
echo "→ new Author('テスト著者')\n";
echo "→ new ISBN('1234567890123')\n";
echo "→ 重複チェック\n";
echo "→ new Book(...)\n";
echo "→ repository->save()\n\n";

echo "ステップ3: Title.php\n";
echo "→ 'デバッグ本'をチェック\n";
echo "→ 空文字じゃない ✓\n";
echo "→ 200文字以内 ✓\n";
echo "→ Titleオブジェクト完成\n\n";

echo "ステップ4: EloquentBookRepository.php\n";
echo "→ BookエンティティをBookModelに変換\n";
echo "→ データベースにINSERT\n\n";

echo "【結果】\n";
echo "ブラウザに表示される:\n";
echo "{\n";
echo "  \"success\": true,\n";
echo "  \"message\": \"本が正常に登録されました\",\n";
echo "  \"data\": {\n";
echo "    \"id\": (自動生成されたID),\n";
echo "    \"title\": \"デバッグ本\",\n";
echo "    \"author\": \"テスト著者\",\n";
echo "    \"isbn\": \"1234567890123\"\n";
echo "  }\n";
echo "}\n\n";

echo "【エラーテストも試してみよう】\n\n";

echo "テスト1: 空のタイトル\n";
echo "→ Title.phpでエラー\n";
echo "→ 'タイトルは空にできません'\n\n";

echo "テスト2: 同じISBNで再登録\n";
echo "→ RegisterBookUseCase.phpでエラー\n";
echo "→ 'このISBNの本は既に登録されています'\n\n";

echo "=== 🎯 理解度チェック ===\n\n";

echo "Q1: 「本を登録」ボタンを押すと、最初に呼ばれるファイルは？\n";
echo "A1: BookController.php\n\n";

echo "Q2: バリデーション（空文字チェック）はどのファイルで行われる？\n";
echo "A2: Title.php, Author.php, ISBN.php（値オブジェクト）\n\n";

echo "Q3: データベースに保存するのはどのファイル？\n";
echo "A3: EloquentBookRepository.php\n\n";

echo "Q4: ビジネスルール（重複チェック）はどのファイルで行われる？\n";
echo "A4: RegisterBookUseCase.php\n\n";

echo "これらの質問に答えられたら、ファイルの繋がりが理解できています！\n";
