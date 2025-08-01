<?php
// --- 最終課題/api/ask_gemini.php (新規作成) ---

// このファイルが、JavaScriptとGemini APIをつなぐ「ウェイター」の役割をします。
header('Content-Type: application/json');
include '../includes/db_connect.php'; // DB接続とconfig.phpの読み込み


// ユーザーからのプロンプト（質問）を受け取る
$user_prompt = $_GET['prompt'] ?? '';
if (empty($user_prompt)) {
    http_response_code(400);
    echo json_encode(['error' => 'プロンプトが空です。']);
    exit;
}

// データベースから全てのレビューコメントを取得
$sql_reviews = "SELECT r.cafe_id, t.name, r.comment_text FROM reviews r JOIN tokyo_cafes t ON r.cafe_id = t.id";
$result_reviews = pg_query($dbconn, $sql_reviews);
$reviews_data = pg_fetch_all($result_reviews);
if ($reviews_data === false) {
    $reviews_data = [];
}

// AIに渡すためのレビューテキストを作成
$reviews_text = "";
foreach ($reviews_data as $review) {
    $reviews_text .= "cafe_id: " . $review['cafe_id'] . ", cafe_name: " . $review['name'] . ", comment: " . $review['comment_text'] . "\n";
}

// Gemini APIに投げるプロンプトを組み立てる
$api_prompt = "あなたは優秀なカフェ推薦アシスタントです。\n"
            . "以下のカフェのレビューリストを参考にして、ユーザーの要望に最も合うカフェを推薦してください。\n"
            . "ユーザーの要望: 「" . $user_prompt . "」\n\n"
            . "レビューリスト:\n" . $reviews_text . "\n"
            . "上記のレビューを分析し、最も関連性の高いカフェのcafe_idを最大3つまで、関連性の高い順にJSON配列の形式で返してください。例: [15, 8, 2]\n"
            . "余計な説明や前置きは一切不要です。JSON配列のみを返してください。";

// --- Gemini APIへのリクエスト ---
$api_key = GEMINI_API_KEY; // config.phpから安全にキーを取得
$api_url = '[https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=](https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=)' . $api_key;

$data = [
    'contents' => [
        [
            'parts' => [
                ['text' => $api_prompt]
            ]
        ]
    ]
];

$options = [
    'http' => [
        'header'  => "Content-type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($data),
    ],
];

$context  = stream_context_create($options);
$response_json = @file_get_contents($api_url, false, $context);

if ($response_json === FALSE) {
    http_response_code(500);
    echo json_encode(['error' => 'Gemini APIへの接続に失敗しました。']);
    exit;
}

$response_data = json_decode($response_json, true);
$ai_text_response = $response_data['candidates'][0]['content']['parts'][0]['text'] ?? '[]';

// AIの返答からJSON配列だけを抽出する
preg_match('/\[.*?\]/', $ai_text_response, $matches);
$cafe_ids_json = $matches[0] ?? '[]';

// JavaScriptに結果を返す
echo $cafe_ids_json;

pg_close($dbconn);
?>