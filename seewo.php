<?php
// +----------------------------------------------------------------------
// | 希沃云班云平台对接例子
// +----------------------------------------------------------------------
// | Author: crx349 <842062626@qq.com> QQ 842062626
// +----------------------------------------------------------------------
// | Link: https://www.xmspace.net  
// +----------------------------------------------------------------------


$app_id = "";//开放平台申请的应用ID 云平台创建应用后就有了
$app_secret ="";//开放平台申请的应用ID通信密钥
$url = "https://openapi.seewo.com/mis-basis/class-api/query-class-detail-by-school-uid";//获取班级列表
$path = "/mis-basis/class-api/query-class-detail-by-school-uid";//请求路径

//请求体
$body = [
    'query'=>[
        'schoolUid'=>'',//学校uid 找客服要
        'appId'=>$app_id,
    ]
];
//公共请求结构
$data =[
    'x-sw-app-id' =>$app_id,
    'x-sw-content-md5' =>strtoupper(md5(json_encode($body))),
    'x-sw-req-path' =>$path,
    'x-sw-sign-type' =>'md5',
    'x-sw-timestamp' =>msectime(),
    'x-sw-version' =>'2',
];

$data['x-sw-sign'] = strtoupper(md5($app_secret.arr2str($data).$app_secret));


$header = [
    'Content-Type:application/json',
    'x-sw-app-id:'.$app_id,
    'x-sw-content-md5:'.strtoupper(md5(json_encode($body))),
    'x-sw-sign:'.$data['x-sw-sign'],
    'x-sw-req-path:'.urlencode($path),
    'x-sw-sign-type:md5',
    'x-sw-timestamp:'.msectime(),
    'x-sw-version:2',
];

header('content-type:application/json');
echo go_curl($url,'POST',json_encode($body),'','5','',$header);


//var_dump($app_secret.arr2str($data).$app_secret);
//var_dump(json_encode($body));

//var_dump($header);







//提交
function go_curl($url, $type, $data = false, $err_msg = null, $timeout = 20, $cert_info = array(),$header ='')
{
    $type = strtoupper($type);
    if ($type == 'GET' && is_array($data)) {
        $data = http_build_query($data);
    }
    $option = array();
    if ( $type == 'POST' ) {
        $option[CURLOPT_POST] = 1;
    }
    if ($data) {
        if ($type == 'POST') {
            $option[CURLOPT_POSTFIELDS] = $data;
        } elseif ($type == 'GET') {
            $url = strpos($url, '?') !== false ? $url.'&'.$data :  $url.'?'.$data;
        }
    }
    $option[CURLOPT_URL]            = $url;
    $option[CURLOPT_FOLLOWLOCATION] = TRUE;
    $option[CURLOPT_MAXREDIRS]      = 4;
    $option[CURLOPT_RETURNTRANSFER] = TRUE;
    $option[CURLOPT_TIMEOUT]        = $timeout;
    if(!empty($header)){
        $option[CURLOPT_HTTPHEADER]        = $header;
    }
    //设置证书信息
    if(!empty($cert_info) && !empty($cert_info['cert_file'])) {
        $option[CURLOPT_SSLCERT]       = $cert_info['cert_file'];
        $option[CURLOPT_SSLCERTPASSWD] = $cert_info['cert_pass'];
        $option[CURLOPT_SSLCERTTYPE]   = $cert_info['cert_type'];
    }
    //设置CA
    if(!empty($cert_info['ca_file'])) {
        // 对认证证书来源的检查，0表示阻止对证书的合法性的检查。1需要设置CURLOPT_CAINFO
        $option[CURLOPT_SSL_VERIFYPEER] = 1;
        $option[CURLOPT_CAINFO] = $cert_info['ca_file'];
    } else {
        // 对认证证书来源的检查，0表示阻止对证书的合法性的检查。1需要设置CURLOPT_CAINFO
        $option[CURLOPT_SSL_VERIFYPEER] = 0;
    }
    $ch = curl_init();
    curl_setopt_array($ch, $option);
    $response = curl_exec($ch);
    $curl_no  = curl_errno($ch);
    $curl_err = curl_error($ch);
    curl_close($ch);
    // error_log
    if($curl_no > 0) {
        if($err_msg !== null) {
            $err_msg = '('.$curl_no.')'.$curl_err;
        }
    }
    return $response;
}


//排序
function arraySort(array $param)
{
    ksort($param);
    reset($param);

    return $param;
}


//数组转字符串
function arr2str($arr)
{
    $ret = "";
    reset($arr);
    while (list($k, $v) = each($arr)) {
        $tmp = "$k" . "$v";
        $ret .= $tmp;
    }
    return $ret;
}

//返回当前的毫秒时间戳
function msectime() {
    list($msec, $sec) = explode(' ', microtime());
    $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);

return $msectime;
}