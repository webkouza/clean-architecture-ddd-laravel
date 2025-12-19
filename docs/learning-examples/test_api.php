<?php

/**
 * API動作テスト用スクリプト
 */

// 本登録のテスト
$data = [
    'title' => 'クリーンアーキテクチャ',
    'author' => 'ロバート・C・マーチン',
    'isbn' => '9784048930567'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/api/books');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: $httpCode\n";
echo "Response: $response\n";
