# 自動点呼システム リファクタリングガイド
## Fatコントローラー → コントローラー / サービス / リポジトリ の3層構造へ

---

## 現状の問題

現在のコードはこのような状態になっています。

```
❌ 現状（問題あり）

コントローラー
├── HTTPリクエストの処理
├── バリデーション
├── DBへの直接アクセス（Eloquentをそのまま使用）
├── ビジネスロジック（点呼の判定など）
└── レスポンスの返却

サービス（あるものとないものが混在）
└── ロジックが書いてある場合もある

リポジトリ → 存在しない
```

問題点：
- コントローラーが肥大化して読みにくい
- 同じロジックが複数のコントローラーにコピーされる
- テストが書きにくい
- 仕様変更のたびに影響範囲が広い

---

## 目指す姿

```
✅ 目標（3層構造）

コントローラー  → HTTPの入出力だけ担当
サービス       → ビジネスロジックだけ担当
リポジトリ     → DBアクセスだけ担当
```

---

## 各層の責任

### コントローラー（Controller）
「外の世界とアプリをつなぐ窓口」

- やること：リクエストを受け取り、サービスを呼び、レスポンスを返す
- やらないこと：ビジネスロジック、DB操作

```php
// ❌ Fatコントローラーの例（自動点呼システムのイメージ）
class RollCallController extends Controller
{
    public function store(Request $request)
    {
        // バリデーション
        $request->validate([...]);

        // DBに直接アクセス
        $driver = Driver::where('employee_id', $request->employee_id)->first();
        if (!$driver) {
            return response()->json(['error' => '運転者が見つかりません'], 404);
        }

        // ビジネスロジックが直書き
        if ($driver->alcohol_level > 0.15) {
            $driver->status = 'ng';
            $driver->save();
            // 管理者への通知処理...
        }

        // 点呼記録の保存
        RollCall::create([...]);

        return response()->json(['success' => true]);
    }
}
```

```php
// ✅ リファクタリング後のコントローラー
class RollCallController extends Controller
{
    public function __construct(
        private RollCallService $rollCallService
    ) {}

    public function store(RegisterRollCallRequest $request): JsonResponse
    {
        $result = $this->rollCallService->register($request->validated());
        return response()->json($result, 201);
    }
}
```

---

### サービス（Service）
「ビジネスロジックの司令塔」

- やること：点呼の合否判定、アルコール濃度チェック、通知の判断など
- やらないこと：HTTPの知識、SQL・Eloquentの直接操作

```php
// ✅ サービスの例
class RollCallService
{
    public function __construct(
        private RollCallRepositoryInterface $rollCallRepository,
        private DriverRepositoryInterface $driverRepository,
        private NotificationService $notificationService
    ) {}

    public function register(array $data): array
    {
        // 運転者の取得はリポジトリに任せる
        $driver = $this->driverRepository->findByEmployeeId($data['employee_id']);
        if (!$driver) {
            throw new \DomainException('運転者が見つかりません');
        }

        // ビジネスロジックはここに集約
        $status = $this->judgeAlcohol($data['alcohol_level']);

        if ($status === 'ng') {
            // NGの場合は管理者に通知
            $this->notificationService->notifyManager($driver);
        }

        // 保存はリポジトリに任せる
        $rollCall = $this->rollCallRepository->save([
            'driver_id'     => $driver->id,
            'alcohol_level' => $data['alcohol_level'],
            'status'        => $status,
            'called_at'     => now(),
        ]);

        return $rollCall->toArray();
    }

    // ビジネスルール：アルコール判定
    private function judgeAlcohol(float $level): string
    {
        return $level > 0.15 ? 'ng' : 'ok';
    }
}
```

---

### リポジトリ（Repository）
「DBアクセスの専門家」

- やること：DBへの保存・取得・更新・削除
- やらないこと：ビジネスロジック、HTTPの知識

まずインターフェース（約束事）を定義します。

```php
// インターフェース（どんなメソッドが必要かの約束）
interface RollCallRepositoryInterface
{
    public function save(array $data): RollCall;
    public function findById(int $id): ?RollCall;
    public function findByDriverId(int $driverId): array;
    public function findTodayByDriverId(int $driverId): array;
}
```

次に実装を書きます。

```php
// ✅ リポジトリの実装例
class EloquentRollCallRepository implements RollCallRepositoryInterface
{
    public function save(array $data): RollCall
    {
        return RollCall::create($data);
    }

    public function findById(int $id): ?RollCall
    {
        return RollCall::find($id);
    }

    public function findByDriverId(int $driverId): array
    {
        return RollCall::where('driver_id', $driverId)
            ->orderBy('called_at', 'desc')
            ->get()
            ->toArray();
    }

    public function findTodayByDriverId(int $driverId): array
    {
        return RollCall::where('driver_id', $driverId)
            ->whereDate('called_at', today())
            ->get()
            ->toArray();
    }
}
```

---

## リファクタリングの進め方

一気に全部変えようとすると壊れます。以下の順番で進めましょう。

### Step 1: リポジトリを作る（DBアクセスをコントローラーから分離）

コントローラーに直書きされているEloquentの操作をリポジトリに移します。

```
作業内容：
1. app/Repositories/ ディレクトリを作成
2. インターフェースを定義
3. Eloquentを使った実装クラスを作成
4. AppServiceProvider でバインド
5. コントローラーのDB操作をリポジトリ呼び出しに置き換え
```

### Step 2: サービスを整理する（ビジネスロジックをコントローラーから分離）

コントローラーに残っているビジネスロジックをサービスに移します。

```
作業内容：
1. app/Services/ ディレクトリを整理
2. コントローラーのロジックをサービスに移動
3. サービスがリポジトリを使うように変更
4. コントローラーはサービスを呼ぶだけにする
```

### Step 3: コントローラーをスリムにする

コントローラーに残るのはこれだけにします。

```
- バリデーション（FormRequest）
- サービスの呼び出し
- レスポンスの返却
```

---

## ディレクトリ構成（目標）

```
app/
├── Http/
│   ├── Controllers/
│   │   └── RollCallController.php   ← 薄くなる
│   └── Requests/
│       └── RegisterRollCallRequest.php
│
├── Services/
│   ├── RollCallService.php          ← ビジネスロジック
│   └── NotificationService.php
│
└── Repositories/
    ├── Interfaces/
    │   ├── RollCallRepositoryInterface.php
    │   └── DriverRepositoryInterface.php
    └── Eloquent/
        ├── EloquentRollCallRepository.php
        └── EloquentDriverRepository.php
```

---

## まとめ

| 層 | 役割 | 知っていいこと | 知らなくていいこと |
|---|---|---|---|
| コントローラー | 入出力の窓口 | HTTP、リクエスト、レスポンス | DB、ビジネスロジック |
| サービス | ビジネスロジック | ドメインのルール | HTTP、SQL |
| リポジトリ | DB操作 | Eloquent、SQL | HTTP、ビジネスロジック |

この3層に分けることで：
- コードが読みやすくなる
- 変更の影響範囲が小さくなる
- テストが書きやすくなる
- 複数人での開発がしやすくなる
