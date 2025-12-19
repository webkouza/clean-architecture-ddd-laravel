<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>図書館管理システム - シンプル版</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6">📚 図書館管理システム</h1>

        <div class="bg-green-100 border border-green-400 rounded p-4 mb-6">
            <h2 class="text-xl font-semibold text-green-800">🎉 成功！</h2>
            <p class="text-green-700">クリーンアーキテクチャが正常に動作しています。</p>
        </div>

        <!-- 本登録フォーム -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h2 class="text-xl font-semibold mb-4">新しい本を登録</h2>

            <form id="bookForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">タイトル</label>
                    <input
                        id="title"
                        type="text"
                        class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                        placeholder="本のタイトルを入力"
                        required
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">著者</label>
                    <input
                        id="author"
                        type="text"
                        class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                        placeholder="著者名を入力"
                        required
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">ISBN</label>
                    <input
                        id="isbn"
                        type="text"
                        class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                        placeholder="ISBNを入力（例：9784048930567）"
                        required
                    >
                </div>

                <button
                    type="submit"
                    class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                >
                    本を登録
                </button>
            </form>

            <!-- メッセージ表示エリア -->
            <div id="message" class="mt-4 hidden"></div>
        </div>

        <!-- 登録された本の表示 -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">登録済みの本</h2>
            <div id="bookList" class="space-y-2">
                <p class="text-gray-500">まだ本が登録されていません。</p>
            </div>
        </div>
    </div>

    <script>
        // 本登録フォームの処理
        document.getElementById('bookForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const title = document.getElementById('title').value;
            const author = document.getElementById('author').value;
            const isbn = document.getElementById('isbn').value;

            try {
                const response = await fetch('/api/books', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ title, author, isbn })
                });

                const data = await response.json();
                const messageDiv = document.getElementById('message');

                if (data.success) {
                    messageDiv.className = 'mt-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded';
                    messageDiv.textContent = data.message;

                    // フォームをリセット
                    document.getElementById('bookForm').reset();

                    // 本リストに追加
                    addBookToList(data.data);
                } else {
                    messageDiv.className = 'mt-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded';
                    messageDiv.textContent = data.error || 'エラーが発生しました';
                }

                messageDiv.classList.remove('hidden');

            } catch (error) {
                const messageDiv = document.getElementById('message');
                messageDiv.className = 'mt-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded';
                messageDiv.textContent = 'ネットワークエラーが発生しました';
                messageDiv.classList.remove('hidden');
            }
        });

        // 本をリストに追加する関数
        function addBookToList(book) {
            const bookList = document.getElementById('bookList');

            // 初回の場合、「まだ本が登録されていません」を削除
            if (bookList.children.length === 1 && bookList.children[0].textContent.includes('まだ本が')) {
                bookList.innerHTML = '';
            }

            const bookDiv = document.createElement('div');
            bookDiv.className = 'border border-gray-200 rounded-lg p-4';
            bookDiv.innerHTML = `
                <h3 class="font-semibold text-lg">${book.title}</h3>
                <p class="text-gray-600">著者: ${book.author}</p>
                <p class="text-gray-600">ISBN: ${book.isbn}</p>
                <p class="text-sm text-green-600">状態: 利用可能</p>
            `;

            bookList.appendChild(bookDiv);
        }
    </script>
</body>
</html>
