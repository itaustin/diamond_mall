<?php


namespace app\api\model;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use think\Config;
use think\Model;
use think\Request;

class HuaweiSms extends Model
{
    private $url;
    private $APP_KEY;
    private $APP_SECRET;
    private $sender;
    private $TEMPLATE_ID;
    private $signature;
    private $interface;
    private $notifyCallBack;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $vv6Config = Config::get('huawei_sms');
        $this->url = $vv6Config['url'];
        $this->interface = $vv6Config['sms_interface'];
        $this->APP_KEY = $vv6Config['APP_KEY'];
        $this->APP_SECRET = $vv6Config['APP_SECRET'];
        $this->sender = $vv6Config['sender'];
        $this->TEMPLATE_ID = $vv6Config['TEMPLATE_ID'];
        $this->signature = $vv6Config['signature'];
        $this->notifyCallBack = $vv6Config['callback'];
    }

    public function sendsms($data){
        $url = $this->url.$this->interface['sms_batchSendSms'];
        $APP_KEY = $this->APP_KEY;
        $APP_SECRET = $this->APP_SECRET;
        $sender = $this->sender; //签名
        $TEMPLATE_ID = $this->TEMPLATE_ID;
        $signature = $this->signature;
        $receiver = $data['sms_phone']; //短信接收人号码
        //选填,短信状态报告接收地址,推荐使用域名,为空或者不填表示不接收状态报告
        $statusCallback = $this->notifyCallBack;
        /**
         * 选填,使用无变量模板时请赋空值 $TEMPLATE_PARAS = '';
         * 单变量模板示例:模板内容为"您的验证码是${NUM_6}"时,$TEMPLATE_PARAS可填写为 '["369751"]'
         * 双变量模板示例:模板内容为"您有${NUM_2}件快递请到${TXT_20}领取"时,$TEMPLATE_PARAS可填写为'["3","人民公园正门"]'
         * ${DATE}${TIME}变量不允许取值为空,${TXT_20}变量可以使用英文空格或点号替代空值,${NUM_6}变量可以使用0替代空值
         * 查看更多模板格式规范:产品介绍>模板和变量规范
         * @var string $TEMPLATE_PARAS
         */
        $TEMPLATE_PARAS = "[\"{$data['code']}\"]"; //模板变量
        $client = new Client();
        try {
            $response = $client->request('POST', $url, [
                'form_params' => [
                    'from' => $sender,
                    'to' => $receiver,
                    'templateId' => $TEMPLATE_ID,
                    'templateParas' => $TEMPLATE_PARAS,
                    'statusCallback' => $statusCallback,
                    'signature' => $signature //使用国内短信通用模板时,必须填写签名名称
                ],
                'headers' => [
                    'Authorization' => 'WSSE realm="SDP",profile="UsernameToken",type="Appkey"',
                    'X-WSSE' => $this->buildWsseHeader($APP_KEY, $APP_SECRET)
                ],
                'verify' => false //为防止因HTTPS证书认证失败造成API调用失败，需要先忽略证书信任问题
            ]);
            return \GuzzleHttp\json_decode($response->getBody()->__toString(),true);
        } catch (RequestException $e){
            echo $e;
            echo Psr7\str($e->getRequest()), "\n";
            if ($e->hasResponse()) {
                echo Psr7\str($e->getResponse());
            }
        }
    }

    /**
     * 构造X-WSSE参数值
     * @param string $appKey
     * @param string $appSecret
     * @return string
     */
    function buildWsseHeader(string $appKey, string $appSecret){
        $now = date('Y-m-d\TH:i:s\Z'); //Created
        $nonce = uniqid(); //Nonce
        $base64 = base64_encode(hash('sha256', ($nonce . $now . $appSecret))); //PasswordDigest
        return sprintf("UsernameToken Username=\"%s\",PasswordDigest=\"%s\",Nonce=\"%s\",Created=\"%s\"",
            $appKey, $base64, $nonce, $now);
    }

    public function notify(){
        $data = $this->request->param();
        file_put_contents(ROOT_PATH.DS."/../web/huawei_sms.txt",json_decode($this->request->param())
            ,FILE_APPEND);
        $keyValues = [];
        parse_str(urldecode($data), $keyValues); //解析状态报告数据

        /**
         * Example: 此处已解析status为例,请按需解析所需参数并自行实现相关处理
         *
         * 'smsMsgId': 短信唯一标识
         * 'total': 长短信拆分条数
         * 'sequence': 拆分后短信序号
         * 'source': 状态报告来源
         * 'updateTime': 资源更新时间
         * 'status': 状态码
         */
        $status = $keyValues['status']; // 状态报告枚举值
        // 通过status判断短信是否发送成功
        if ('DELIVRD' === strtoupper($status)) {
            file_put_contents(
                ROOT_PATH.DS."/../web/huawei_sms.txt",
                "Send sms success. smsMsgId: {$keyValues['smsMsgId']}，Error：{$status}".PHP_EOL.'\r\n',
                FILE_APPEND
            );
        } else {
            // 发送失败,打印status和orgCode
            file_put_contents(
                ROOT_PATH.DS."/../web/huawei_sms.txt",
                "Send sms Error. smsMsgId: {$keyValues['smsMsgId']}，Status：{$status}".PHP_EOL.'\r\n',
                FILE_APPEND
            );
        }
    }
}