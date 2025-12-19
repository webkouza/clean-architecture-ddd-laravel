<?php

/**
 * 🎯 RegisterBookUseCase.php の詳細解説
 */

echo "=== 🎯 RegisterBookUseCase.php（管理人の役割）===\n\n";

echo "【場所】\n";
echo "ファイル: app/Application/Book/UseCase/RegisterBookUseCase.php\n";
echo "層: アプリケーション層\n";
echo "役割: ビジネスフローの制御\n\n";

echo "【実際のコード解説】\n\n";

echo "public function execute(RegisterBookCommand \$command): RegisterBookResponse\n";
echo "{\n";
echo "    // 1. 値オブジェクトを作成（バリデーション含む）\n";
echo "    \$title = new Title(\$command->getTitle());      // 「ハリーポッター」\n";
echo "    \$author = new Author(\$command->getAuthor());   // 「J.K.ローリング」\n";
echo "    \$isbn = new ISBN(\$command->getISBN());         // 「9784915512377」\n\n";

echo "    // 2. 重複チェック（ビジネスルール）\n";
echo "    \$existingBook = \$this->bookRepository->findByISBN(\$isbn);\n";
echo "    if (\$existingBook !== null) {\n";
echo "        throw new \\DomainException('このISBNの本は既に登録されています');\n";
echo "    }\n\n";

echo "    // 3. 新しいIDを生成\n";
echo "    \$bookId = new BookId(\$this->generateNewId());\n\n";

echo "    // 4. エンティティを作成\n";
echo "    \$book = new Book(\$bookId, \$title, \$author, \$isbn);\n\n";

echo "    // 5. 保存\n";
echo "    \$this->bookRepository->save(\$book);\n\n";

echo "    // 6. レスポンスを返す\n";
echo "    return new RegisterBookResponse(\n";
echo "        \$book->getId()->getValue(),\n";
echo "        \$book->getTitle()->getValue(),\n";
echo "        \$book->getAuthor()->getValue(),\n";
echo "        \$book->getISBN()->getValue()\n";
echo "    );\n";
echo "}\n\n";

echo "【ユースケースの仕事（詳細）】\n\n";

echo "ステップ1: 値オブジェクト作成\n";
echo "━━━━━━━━━━━━━━━━━━━━\n";
echo "- new Title('ハリーポッター')\n";
echo "  → Title.phpが呼ばれる\n";
echo "  → 空文字チェック、文字数チェック\n";
echo "  → OKならTitleオブジェクト完成\n\n";

echo "- new Author('J.K.ローリング')\n";
echo "  → Author.phpが呼ばれる\n";
echo "  → 同様のチェック\n\n";

echo "- new ISBN('9784915512377')\n";
echo "  → ISBN.phpが呼ばれる\n";
echo "  → ISBN形式チェック\n\n";

echo "ステップ2: 重複チェック\n";
echo "━━━━━━━━━━━━━━━━━━━━\n";
echo "- \$this->bookRepository->findByISBN(\$isbn)\n";
echo "  → EloquentBookRepository.phpが呼ばれる\n";
echo "  → データベースで同じISBNを検索\n";
echo "  → 見つかったらエラー、なければ続行\n\n";

echo "ステップ3: ID生成\n";
echo "━━━━━━━━━━━━━━━━━━━━\n";
echo "- \$this->generateNewId()\n";
echo "  → 新しいIDを作る（例：1234）\n";
echo "- new BookId(1234)\n";
echo "  → BookId.phpが呼ばれる\n";
echo "  → 正の数かチェック\n\n";

echo "ステップ4: エンティティ作成\n";
echo "━━━━━━━━━━━━━━━━━━━━\n";
echo "- new Book(\$bookId, \$title, \$author, \$isbn)\n";
echo "  → Book.phpが呼ばれる\n";
echo "  → 値オブジェクトを組み合わせて本を作る\n";
echo "  → 初期状態は「利用可能」\n\n";

echo "ステップ5: 保存\n";
echo "━━━━━━━━━━━━━━━━━━━━\n";
echo "- \$this->bookRepository->save(\$book)\n";
echo "  → EloquentBookRepository.phpが呼ばれる\n";
echo "  → データベースにINSERT\n\n";

echo "ステップ6: レスポンス作成\n";
echo "━━━━━━━━━━━━━━━━━━━━\n";
echo "- new RegisterBookResponse(...)\n";
echo "  → 結果をまとめたオブジェクトを作る\n";
echo "  → コントローラーに返す\n\n";

echo "【例え話】\n";
echo "ユースケース = レストランの「シェフ」\n";
echo "- 注文票（コマンド）を受け取る\n";
echo "- 材料（値オブジェクト）をチェック\n";
echo "- 在庫（重複）を確認\n";
echo "- 料理（エンティティ）を作る\n";
echo "- 冷蔵庫（リポジトリ）に保存\n";
echo "- 完成品をお客さんに渡す\n";
