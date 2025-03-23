<?php
function request_by_curl($remote_server, $post_string) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $remote_server);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json;charset=utf-8'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

// 判断是否传入 msg 参数
if (!isset($_REQUEST['msg'])) {
    exit('Missing message parameter.');
}

// 预定义多个 Webhook Key（可以增加更多）
$wecom_keys = [
    "693axxx6-7aoc-4bc4-97a0-0ec2sifa5aaa",
    "abc123xx-1bcd-2efg-34hi-56789jklmnop",
    "xyz789xx-9rst-8uvw-76xy-54321abcdefg"
];

// 随机选择一个 Webhook Key
$selected_key = $wecom_keys[array_rand($wecom_keys)];

// 生成 Webhook API URL
$wecom_api_url = "https://qyapi.weixin.qq.com/cgi-bin/webhook/send?key=$selected_key";

// 获取消息内容
$message = $_REQUEST['msg'];

// 构造请求数据
$data = array(
    'msgtype' => 'text',
    'text' => array(
        'content' => $message
    )
);

// 将数据转换为 JSON
$data_string = json_encode($data, JSON_UNESCAPED_UNICODE);

// 发送请求
$result = request_by_curl($wecom_api_url, $data_string);

// 输出结果
echo $result;
?>
