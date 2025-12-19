<?php

/**
 * 🏗️ ディレクトリ構成の詳細説明
 */

echo "=== 🏠 クリーンアーキテクチャのディレクトリ構成 ===\n\n";

echo "📁 Domain/ (ドメイン層) - 家の中心部\n";
echo "   役割: ビジネスルールとビジネス概念を定義\n";
echo "   特徴: フレームワークに依存しない、純粋なPHP\n";
echo "   \n";
echo "   📁 Entity/ - エンティティ（現実世界のモノ）\n";
echo "      例: Book.php - 本そのもの\n";
echo "      責任: ビジネスルール（貸し出し、返却など）\n";
echo "   \n";
echo "   📁 ValueObject/ - 値オブジェクト（属性）\n";
echo "      例: Title.php, Author.php, ISBN.php\n";
echo "      責任: バリデーション、値の表現\n";
echo "   \n";
echo "   📁 Repository/ - リポジトリインターフェース\n";
echo "      例: BookRepositoryInterface.php\n";
echo "      責任: データ保存の約束事を定義\n";
echo "\n";

echo "📁 Application/ (アプリケーション層) - 家の管理人\n";
echo "   役割: ユースケース（やりたいこと）を実行\n";
echo "   特徴: ドメインオブジェクトを組み合わせて処理\n";
echo "   \n";
echo "   📁 UseCase/ - ユースケース\n";
echo "      例: RegisterBookUseCase.php\n";
echo "      責任: 「本を登録する」という一連の処理\n";
echo "\n";

echo "📁 Infrastructure/ (インフラ層) - 家の設備\n";
echo "   役割: 外部システム（DB、API）との接続\n";
echo "   特徴: フレームワーク依存、技術的な詳細\n";
echo "   \n";
echo "   📁 Model/ - Eloquentモデル\n";
echo "      例: BookModel.php\n";
echo "      責任: データベーステーブルとの対応\n";
echo "   \n";
echo "   📁 Repository/ - リポジトリ実装\n";
echo "      例: EloquentBookRepository.php\n";
echo "      責任: 実際のデータ保存・取得処理\n";
echo "\n";

echo "📁 Http/ (プレゼンテーション層) - 家の玄関\n";
echo "   役割: 外部（ブラウザ、API）との接点\n";
echo "   特徴: HTTPリクエスト・レスポンスの処理\n";
echo "   \n";
echo "   📁 Controllers/ - コントローラー\n";
echo "      例: BookController.php\n";
echo "      責任: HTTPリクエストを受けて、ユースケースを呼ぶ\n";
echo "\n";

echo "=== 🔄 依存の向き（重要！） ===\n";
echo "Http → Application → Domain ← Infrastructure\n";
echo "                      ↑\n";
echo "                   すべてがここに依存\n";
echo "\n";

echo "=== 🎯 なぜこの構成？ ===\n";
echo "1. 変更に強い: データベースを変えてもビジネスロジックは影響なし\n";
echo "2. テストしやすい: ドメイン層は単体でテスト可能\n";
echo "3. 理解しやすい: ビジネスルールがドメイン層に集約\n";
echo "4. 再利用しやすい: ドメイン層は他のプロジェクトでも使える\n";
