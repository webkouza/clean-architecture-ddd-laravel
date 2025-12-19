<?php

/**
 * 🚪 BookController.php の詳細解説
 */

echo "=== 🚪 BookController.php（玄関の役割）===\n\n";

echo "【場所】\n";
echo "ファイル: app/Http/Controllers/BookController.php\n";
echo "層: プレゼンテーション層\n";
echo "役割: HTTPリクエストの受付窓口\n\n";

echo "【実際のコード解説】\n\n";

echo "public function store(RegisterBookRequest \$request): JsonResponse\n";
echo "{\n";
echo "    try {\n";
echo "        // 1. FormRequestで既にバリデーション済み\n";
echo "        // 2. HTTPリクエストからコマンドを作成\n";
echo "        \$command = new RegisterBookCommand(\n";
echo "            \$request->validated('title'),    // 「ハリーポッター」\n";
echo "            \$request->validated('author'),   // 「J.K.ローリング」\n";
echo "            \$request->validated('isbn')      // 「9784915512377」\n";
echo "        );\n\n";

echo "        // 3. ユースケースを実行\n";
echo "        \$response = \$this->registerBookUseCase->execute(\$command);\n\n";

echo "        // 4. HTTPレスポンスを返す\n";
echo "        return response()->json([\n";
echo "            'success' => true,\n";
echo "            'message' => '本が正常に登録されました',\n";
echo "            'data' => \$response->toArray()\n";
echo "        ], 201);\n";
echo "    } catch (...) {\n";
echo "        // エラー処理\n";
echo "    }\n";
echo "}\n\n";

echo "【コントローラーの仕事】\n";
echo "1. HTTPリクエストを受け取る\n";
echo "   → ブラウザから送られてきたJSONデータ\n\n";

echo "2. データを取り出す\n";
echo "   → \$request->validated('title') で「ハリーポッター」を取得\n\n";

echo "3. コマンドオブジェクトを作る\n";
echo "   → RegisterBookCommand に3つの値を渡す\n\n";

echo "4. ユースケースに丸投げ\n";
echo "   → 「あとはよろしく！」\n\n";

echo "5. 結果をHTTPレスポンスに変換\n";
echo "   → JSONでブラウザに返す\n\n";

echo "【重要なポイント】\n";
echo "❌ コントローラーでやってはいけないこと:\n";
echo "   - ビジネスロジック（本の重複チェックなど）\n";
echo "   - データベース操作\n";
echo "   - 複雑な計算\n\n";

echo "✅ コントローラーの仕事:\n";
echo "   - HTTPリクエストの受け取り\n";
echo "   - データの取り出し\n";
echo "   - ユースケースの呼び出し\n";
echo "   - HTTPレスポンスの返却\n\n";

echo "【例え話】\n";
echo "コントローラー = レストランの「注文受付係」\n";
echo "- お客さんから注文を聞く\n";
echo "- 注文票を作る\n";
echo "- 厨房（ユースケース）に渡す\n";
echo "- 料理ができたらお客さんに渡す\n";
echo "- 料理は作らない！\n";
