<?php

/**
 * 🔗 ファイル同士の繋がりを図で理解
 */

echo "=== 🔗 ファイル同士の繋がり ===\n\n";

echo "【ブラウザで「登録」ボタンを押すと...】\n\n";

echo "1️⃣ BookController.php が呼ばれる\n";
echo "┌─────────────────────────────────────┐\n";
echo "│ app/Http/Controllers/BookController.php │\n";
echo "│                                         │\n";
echo "│ public function store(\$request) {       │\n";
echo "│   \$command = new RegisterBookCommand(); │ ← コマンド作成\n";
echo "│   \$this->useCase->execute(\$command);   │ ← ユースケース呼び出し\n";
echo "│ }                                       │\n";
echo "└─────────────────────────────────────┘\n";
echo "                    ↓ \$command を渡す\n\n";

echo "2️⃣ RegisterBookUseCase.php が呼ばれる\n";
echo "┌─────────────────────────────────────────┐\n";
echo "│ app/Application/Book/UseCase/            │\n";
echo "│ RegisterBookUseCase.php                  │\n";
echo "│                                         │\n";
echo "│ public function execute(\$command) {     │\n";
echo "│   \$title = new Title(\$command->title); │ ← 値オブジェクト作成\n";
echo "│   \$book = new Book(\$title, ...);       │ ← エンティティ作成\n";
echo "│   \$this->repository->save(\$book);      │ ← リポジトリ呼び出し\n";
echo "│ }                                       │\n";
echo "└─────────────────────────────────────────┘\n";
echo "         ↓ new Title()          ↓ \$this->repository->save()\n\n";

echo "3️⃣ Title.php が呼ばれる        4️⃣ EloquentBookRepository.php が呼ばれる\n";
echo "┌─────────────────────┐    ┌─────────────────────────────────┐\n";
echo "│ app/Domain/Book/     │    │ app/Infrastructure/Book/        │\n";
echo "│ ValueObject/Title.php│    │ Repository/                     │\n";
echo "│                     │    │ EloquentBookRepository.php      │\n";
echo "│ public function     │    │                                 │\n";
echo "│ __construct(\$value) {│    │ public function save(\$book) {   │\n";
echo "│   if (empty(\$value)) │    │   \$model = new BookModel();    │\n";
echo "│     throw Error;    │    │   \$model->title = \$book->...;  │\n";
echo "│   \$this->value =    │    │   \$model->save();              │\n";
echo "│     \$value;         │    │ }                               │\n";
echo "│ }                   │    │                                 │\n";
echo "└─────────────────────┘    └─────────────────────────────────┘\n";
echo "         ↓ バリデーション              ↓ データベース保存\n\n";

echo "5️⃣ Book.php が呼ばれる\n";
echo "┌─────────────────────────────────────┐\n";
echo "│ app/Domain/Book/Entity/Book.php     │\n";
echo "│                                     │\n";
echo "│ public function __construct(        │\n";
echo "│   \$id, \$title, \$author, \$isbn     │\n";
echo "│ ) {                                 │\n";
echo "│   \$this->id = \$id;                 │\n";
echo "│   \$this->title = \$title;           │\n";
echo "│   \$this->isAvailable = true;       │ ← 初期状態設定\n";
echo "│ }                                   │\n";
echo "└─────────────────────────────────────┘\n\n";

echo "=== 🎯 重要な繋がりのポイント ===\n\n";

echo "【1. use文での繋がり】\n";
echo "BookController.php の上部:\n";
echo "use App\\Application\\Book\\UseCase\\RegisterBookUseCase;\n";
echo "↑ これで RegisterBookUseCase を使えるようになる\n\n";

echo "RegisterBookUseCase.php の上部:\n";
echo "use App\\Domain\\Book\\ValueObject\\Title;\n";
echo "use App\\Domain\\Book\\Entity\\Book;\n";
echo "↑ これで Title や Book を使えるようになる\n\n";

echo "【2. コンストラクタでの繋がり】\n";
echo "BookController.php:\n";
echo "public function __construct(RegisterBookUseCase \$useCase) {\n";
echo "  \$this->useCase = \$useCase;  ← Laravelが自動で渡してくれる\n";
echo "}\n\n";

echo "RegisterBookUseCase.php:\n";
echo "public function __construct(BookRepositoryInterface \$repo) {\n";
echo "  \$this->repository = \$repo;  ← Laravelが自動で渡してくれる\n";
echo "}\n\n";

echo "【3. AppServiceProvider.php での設定】\n";
echo "app/Providers/AppServiceProvider.php:\n";
echo "\$this->app->bind(\n";
echo "  BookRepositoryInterface::class,     ← インターフェース\n";
echo "  EloquentBookRepository::class       ← 実際の実装\n";
echo ");\n";
echo "↑ 「BookRepositoryInterfaceが必要な時は\n";
echo "   EloquentBookRepositoryを使って」という設定\n\n";

echo "=== 🎉 まとめ ===\n";
echo "1. ブラウザ → BookController\n";
echo "2. BookController → RegisterBookUseCase\n";
echo "3. RegisterBookUseCase → Title, Author, ISBN, Book\n";
echo "4. RegisterBookUseCase → EloquentBookRepository\n";
echo "5. EloquentBookRepository → データベース\n";
echo "6. 結果が逆順で返っていく\n\n";

echo "各ファイルは「use文」と「コンストラクタ」で繋がっている！\n";
