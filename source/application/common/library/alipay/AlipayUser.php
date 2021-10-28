<?php
/**
 * date: 2020/5/16 1:23 下午
 * @author 佐威网络科技-PaulAustin
 * @version v1.0
 * Description:
 */
namespace app\common\library\alipay;

vendor('aop.request.AlipayUserInfoShareRequest');

class AlipayUser extends AliBase{

    public function getUserInfo($accessToken){
        $request = new \AlipayUserInfoShareRequest();
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $result = $this->aop->execute($request, $accessToken)->$responseNode;
        return $result;
    }

    public function check($data,$rsa){
        $this->aop->rsaCheckV1($data,'null',$rsa);
    }

}