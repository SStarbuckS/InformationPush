<?php
/**
 * =========================
 * 企业微信图文消息（mpnews）配置区
 * =========================
 */
$api_base_url = "https://qyapi.weixin.qq.com";  // 企业微信API，可改为反代
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
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}

/**
 * =========================
 * 开始推送
 * =========================
 */
$ACCESS_TOKEN = json_decode(
    https_request("{$api_base_url}/cgi-bin/gettoken?corpid={$corpid}&corpsecret={$corpsecret}"),
    true
)["access_token"];

$url = "{$api_base_url}/cgi-bin/message/send?access_token=" . $ACCESS_TOKEN;
$MsgArray = array();

// 标题
$MsgArray["title"] = isset($_REQUEST['title']) ? $_REQUEST['title'] : $title_default;

// 推送的文本内容
$MsgArray["msg"] = $_REQUEST['msg'];

// 转化成json数组让微信可以接收
$json_data = json_encode(getDataArray($MsgArray), JSON_UNESCAPED_UNICODE);
$res = https_request($url, $json_data);

// 解析企业微信API响应并进行判断
$response = json_decode($res, true);
if ($response && $response['errcode'] == 0) {
    echo "Success";
} else {
    echo "Error: " . $res; // 原始响应，方便调试
}
