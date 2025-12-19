<?php

/**
 * 🎯 2つのバリデーションの違いと使い分け
 */

echo "=== 📝 FormRequest vs 値オブジェクト ===\n\n";

echo "【FormRequest（従来のLaravelバリデーション）】\n";
echo "場所: app/Http/Requests/\n";
echo "層: プレゼンテーション層（HTTP層）\n";
echo "責任: HTTPリクエストの形式チェック\n";
echo "\n";
echo "チェック内容:\n";
echo "  ✓ 必須項目が送られてきたか\n";
echo "  ✓ データ型は正しいか（文字列、数値など）\n";
echo "  ✓ 文字数制限\n";
echo "  ✓ 正規表現パターン\n";
echo "\n";
echo "例:\n";
echo "  'title' => 'required|string|max:200'\n";
echo "  → 「titleというキーが必須で、文字列で、200文字以内」\n";
echo "\n";
echo "特徴:\n";
echo "  ✓ Laravelフレームワークに依存\n";
echo "  ✓ HTTPリクエストでしか使えない\n";
echo "  ✓ 自動でエラーレスポンスを返す\n";
echo "  ✗ ビジネスルールはチェックできない\n";
echo "  ✗ 他のプロジェクトで再利用できない\n";
echo "\n\n";

echo "【値オブジェクト（DDDのバリデーション）】\n";
echo "場所: app/Domain/Book/ValueObject/\n";
echo "層: ドメイン層（ビジネス層）\n";
echo "責任: ビジネスルールの検証\n";
echo "\n";
echo "チェック内容:\n";
echo "  ✓ ビジネス的に正しい値か\n";
echo "  ✓ ドメインの制約を満たすか\n";
echo "  ✓ 値の意味が正しいか\n";
echo "\n";
echo "例:\n";
echo "  new Title('') → エラー\n";
echo "  → 「ビジネス的に、タイトルが空の本は存在しない」\n";
echo "\n";
echo "特徴:\n";
echo "  ✓ フレームワークに依存しない\n";
echo "  ✓ どこからでも使える（CLI、バッチ処理など）\n";
echo "  ✓ ビジネスルールを表現できる\n";
echo "  ✓ 他のプロジェクトで再利用できる\n";
echo "  ✗ 自動エラーレスポンスはない\n";
echo "\n\n";

echo "=== 🎯 使い分けの例 ===\n\n";

echo "【ケース1: Webフォームからの入力】\n";
echo "1. FormRequest: HTTPリクエストの形式チェック\n";
echo "   → 「titleというキーが送られてきたか」\n";
echo "   → 「文字列型か」\n";
echo "\n";
echo "2. 値オブジェクト: ビジネスルールチェック\n";
echo "   → 「タイトルとして意味があるか」\n";
echo "   → 「ビジネス的に許可される値か」\n";
echo "\n\n";

echo "【ケース2: CSVインポート（バッチ処理）】\n";
echo "1. FormRequestは使えない（HTTPリクエストじゃない）\n";
echo "2. 値オブジェクトは使える！\n";
echo "   → どこからでも同じビジネスルールを適用\n";
echo "\n\n";

echo "【ケース3: 外部APIからのデータ】\n";
echo "1. FormRequestは使えない（HTTPリクエストじゃない）\n";
echo "2. 値オブジェクトは使える！\n";
echo "   → 入力元に関わらず、同じルールを適用\n";
echo "\n\n";

echo "=== 🎓 結論：両方使う！ ===\n\n";
echo "【推奨される構成】\n";
echo "\n";
echo "1. FormRequest（プレゼンテーション層）\n";
echo "   役割: HTTPリクエストの形式チェック\n";
echo "   例: 'title' => 'required|string|max:200'\n";
echo "\n";
echo "2. 値オブジェクト（ドメイン層）\n";
echo "   役割: ビジネスルールの検証\n";
echo "   例: new Title($value) → ビジネス的に正しいかチェック\n";
echo "\n";
echo "【処理の流れ】\n";
echo "HTTPリクエスト\n";
echo "  ↓\n";
echo "FormRequest（形式チェック）\n";
echo "  ↓\n";
echo "Controller\n";
echo "  ↓\n";
echo "UseCase\n";
echo "  ↓\n";
echo "値オブジェクト（ビジネスルールチェック）\n";
echo "  ↓\n";
echo "Entity\n";
echo "\n";
echo "【メリット】\n";
echo "✓ 二重チェックで安全\n";
echo "✓ 責任が明確\n";
echo "✓ どちらも再利用可能\n";
