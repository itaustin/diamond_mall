<?php
/**
 * date: 2020/5/16 1:23 下午
 * @author 佐威网络科技-PaulAustin
 * @version v1.0
 * Description:
 */
namespace app\common\library\alipay;

use app\common\exception\BaseException;
use think\Cache;
vendor('aop.AopClient');
vendor('aop.request.AlipayUserUserinfoShareRequest');
vendor('aop.request.AlipaySystemOauthTokenRequest');

class AliBase{
    public $aop;

    protected $appId;
    protected $gatewayUrl = "https://openapi.alipay.com/gateway.do";
    protected $rsaPrivateKey;
    protected $alipayrsaPublicKey;
    protected $signType = "RSA2";
    protected $postCharset;
    protected $format = "json";
    protected $timestamp;
    protected $error;
    protected $accessToken;
    protected $alipayPublicKey;

    /**
     * 构造函数
     * AliBase constructor.
     * @param null $appId
     * @param null $gatewayUrl
     * @param null $rsaPrivateKey
     * @param null $alipayrsaPublicKey
     * @param string $signType
     * @param string $postCharset
     * @param string $format
     */
    public function __construct($appId = null, $gatewayUrl = null, $rsaPrivateKey = null, $alipayrsaPublicKey = null, $alipayPublicKey = null, $signType = null, $postCharset = null, $format = null)
    {
        $this->aop = new \AopClient();
        $this->setConfig($appId, $gatewayUrl, $rsaPrivateKey, $alipayrsaPublicKey, $alipayPublicKey, $signType, $postCharset, $format);
    }

    protected function setConfig($appId = null, $gatewayUrl = null, $rsaPrivateKey = null, $alipayrsaPublicKey = null, $alipayPublicKey = null, $signType = null, $postCharset = null, $format = null)
    {
        !empty($appId) && $this->aop->gatewayUrl = $gatewayUrl;
        !empty($gatewayUrl) && $this->aop->appId = $appId;
        !empty($rsaPrivateKey) && $this->aop->rsaPrivateKey = $rsaPrivateKey;
        !empty($alipayrsaPublicKey) && $this->aop->alipayrsaPublicKey = $alipayrsaPublicKey;
        !empty($alipayPublicKey) && $this->aop->alipayPublicKey = $alipayPublicKey;
        !empty($signType) && $this->aop->signType = $signType;
        !empty($postCharset) && $this->aop->postCharset = $postCharset;
        !empty($format) && $this->aop->format = $format;
    }

    /**
     * 写入日志记录
     * @param $values
     * @return bool|int
     */
    protected function doLogs($values)
    {
        return log_write($values);
    }

    /**
     * 获取access_token
     * @return mixed
     * @throws BaseException
     */
    public function getAccessToken($auth_code = null)
    {
        $cacheKey = $this->appId . '@access_token';
//        if (!Cache::get($cacheKey)) {
//            // 请求API获取 access_token
//
//            // 写入缓存
//            Cache::set($cacheKey, get_object_vars($result->$responseNode), 2700);    // 7000
//        }
        $request = new \AlipaySystemOauthTokenRequest();
        $request->setGrantType("authorization_code");
        $request->setCode($auth_code);
        $result = $this->aop->execute($request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        if(!isset($result->$responseNode->access_token)){
            throw new BaseException(['code' => 10,'msg' => "支付宝access_token获取失败，错误信息：auth_code可能已失效"]);
        }
        // 记录日志
        $this->doLogs([
            'describe' => '获取access_token',
            'appId' => $this->appId,
            'result' => get_object_vars($result)
        ]);
        return get_object_vars($result->$responseNode);
        $this->accessToken = Cache::get($cacheKey);
        return Cache::get($cacheKey);
    }

    /**
     * 获取access_token
     * @return mixed
     * @throws BaseException
     */
    public function getRefreshToken($auth_code = null, $refresh_code = null)
    {
        $cacheKey = $this->appId . '@access_token';
        if (!Cache::get($cacheKey)) {
            // 请求API获取 access_token
            $request = new \AlipaySystemOauthTokenRequest();
            $request->setGrantType("refresh_token");
            $request->setCode($auth_code);
            $request->setRefreshToken($refresh_code);
            $result = $this->aop->execute($request);
            $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
            if(!isset($result->$responseNode->access_token)){
                throw new BaseException(['code' => 10,'msg' => "支付宝access_token获取失败，错误信息：auth_code可能已失效"]);
            }
            // 记录日志
            $this->doLogs([
                'describe' => '获取access_token',
                'appId' => $this->appId,
                'result' => get_object_vars($result)
            ]);
            // 写入缓存
            Cache::set($cacheKey, get_object_vars($result->$responseNode), 2700);    // 7000
        }
        $this->accessToken = Cache::get($cacheKey);
        return Cache::get($cacheKey);
    }

    /**
     * 模拟GET请求 HTTPS的页面
     * @param string $url 请求地址
     * @return string $result
     */
    protected function get($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    /**
     * 模拟POST请求
     * @param $url
     * @param array $data
     * @param bool $useCert
     * @param array $sslCert
     * @return mixed
     */
    protected function post($url, $data = [], $useCert = false, $sslCert = [])
    {
        $header = [
            'Content-type: application/json;'
        ];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        if ($useCert == true) {
            // 设置证书：cert 与 key 分别属于两个.pem文件
            curl_setopt($curl, CURLOPT_SSLCERTTYPE, 'PEM');
            curl_setopt($curl, CURLOPT_SSLCERT, $sslCert['certPem']);
            curl_setopt($curl, CURLOPT_SSLKEYTYPE, 'PEM');
            curl_setopt($curl, CURLOPT_SSLKEY, $sslCert['keyPem']);
        }
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    /**
     * 数组转json
     * @param $data
     * @return string
     */
    protected function jsonEncode($data)
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * json转数组
     * @param $json
     * @return mixed
     */
    protected function jsonDecode($json)
    {
        return json_decode($json, true);
    }

    /**
     * 返回错误信息
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }
}