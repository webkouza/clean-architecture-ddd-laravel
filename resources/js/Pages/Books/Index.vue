<template>
  <div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">📚 図書館管理システム</h1>

    <!-- 本登録フォーム -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
      <h2 class="text-xl font-semibold mb-4">新しい本を登録</h2>

      <form @submit.prevent="registerBook" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">タイトル</label>
          <input
            v-model="form.title"
            type="text"
            class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
            placeholder="本のタイトルを入力"
            required
          >
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">著者</label>
          <input
            v-model="form.author"
            type="text"
            class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
            placeholder="著者名を入力"
            required
          >
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">ISBN</label>
          <input
            v-model="form.isbn"
            type="text"
            class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
            placeholder="ISBNを入力（例：9784048930567）"
            required
          >
        </div>

        <button
          type="submit"
          class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
          :disabled="loading"
        >
          {{ loading ? '登録中...' : '本を登録' }}
        </button>
      </form>

      <!-- エラーメッセージ -->
      <div v-if="error" class="mt-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
        {{ error }}
      </div>

      <!-- 成功メッセージ -->
      <div v-if="success" class="mt-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
        {{ success }}
      </div>
    </div>

    <!-- 本の一覧 -->
    <div class="bg-white p-6 rounded-lg shadow-md">
      <h2 class="text-xl font-semibold mb-4">登録済みの本</h2>

      <div v-if="books.length === 0" class="text-gray-500">
        まだ本が登録されていません。
      </div>

      <div v-else class="grid gap-4">
        <div
          v-for="book in books"
          :key="book.id"
          class="border border-gray-200 rounded-lg p-4"
        >
          <h3 class="font-semibold text-lg">{{ book.title }}</h3>
          <p class="text-gray-600">著者: {{ book.author }}</p>
          <p class="text-gray-600">ISBN: {{ book.isbn }}</p>
          <p class="text-sm">
            状態:
            <span :class="book.is_available ? 'text-green-600' : 'text-red-600'">
              {{ book.is_available ? '利用可能' : '貸し出し中' }}
            </span>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'

// リアクティブなデータ
const form = ref({
  title: '',
  author: '',
  isbn: ''
})

const books = ref([])
const loading = ref(false)
const error = ref('')
const success = ref('')

// 本を登録する関数
const registerBook = async () => {
  loading.value = true
  error.value = ''
  success.value = ''

  try {
    const response = await fetch('/api/books', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(form.value)
    })

    const data = await response.json()

    if (data.success) {
      success.value = data.message
      // フォームをリセット
      form.value = { title: '', author: '', isbn: '' }
      // 本の一覧を更新
      loadBooks()
    } else {
      error.value = data.error || 'エラーが発生しました'
    }
  } catch (err) {
    error.value = 'ネットワークエラーが発生しました'
  } finally {
    loading.value = false
  }
}

// 本の一覧を読み込む（今回は簡単にローカルストレージを使用）
const loadBooks = () => {
  // 実際のアプリでは、APIから取得します
  const savedBooks = localStorage.getItem('books')
  if (savedBooks) {
    books.value = JSON.parse(savedBooks)
  }
}

// コンポーネントがマウントされた時に実行
onMounted(() => {
  loadBooks()
})
</script>

<style scoped>
/* Tailwind CSSを使用しているので、追加のスタイルは不要 */
</style>
