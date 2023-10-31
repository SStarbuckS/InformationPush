<?php
function request_by_curl($remote_server, $post_string) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $remote_server);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json;charset=utf-8'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // 线下环境不用开启curl证书验证, 未调通情况可尝试添加该代码
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

if (!isset($_REQUEST['msg'])) {
    exit;
}

// 替换成你的Telegram Bot Token 和 Chat ID
$token = "YOUR_BOT_TOKEN";
$chat_id = "YOUR_CHAT_ID";

// 构造Telegram API发送消息的URL
$telegram_api_url = "https://api.telegram.org/bot$token/sendMessage";

// 接收的消息内容
$message = $_REQUEST['msg'];

// 构造POST数据
$data = array(
    'chat_id' => $chat_id,
    'text' => $message
);

$data_string = json_encode($data);

$result = request_by_curl($telegram_api_url, $data_string);
echo $result;
?>
