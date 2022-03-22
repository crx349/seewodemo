<?php
/**
 * 希沃开放平台api类
 * @author crx349 <842062626@qq.com> QQ 842062626
 * @link https://www.xmspace.net  
 * @version 1.0
 * @example
 * @return Array
 */


class SeeWo{


    private $AppId = "";  	//应用id
    private $AppSecret = "";	   		//应用密钥
    private $SchoolUid = ""; //班级id


    /**
     * 初始化
     * @param [type] $AppId 应用id
     * @param [type] $AppSecret   应用密钥
     * @param [type] $SchoolUid   班级uid
     */
    public function __construct($AppId = null,$AppSecret = null,$SchoolUid = null){
        if($AppId) $this->AppId = $AppId;
        if($AppSecret) $this->AppSecret = $AppSecret;
        if($SchoolUid) $this->SchoolUid = $SchoolUid;
    }

    /**
     * 公共头部
     * @param string $body
     * @param string $path
     * @return array
     */
    public function PHeader($body ="",$path =""){

        $data =[
            'x-sw-app-id' =>$this->AppId,
            'x-sw-content-md5' =>strtoupper(md5(json_encode($body))),
            'x-sw-req-path' =>$path,
            'x-sw-sign-type' =>'md5',
            'x-sw-timestamp' =>$this->msectime(),
            'x-sw-version' =>'2',
        ];

        $data['x-sw-sign'] = strtoupper(md5($this->AppSecret.$this->arr2str($data).$this->AppSecret));


        $header = [
            'Content-Type:application/json',
            'x-sw-app-id:'.$this->AppId,
            'x-sw-content-md5:'.strtoupper(md5(json_encode($body))),
            'x-sw-sign:'.$data['x-sw-sign'],
            'x-sw-req-path:'.urlencode($path),
            'x-sw-sign-type:md5',
            'x-sw-timestamp:'.$this->msectime(),
            'x-sw-version:2',
        ];

        return $header;

    }


    /**
     * 获取班级列表
     * @param string $body
     * @return bool|string
     */
    public function QueryClassDetailBySchoolUid($body){

        $url = "https://openapi.seewo.com/mis-basis/class-api/query-class-detail-by-school-uid";
        $path = "/mis-basis/class-api/query-class-detail-by-school-uid";

        $header = $this->PHeader($body,$path);
        header('content-type:application/json');
        return $this->go_curl($url,'POST',json_encode($body),'','5','',$header);
    }

    /**
     * 获取排课计划
     * @param string $body
     * @return bool|string
     */
    public function FindEnablePlanInSchool($body){

        $url = "https://openapi.seewo.com/timetable-api/timetable-plan/find-enable-plan-in-school";
        $path = "/timetable-api/timetable-plan/find-enable-plan-in-school";

        $header = $this->PHeader($body,$path);
        header('content-type:application/json');
        return $this->go_curl($url,'POST',json_encode($body),'','5','',$header);
    }

    /**
     * 通过课程类型查询课程列表
     * @param string $body
     * @return bool|string
     */
    public function ListByNameOrType($body){

        $url = "https://openapi.seewo.com/timetable-api/course/list-by-name-or-type";
        $path = "/timetable-api/course/list-by-name-or-type";

        $header = $this->PHeader($body,$path);
        header('content-type:application/json');
        return $this->go_curl($url,'POST',json_encode($body),'','5','',$header);
    }


    //获取排课计划：http://open.seewo.com/#/service/1389/doc/1854
    //1、先获取走班课程对应的班级
    //http://open.seewo.com/#/service/1389/doc/1797
    //
    //{
    // "courseReq":{
    //  "pageNo":"1",
    //  "planUid":"排课计划uid",
    //  "pageSize":"10",
    //  "type":"2"
    // }
    //}

    /**
     * 绑定学生
     * @param $body
     * @return bool|string
     */
    public function BindStudentToClass($body){
        $url = "https://openapi.seewo.com/mis-basis/student-api/bind-student-to-class";
        $path = "/mis-basis/student-api/bind-student-to-class";

        $header = $this->PHeader($body,$path);
        header('content-type:application/json');
        return $this->go_curl($url,'POST',json_encode($body),'','5','',$header);

    }
    /**
     * 批量添加学生
     * @param $body
     * @return bool|string
     */
    public function BatchSaveClassStudents($body){
        $url = "https://openapi.seewo.com/seewo-yunban-api/student-service/batch-save-class-students";
        $path = "/seewo-yunban-api/student-service/batch-save-class-students";

        $header = $this->PHeader($body,$path);
        header('content-type:application/json');
        return $this->go_curl($url,'POST',json_encode($body),'','5','',$header);

    }

	//https://open.seewo.com/#/service/1389/doc/1820
    /**
     * 根据学校id获取考勤
     * @param $body
     * @return bool|string
     */
    public function ListLessonRecordsConditional($body){
        $url = "https://openapi.seewo.com/seewo-yunban-api/attendance-service/list-lesson-records-conditional";
        $path = "/seewo-yunban-api/attendance-service/list-lesson-records-conditional";

        $header = $this->PHeader($body,$path);
        header('content-type:application/json');
        return $this->go_curl($url,'POST',json_encode($body),'','5','',$header);

    }

    /**
     * 根据学号删除学员
     * @param $body
     * @return bool|string
     */
    public function RemoveClassStudents($body){
        $url = "https://openapi.seewo.com/seewo-yunban-api/student-service/remove-class-students";
        $path = "/seewo-yunban-api/student-service/remove-class-students";

        $header = $this->PHeader($body,$path);
        header('content-type:application/json');
        return $this->go_curl($url,'POST',json_encode($body),'','5','',$header);

    }

    //通用提交接口
    public function CurrencyPost($body,$url){
        //$url = "https://openapi.seewo.com/seewo-yunban-api/student-service/remove-class-students";
        $path = substr($url,25);//"/seewo-yunban-api/student-service/remove-class-students";

        $header = $this->PHeader($body,$path);
        header('content-type:application/json');
        return $this->go_curl($url,'POST',json_encode($body),'','5','',$header);
    }
    /**
     * 公共模拟提交
     * @param $url
     * @param $type
     * @param bool $data
     * @param null $err_msg
     * @param int $timeout
     * @param array $cert_info
     * @param string $header
     * @return bool|string
     */
    private function go_curl($url, $type, $data = false, $err_msg = null, $timeout = 20, $cert_info = array(),$header ='')
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

    //返回当前的毫秒时间戳
    private function msectime() {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);

        return $msectime;
    }

    //数组转字符串
    private function arr2str($arr)
    {
        $ret = "";
        reset($arr);
        while (list($k, $v) = each($arr)) {
            $tmp = "$k" . "$v";
            $ret .= $tmp;
        }
        return $ret;
    }

    //写日志
    public function saveLog($data,$type='a')
    {
        $years = date('Y-m');
        //设置路径目录信息
        //$url = './log/' . $years . '/' . date('Ymd') . 'txt';
        $url = './' .$type.'_'. date('Ymd') . '.txt';
        $dir_name = dirname($url);
        //目录不存在就创建
        if (!file_exists($dir_name)) {
            //iconv防止中文名乱码
            $res = mkdir(iconv("UTF-8", "GBK", $dir_name), 0777, true);
        }
        $fp = fopen($url, "a");//打开文件资源通道 不存在则自动创建
        fwrite($fp, date("Y-m-d H:i:s") . var_export($data, true) . "\r\n");//写入文件
        fclose($fp);//关闭资源通道
    }
}