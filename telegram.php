<?php
/**
 * =========================
 * Telegram Bot 配置区
 * =========================
 */

$token = "Token";  // Bot Token
$chat_id = "Chat_id";                                     // 接收消息的 Chat ID
$api_base_url = "https://api.telegram.org";                 // Telegram API，可改为反代

// 如果不存在文本就禁止提交
if (!isset($_REQUEST['msg'])) {
    exit;
}

// curl请求函数
function https_request($url, $data) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json;charset=utf-8'));
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}

/**
 * =========================
 * 开始推送
 * =========================
 */

// 构造 Telegram API URL
$webhook = $api_base_url . "/bot{$token}/sendMessage";

// 获取消息内容
$message = $_REQUEST['msg'];

// 构造请求数据
$data = [
    'chat_id' => $chat_id,
    'text' => $message
];

// 发送请求
$json_data = json_encode($data, JSON_UNESCAPED_UNICODE);
$result = https_request($webhook, $json_data);

// 解析响应
$response = json_decode($result, true);
if ($response && $response['ok'] == true) { 
    echo "Success";
} else {
    echo "Error: " . $result; // 原始响应，方便调试
}
