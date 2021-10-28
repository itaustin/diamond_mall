<?php
/**
 * date: 2020/5/16 10:40 上午
 * @author 佐威网络科技-PaulAustin
 * @version v1.0
 * Description:
 */
namespace app\mobile\controller;

use app\common\exception\BaseException;

vendor('aop.AopClient');
vendor('aop.request.AlipayUserUserinfoShareRequest');
vendor('aop.request.AlipaySystemOauthTokenRequest');

class Alipay extends Controller
{
    /**
     * @param
     * array(6) {
     * ["app_id"] => string(16) "2019072565978562"
     * ["source"] => string(13) "alipay_wallet"
     * ["userOutputs"] => string(9) "auth_user"
     * ["scope"] => string(9) "auth_user"
     * ["alipay_token"] => string(0) ""
     * ["auth_code"] => string(32) "67b9576c002f4b59a63d794dac73OX99"
     * }
     */
    public function getCode(){
        $param = $this->request->param();
        if(empty($param)){
            throw new BaseException(['msg' => '非法调用']);
        }
        foreach ($param as $key => $value){
            if($key == 'auth_code'){
                $request_uri = $this->request->domain() . '?s=/mobile/mine&' . $key . '=' . $value;
            }
        }
        $this->redirect($request_uri,301);
    }
}