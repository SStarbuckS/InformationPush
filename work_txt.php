<?php
date_default_timezone_set('PRC');
ini_set('session.gc_maxlifetime', "18000"); // 5小时  
ini_set("session.cookie_lifetime","18000"); // 5小时  

//如果不存在文本就禁止提交
if(!isset($_REQUEST['msg']))
{
  exit;
}

//获取发送数据数组
function getDataArray($MsgArray)
{
    $data = array(
        //要发送给的用户，@all为全部
        "touser" => "@all", 
        "toparty" => "@all", 
        "totag" => "@all", 
        "msgtype" => "text", 
        "agentid" => $MsgArray["agentid"], 
        'text' => array(
            'content' => $MsgArray["msg"]
            ),
        "safe" => 0,
        "enable_id_trans" => 0,
        "enable_duplicate_check" => 0,
        "duplicate_check_interval" => 1800
    );
    return $data;
}


//curl请求函数，微信都是通过该函数请求
function https_request($url, $data = null)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
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
 * 开始推送
 */

 if(!session_id()){
       session_start();
      }

$ACCESS_TOKEN ='';

if (isset($_SESSION['WorkACCESS_TOKEN'])) 
{
    //存在
    $ACCESS_TOKEN =$_SESSION['WorkACCESS_TOKEN'];
    echo '从缓存中获取的ACCESS_TOKEN';
}
else
{
    //替换你的ACCESS_TOKEN
    $ACCESS_TOKEN = json_decode(https_request("https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=企业ID&corpsecret=应用的Secret"),true)["access_token"];
    $_SESSION['WorkACCESS_TOKEN']=$ACCESS_TOKEN;
    echo '重获取的ACCESS_TOKEN';
}

//模板消息请求URL
$url = "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=".$ACCESS_TOKEN;
$MsgArray=array();

//推送的应用id
$MsgArray["agentid"]="";

//标题是可选值
if(!isset($_REQUEST['title'])){
   $MsgArray["title"]="新提醒";
}
else{
   $MsgArray["title"]=$_REQUEST['title'];
}
//推送的文本内容
$MsgArray["msg"]=$_REQUEST['msg'];

//推送时间
$MsgArray["time"]=date('Y-m-d h:i:s',time());
$MsgArray["url"]="http://script.haokaikai.cn/Remind/msg.php?title=".$MsgArray["title"]."&time=".$MsgArray["time"]."&msg=".$MsgArray["msg"];
//转化成json数组让微信可以接收
$json_data = json_encode(getDataArray($MsgArray));
//echo $json_data;exit;
$res = https_request($url, urldecode($json_data));//请求开始
$res = json_decode($res, true);
if ($res['errcode'] == 0 && $res['errcode'] == "ok") {
    echo "发送成功！<br/>";
}
else{
     echo "发送失败<br/>";
}
