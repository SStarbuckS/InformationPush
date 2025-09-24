<?php
/**
 * =========================
 * 企业微信群机器人配置区
 * =========================
 */
$wecom_keys = [
    "693axxx6-7aoc-4bc4-97a0-0ec2sifa5aaa",
    "abc123xx-1bcd-2efg-34hi-56789jklmnop",
    "xyz789xx-9rst-8uvw-76xy-54321abcdefg"
]; // Webhook Key 列表，可添加更多

$api_base_url = "https://qyapi.weixin.qq.com"; // 企业微信API，可改为反代

// 如果不存在文本就禁止提交
if (!isset($_REQUEST['msg'])) {
    exit;
}

// curl请求函数，微信都是通过该函数请求
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

// 随机选择一个 Webhook Key
$selected_key = $wecom_keys[array_rand($wecom_keys)];

// 构造完整 Webhook URL（拼接路径）
$wecom_api_url = $api_base_url . "/cgi-bin/webhook/send?key=" . $selected_key;

// 获取消息内容
$message = $_REQUEST['msg'];

// 构造请求数据
$data = [
    'msgtype' => 'text',
    'text' => [
        'content' => $message
    ]
];

// 转化成json数组让微信可以接收
$json_data = json_encode($data, JSON_UNESCAPED_UNICODE);
$result = https_request($wecom_api_url, $json_data);

// 解析企业微信 API 响应并进行判断
$response = json_decode($result, true);
if ($response && $response['errcode'] == 0) {
    echo "Success";
} else {
    echo "Error: " . $result;
}
