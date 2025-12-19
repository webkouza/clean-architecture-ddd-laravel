<?php

/**
 * 🎯 依存性注入（Dependency Injection）の説明
 */

echo "=== 🎯 依存性注入（Dependency Injection）===\n\n";

echo "【問題】\n";
echo "インターフェースと実装があっても、\n";
echo "「どの実装を使うか」を決める必要がある\n\n";

echo "❌ 悪い例：ユースケース内で実装を決める\n";
echo "class RegisterBookUseCase {\n";
echo "    public function __construct() {\n";
echo "        // ユースケース内で具体的な実装を決めている\n";
echo "        \$this->repository = new EloquentBookRepository();\n";
echo "    }\n";
echo "}\n\n";
echo "問題点：\n";
echo "・ユースケースが具体的な実装に依存\n";
echo "・テスト時に別の実装を使えない\n";
echo "・設定変更でコードを変更する必要がある\n\n";

echo "✅ 良い例：外部から注入する\n";
echo "class RegisterBookUseCase {\n";
echo "    public function __construct(BookRepositoryInterface \$repository) {\n";
echo "        // 外部から渡されたものを使う\n";
echo "        \$this->repository = \$repository;\n";
echo "    }\n";
echo "}\n\n";
echo "利点：\n";
echo "・ユースケースはインターフェースにのみ依存\n";
echo "・外部から好きな実装を渡せる\n";
echo "・テスト時は別の実装を渡せる\n\n";

echo "【Laravelでの依存性注入】\n\n";

echo "1. AppServiceProvider.phpで設定\n";
echo "\$this->app->bind(\n";
echo "    BookRepositoryInterface::class,  // インターフェース\n";
echo "    EloquentBookRepository::class    // 実装\n";
echo ");\n\n";

echo "2. Laravelが自動で注入\n";
echo "// コントローラーのコンストラクタ\n";
echo "public function __construct(RegisterBookUseCase \$useCase) {\n";
echo "    // Laravelが自動で以下を実行：\n";
echo "    // 1. BookRepositoryInterfaceが必要\n";
echo "    // 2. 設定を見る → EloquentBookRepositoryを使う\n";
echo "    // 3. EloquentBookRepositoryを作成\n";
echo "    // 4. RegisterBookUseCaseに渡す\n";
echo "    // 5. コントローラーに渡す\n";
echo "}\n\n";

echo "【環境別の設定例】\n\n";

echo "// 本番環境\n";
echo "\$this->app->bind(\n";
echo "    BookRepositoryInterface::class,\n";
echo "    EloquentBookRepository::class  // MySQL使用\n";
echo ");\n\n";

echo "// テスト環境\n";
echo "\$this->app->bind(\n";
echo "    BookRepositoryInterface::class,\n";
echo "    InMemoryBookRepository::class  // メモリ使用\n";
echo ");\n\n";

echo "// 開発環境\n";
echo "\$this->app->bind(\n";
echo "    BookRepositoryInterface::class,\n";
echo "    FileBookRepository::class      // ファイル使用\n";
echo ");\n\n";

echo "=== 🎉 依存性注入の利点 ===\n";
echo "✓ 設定ファイルを変更するだけで実装を切り替え可能\n";
echo "✓ コードを変更せずに環境別の設定が可能\n";
echo "✓ テスト時は自動でテスト用実装を使用\n";
echo "✓ 新しい実装を追加しても既存コードは変更不要\n";
