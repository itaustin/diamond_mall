<?php
/**
 * date: 2020/5/18 6:23 下午
 * @author 佐威网络科技-PaulAustin
 * @version v1.0
 * Description:
 */
namespace app\common\model;

use app\common\library\alipay\AlipayUser as AliPayUserModel;
vendor('aop.AopClient');

class AlipayTradeService {
    protected $publicKey = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAtO/1gAPZtTo0mbgJcoM2azI2YlJd2dp94ff5hVEltFgzV2Cq8ytUJBKgJDmu/CtbqZ+2xPd+XB34OB/qOfx3MHpkurPA+cAZG/XgwHEHaCXRfaANubd0KVJ/XMYX2rHUFRTecqD7xtxx26Tmb0cvmgXXb824OeYm/p9S+tU+KWdK6xBRZ3wYJ38fdp1BylDias0h6Up/UtAdkunHy2hJytmnpIpiZLd4wpicBe4ZfTDxQdGgvnYTxACWt6i6XmQqMqdCbrVVqo4vVEQBVZfPJ0zNC+lxtUju1G+HXIvWRvAlW1EFIrROvdwPH/+RQzXKuLOPF78TznmMDwXjhzoe8wIDAQAB";

    public function check($data){
        $model = new \AopClient();
        $model->alipayrsaPublicKey = $this->publicKey;
        return $model->rsaCheckV1($data,null,"RSA2");
    }
}