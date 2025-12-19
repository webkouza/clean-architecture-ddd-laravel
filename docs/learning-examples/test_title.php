<?php

require_once 'vendor/autoload.php';

use App\Domain\Book\ValueObject\Title;

echo "=== 📝 Titleクラスのテスト ===\n\n";

// ✅ 正常なケース
try {
    $title1 = new Title("ハリーポッター");
    echo "✅ 成功: " . $title1->getValue() . "\n";
} catch (Exception $e) {
    echo "❌ エラー: " . $e->getMessage() . "\n";
}

// ✅ 前後に空白があるケース
try {
    $title2 = new Title("  クリーンアーキテクチャ  ");
    echo "✅ 成功（空白削除）: '" . $title2->getValue() . "'\n";
} catch (Exception $e) {
    echo "❌ エラー: " . $e->getMessage() . "\n";
}

echo "\n";

// ❌ 空のタイトル
try {
    $title3 = new Title("");
    echo "✅ 成功: " . $title3->getValue() . "\n";
} catch (Exception $e) {
    echo "❌ 期待通りエラー: " . $e->getMessage() . "\n";
}

// ❌ 空白だけのタイトル
try {
    $title4 = new Title("   ");
    echo "✅ 成功: " . $title4->getValue() . "\n";
} catch (Exception $e) {
    echo "❌ 期待通りエラー: " . $e->getMessage() . "\n";
}

// ❌ 長すぎるタイトル
try {
    $longTitle = str_repeat("あ", 201); // 「あ」を201回繰り返す
    $title5 = new Title($longTitle);
    echo "✅ 成功: " . $title5->getValue() . "\n";
} catch (Exception $e) {
    echo "❌ 期待通りエラー: " . $e->getMessage() . "\n";
}

echo "\n=== 🎯 値オブジェクトの利点 ===\n";
echo "1. 不正な値でオブジェクトを作れない\n";
echo "2. バリデーションが1箇所に集まる\n";
echo "3. 「タイトル」という意味が明確\n";
echo "4. 同じバリデーションを何度も書かなくて良い\n";
