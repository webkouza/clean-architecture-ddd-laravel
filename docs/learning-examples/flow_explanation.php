<?php

/**
 * 🎯 FormRequest + 値オブジェクトの処理の流れ
 */

echo "=== 🌊 処理の流れ（詳細版） ===\n\n";

echo "【1. HTTPリクエスト】\n";
echo "ブラウザから送信:\n";
echo "{\n";
echo "  \"title\": \"ハリーポッター\",\n";
echo "  \"author\": \"J.K.ローリング\",\n";
echo "  \"isbn\": \"9784915512377\"\n";
echo "}\n\n";

echo "【2. FormRequest（プレゼンテーション層）】\n";
echo "場所: app/Http/Requests/RegisterBookRequest.php\n";
echo "チェック内容:\n";
echo "  ✓ titleキーが存在するか\n";
echo "  ✓ titleが文字列か\n";
echo "  ✓ titleが200文字以内か\n";
echo "  ✓ authorキーが存在するか\n";
echo "  ✓ authorが文字列か\n";
echo "  ✓ authorが100文字以内か\n";
echo "  ✓ isbnキーが存在するか\n";
echo "  ✓ isbnが正規表現にマッチするか\n";
echo "\n";
echo "結果: HTTPリクエストとして正しい形式 ✓\n\n";

echo "【3. Controller（プレゼンテーション層）】\n";
echo "場所: app/Http/Controllers/BookController.php\n";
echo "処理:\n";
echo "  1. FormRequestから値を取得\n";
echo "  2. RegisterBookCommandを作成\n";
echo "  3. RegisterBookUseCaseを呼び出し\n";
echo "\n";

echo "【4. UseCase（アプリケーション層）】\n";
echo "場所: app/Application/Book/UseCase/RegisterBookUseCase.php\n";
echo "処理:\n";
echo "  1. 値オブジェクトを作成（ここでビジネスバリデーション）\n";
echo "     → new Title('ハリーポッター')\n";
echo "     → new Author('J.K.ローリング')\n";
echo "     → new ISBN('9784915512377')\n";
echo "  2. 重複チェック（ビジネスルール）\n";
echo "  3. Bookエンティティを作成\n";
echo "  4. リポジトリで保存\n";
echo "\n";

echo "【5. 値オブジェクト（ドメイン層）】\n";
echo "場所: app/Domain/Book/ValueObject/Title.php\n";
echo "チェック内容:\n";
echo "  ✓ ビジネス的にタイトルとして正しいか\n";
echo "  ✓ 空文字列でないか（ビジネスルール）\n";
echo "  ✓ 前後の空白を削除\n";
echo "  ✓ 文字数制限（ビジネス要件）\n";
echo "\n";
echo "結果: ビジネス的に正しいタイトル ✓\n\n";

echo "【6. Entity（ドメイン層）】\n";
echo "場所: app/Domain/Book/Entity/Book.php\n";
echo "処理:\n";
echo "  1. 値オブジェクトを組み合わせて本を作成\n";
echo "  2. 初期状態は「利用可能」に設定\n";
echo "\n";

echo "【7. Repository（インフラ層）】\n";
echo "場所: app/Infrastructure/Book/Repository/EloquentBookRepository.php\n";
echo "処理:\n";
echo "  1. ドメインエンティティをEloquentモデルに変換\n";
echo "  2. データベースに保存\n";
echo "\n";

echo "=== 🎯 二重バリデーションの利点 ===\n\n";

echo "【FormRequestで防げるもの】\n";
echo "❌ titleキーがない\n";
echo "❌ titleがnull\n";
echo "❌ titleが配列\n";
echo "❌ titleが数値\n";
echo "❌ isbnが不正な形式\n";
echo "\n";

echo "【値オブジェクトで防げるもの】\n";
echo "❌ 空文字列のタイトル（ビジネス的に無効）\n";
echo "❌ 空白だけのタイトル\n";
echo "❌ ビジネス要件を満たさない値\n";
echo "❌ ドメイン固有の制約違反\n";
echo "\n";

echo "【両方使うことで】\n";
echo "✓ HTTPレベルの安全性\n";
echo "✓ ビジネスレベルの安全性\n";
echo "✓ 責任の明確化\n";
echo "✓ 再利用性の向上\n";
echo "✓ テストのしやすさ\n";
