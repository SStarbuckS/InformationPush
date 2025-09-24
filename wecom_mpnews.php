<?php
/**
 * =========================
 * 企业微信图文消息（mpnews）配置区
 * =========================
 */

$api_base_url = "https://qyapi.weixin.qq.com";  // 企业微信 API，可改为反代
$corpid = "Corpid";                 // 企业ID
$corpsecret = "Corpsecret"; // 应用密钥
$agentid = "Agentid";                            // 应用ID
$thumb_media_id = "Thumb_media_id"; // 缩略图ID
$author = "Author";                          // 作者
$title_default = "新提醒";                       // 默认标题

// 如果不存在文本就禁止提交
if (!isset($_REQUEST['msg'])) {
    exit;
}

// 获取发送数据数组
function getDataArray($MsgArray)
{
    global $agentid, $thumb_media_id, $author;
    $data = array(
        "touser" => "@all",
        "msgtype" => "mpnews",
        "agentid" => $agentid,
        "mpnews" => array(
            'articles' => array(
                array(
                    'title' => $MsgArray["title"],
                    "thumb_media_id" => $thumb_media_id,
                    "author" => $author,
                    'content' => str_replace(array("\n", "\r\n", "\r"), "<br>", $MsgArray["msg"]),
                    'digest' => $MsgArray["msg"]
                )
            )
        )
    );
    return $data;
}

// curl请求函数，微信都是通过该函数请求
function https_request($url, $data = null)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json;charset=utf-8'));
    if (!empty($data)) {
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
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

// 获取access_token
$ACCESS_TOKEN = json_decode(
    https_request("{$api_base_url}/cgi-bin/gettoken?corpid={$corpid}&corpsecret={$corpsecret}"),
    true
)["access_token"];

// 构造完整 Webhook URL（拼接路径）
$webhook = "{$api_base_url}/cgi-bin/message/send?access_token=" . $ACCESS_TOKEN;
$MsgArray = array();

// 获取消息标题
$MsgArray["title"] = isset($_REQUEST['title']) ? $_REQUEST['title'] : $title_default;

// 获取消息内容
$MsgArray["msg"] = $_REQUEST['msg'];

// 发送请求
$json_data = json_encode(getDataArray($MsgArray), JSON_UNESCAPED_UNICODE);
$result = https_request($webhook, $json_data);

// 解析响应
$response = json_decode($result, true);
if ($response && $response['errcode'] == 0) {
    echo "Success";
} else {
    echo "Error: " . $result; // 原始响应，方便调试
}
