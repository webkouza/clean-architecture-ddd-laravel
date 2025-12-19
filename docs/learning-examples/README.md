# 📚 学習用サンプルファイル

このディレクトリには、クリーンアーキテクチャとDDDの学習用に作成したPHPファイルが含まれています。

## 📁 ファイル一覧

### 🏗️ アーキテクチャの基本

- **`bad_example.php`** - 悪い設計の例（全部が混ざったコード）
- **`directory_explanation.php`** - ディレクトリ構成の詳細説明

### 📝 値オブジェクトとエンティティ

- **`test_title.php`** - Titleクラスの動作テスト
- **`test_book_entity.php`** - Bookエンティティの動作テスト

### 🔌 インターフェースと依存性逆転

- **`interface_example.php`** - インターフェースの基本例
- **`interface_real_world.php`** - 現実世界の例（コンセントの例え）
- **`good_dependency_example.php`** - 良い依存関係の例
- **`bad_dependency_example.php`** - 悪い依存関係の例
- **`interface_detailed_explanation.php`** - インターフェースの詳細解説
- **`dependency_injection_explanation.php`** - 依存性注入の説明

### 🎯 処理の流れ

- **`step_by_step_flow.php`** - 処理の流れを段階的に説明
- **`file_connections.php`** - ファイル間の繋がりを図解
- **`flow_explanation.php`** - 処理フローの詳細説明
- **`debug_flow.php`** - デバッグ用の動作確認

### 🏛️ 各層の説明

- **`controller_explanation.php`** - コントローラー層の詳細
- **`usecase_explanation.php`** - ユースケース層の詳細
- **`validation_comparison.php`** - バリデーション手法の比較

### 🧪 テスト

- **`simple_test.php`** - 簡単な動作テスト

## 🚀 実行方法

各ファイルは以下のコマンドで実行できます：

```bash
# プロジェクトルートから実行
php docs/learning-examples/ファイル名.php

# 例：
php docs/learning-examples/test_title.php
php docs/learning-examples/bad_example.php
```

## 📖 学習の順序

推奨する学習順序：

1. **`bad_example.php`** - まず悪い例を理解
2. **`directory_explanation.php`** - ディレクトリ構成を理解
3. **`test_title.php`** - 値オブジェクトを理解
4. **`test_book_entity.php`** - エンティティを理解
5. **`interface_real_world.php`** - インターフェースの概念を理解
6. **`good_dependency_example.php`** - 良い設計を理解
7. **`step_by_step_flow.php`** - 全体の流れを理解

## 🎯 学習のポイント

- 各ファイルを実行して、実際の動作を確認してください
- コードを読んで、なぜそのような設計になっているかを考えてください
- 悪い例と良い例を比較して、違いを理解してください

---

**💡 ヒント**: これらのファイルは学習用なので、自由に編集して実験してみてください！
