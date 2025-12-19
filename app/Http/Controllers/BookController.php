<?php

namespace App\Http\Controllers;

use App\Application\Book\UseCase\RegisterBookUseCase;
use App\Application\Book\UseCase\RegisterBookCommand;
use App\Http\Requests\RegisterBookRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;

/**
 * 本コントローラー
 *
 * コントローラーの役割：
 * - HTTPリクエストを受け取る
 * - リクエストをコマンドに変換する
 * - ユースケースを呼び出す
 * - レスポンスをHTTPレスポンスに変換する
 * - ビジネスロジックは持たない！
 */
class BookController extends Controller
{
    private RegisterBookUseCase $registerBookUseCase;

    public function __construct(RegisterBookUseCase $registerBookUseCase)
    {
        $this->registerBookUseCase = $registerBookUseCase;
    }

    /**
     * 本の一覧画面を表示
     */
    public function index()
    {
        return Inertia::render('Books/Index');
    }

    /**
     * 本を登録する（FormRequest + 値オブジェクト版）
     */
    public function store(RegisterBookRequest $request): JsonResponse
    {
        try {
            // 1. FormRequestで既にバリデーション済み
            // 2. HTTPリクエストからコマンドを作成
            $command = new RegisterBookCommand(
                $request->validated('title'),
                $request->validated('author'),
                $request->validated('isbn')
            );

            // 2. ユースケースを実行
            $response = $this->registerBookUseCase->execute($command);

            // 3. HTTPレスポンスを返す
            return response()->json([
                'success' => true,
                'message' => '本が正常に登録されました',
                'data' => $response->toArray()
            ], 201);

        } catch (\InvalidArgumentException $e) {
            // バリデーションエラー
            return response()->json([
                'success' => false,
                'message' => 'バリデーションエラー',
                'error' => $e->getMessage()
            ], 400);

        } catch (\DomainException $e) {
            // ビジネスルールエラー
            return response()->json([
                'success' => false,
                'message' => 'ビジネスルールエラー',
                'error' => $e->getMessage()
            ], 422);

        } catch (\Exception $e) {
            // その他のエラー
            return response()->json([
                'success' => false,
                'message' => 'システムエラーが発生しました',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
