<?php
/**
 * date: 2020/2/22 10:01 上午
 * @author 佐威网络科技-PaulAustin
 * @version v1.0
 * Description:
 */
namespace app\mobile\controller;

use app\api\model\dealer\Referee as RefereeModel;
use app\api\model\dealer\Withdraw;
use app\api\model\OrderAddress;
use app\api\model\Setting;
use app\store\model\AgentWith;
use app\store\model\store\User;
use think\Cache;
use think\Cookie;
use think\Queue;
use app\api\model\Order as OrderModel;

vendor('aop.AopClient');
vendor('aop.request.AlipayTradeWapPayRequest');
vendor('aop.requrest.AlipayTradePagePayRequest');

class Index extends Controller{
    /**
     * 微信H5首页
     */
    public function index(){
//        $orderInfo = (new \app\api\model\Order())->where("order_id",1228)->find();
//        $jobHandlerClassName = 'app\api\job\sync';
//        $jobQueueName = "orderBonus";
//        $jobData = ['ts' => time(), 'orderParam' => $orderInfo, 'a' => 1];
//        Queue::later(1, $jobHandlerClassName, $jobData, $jobQueueName);
//        $GLOBALS['user_ids'][] = 10151;
//        $data = $this->getParentMember(10151);
//        $this->bonus_lower_layer($data,10151);
        return $this->fetch("index");
    }

    public function getParentMember($user_id){
        // 查找自己的上级
        $parent_id = RefereeModel::getRefereeUserId($user_id,1,false);
        if($parent_id !== 0){
            // 放入查询到的用户
            $GLOBALS['user_ids'][] = $parent_id;
            // 没到头，继续递归查询
            $this->getParentMember($parent_id);
        }
        // 返回邀请一条线的集合
        return $GLOBALS['user_ids'];
    }

    /**
     * 金额分红设置大盘积分
     */
    public function bonus_lower_layer($data,$self_user_id){
        unset($GLOBALS['user_ids']);
        $orderModel = new OrderModel();
        foreach ($data as $key => $user_id){
            $GLOBALS['user_ids'][] = $user_id;
            $nowData = $this->getBottom($user_id);
            $meetPeople = [
                1 => 0,
                2 => 0,
                3 => 0
            ];
            foreach ($GLOBALS['lower_layer_user_ids'] as $value){
                // 挨个查找用户是否满足星级
                $total_price = $orderModel->where("user_id",$value)->where('pay_status',20)->sum("pay_price");
                if($this->checkMoneyMemberGrade($total_price) == 3){
                    $meetPeople[3]++;
                }
                if($this->checkMoneyMemberGrade($total_price) == 2){
                    $meetPeople[2]++;
                }
                if($this->checkMoneyMemberGrade($total_price) == 1){
                    $meetPeople[1]++;
                }
            }
            $level = 0;
            if($meetPeople[3] >= 2){
                // 底下用户满足两个3星人员，我自身的级别是4星
                $level = 4;
            }
            if($meetPeople[2] >= 2){
                // 底下用户满足两个3星人员，我自身的级别是4星
                $level = 3;
            }
            if($meetPeople[1] >= 2){
                // 底下用户满足两个3星人员，我自身的级别是4星
                $level = 2;
            }
            if(OrderModel::where('user_id',$self_user_id)->where("pay_status",20)->sum('pay_price') >= 20000){
                $level = 1;
            }
            if($level <= 0) continue;
            foreach ($nowData as $value){
                $parent = RefereeModel::getRefereeUserId($value,1,false);
                $self_general_score = $this->where("user_id",$value)->value("general_score");
                if($self_general_score < 12){
                    // 可给上级分配的大盘分红指数
                    $canBonus = bcsub(12,$self_general_score);
                    // 如果可分配大盘指数大于或者等于当前等级分配的指数，直接分配
                    if($canBonus >= $level){
                        $this->where("user_id",$parent)->setInc("market_index",$level);
                        $this->where("user_id",$value)->setInc("general_score",$level);
                    } else {
                        // 如果可分配大盘积分不足，将不分配
                        if($canBonus !== 0){
                            // 否则使用当前等级的分红指数减去可分配的分红指数，分给上级
                            $bonus = bcsub($level, $canBonus);
                            $this->where("user_id",$parent)->setInc("market_index",$bonus);
                            $this->where("user_id",$value)->setInc("general_score",$bonus);
                        }
                    }
                }
            }
            unset($GLOBALS['user_ids']);
        }
    }

    public function getBottom($user_id){
        $referee = RefereeModel::where('dealer_id',$user_id)->where("level",1)->field("user_id")->select();
        if(!empty($referee)){
            foreach ($referee as $key => $user_id){
                $GLOBALS['lower_layer_user_ids'][] = $user_id['user_id'];
                // 继续查询下层
                $this->getBottom($user_id['user_id']);
            }
        }
        return $GLOBALS['lower_layer_user_ids'];
    }

    public function checkMoneyMemberGrade($pay_price){
        if($pay_price >= 40000){
            return 4;
        }
        if($pay_price >= 30000){
            return 3;
        }
        if($pay_price >= 20000){
            return 2;
        }
        if($pay_price >= 10000){
            return 1;
        }
    }

    public function pay(){
        $aop = new \AopClient();
        $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
        $aop->appId = "2019072565978562";
        $aop->rsaPrivateKey = 'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCYYbQXup6l66CDlU7HMLoBPnzZb2vbbszyqoX0MY/J6Y/tXUUj/N+gHjfoaUGqsmvYqYRPHeWq/AzUv5h5b6slEnUrxBqM4XvX+j1sezYY5BpmcrCY0yFo3rKp//cxwmTE2DetjzkjxaNvGYv8O8e2B51EIF0kItY1ejtv1J8nuTqV//Z/5W77P3fh1+SpLCnQcCE+Q2I6PHIg+JrHo3i7aW9+TcnbEBWsfRQhQ9kaLJXIKkxbUWq3hFN83j33SjTj0TBSbolnYcEaZTGFL/Dk1t/+d97igacNHICVf5AUHvIs19o6Kz8swk21Nt8ucaGAQfeLNBMn8Wvd80rhZw/5AgMBAAECggEAL/7GHJs5EQWQPyGt+GtOisnFxS6lCC5z+xLesjVSFsSsApVYC6fAP8yEy30ro7oi4dEyzUCbcCmHWRMn0Ufs3fZcVXak7i6vNS2qXxrHxTtDXXVOTjRh14gdOhJXJ6MU3jNEBjSQSMYdXgbr4nxpwNnRHP4cmB8o3Rss0WJk5ruhhpQrlkYL3nNkNS3Soo1MmYSSz4Yzz3t/bZtJaOutfhdl4opXx+HgK6mYRD2LRC7PbNDACvq33SjOt3A8einVrcPF2mecwrJrtHqcov+t9E7prV19KEGUpYmQ3pRNdSWPGpinGtWR3TQjmZAqTg7Yp3ddIGTkDIrOEZ3JuYUfyQKBgQDd6Thv5bxErHmPDa0OxF98xe3qjTMpXon2057GIMFT9ld1Ah+huZbCXSY6Wq6uKk1P3AWxbBSN0izlfe+GUeLtAZX2iTD8H74NBD0vpFCFvTvNtJlFplanzhzKnC3iSm5fy79bXVvdwyJwjenkUnakQbVD6wDHpkahsCpCFiEkMwKBgQCvyjNSL67Buo4Es4AWpt2A4VHd8USQ/XBeRJxw6cpgYE5gQX3fR3pEbvE0olJm6n0hHkTwUANCDBn+AO4GHtBtEc/UfDTbKPFI5e8LQlKrhU3CGCxNabkvn9rlnsVJ9ZrKe8mfiRbB1xVmLGNt0ndfF2i0OV0Vd1A5OGDXfh9vIwKBgHqVufp+UzkwedofeOj+a602fY4jQA7rTZVPI5dZQtLJ81gMu0KQjgqCgHqd238UmS6zYWW4ScZqQyjnH6j4lT3NsXTGJowwUMFKBS7LOzwV4/JngH/sOlRqVJdMHUCzUzOSDvw+n1/qypyYmIrrTuS/8404RW8EmGH7OezV4qrnAoGAQn8zkLhW3BPM2zxnjKoMik8xn8o/jLVCR3ae0xutIW3s1/6ONeMc/jwVZDqu/x1eviNsIpex088uGeaSI6COaWPegVuGxwyi0VehaFnrWdSwSGbKg3Ilyj0nGctIiCSvLD9NUtsRz7uj7aWG6yk9XKxHQD2e15rtrueDwqCEFY8CgYEApX0ItR+79RhiBQZ+I8aeJ8IppafziTNoOE6SDkHByKMvCJo8xDgC6yjrUSZxVkThpHASD6a9wJrRxlfsi/RGwfektWMtLOaRCK4zP8KWmdjYNRIn6ISZUsuQqEAvPGaNbT/wNXIUXu6ossNb3eKSqO0GoWEmDQ+B0p2eDY3cnf4=';
        $aop->alipayrsaPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAtO/1gAPZtTo0mbgJcoM2azI2YlJd2dp94ff5hVEltFgzV2Cq8ytUJBKgJDmu/CtbqZ+2xPd+XB34OB/qOfx3MHpkurPA+cAZG/XgwHEHaCXRfaANubd0KVJ/XMYX2rHUFRTecqD7xtxx26Tmb0cvmgXXb824OeYm/p9S+tU+KWdK6xBRZ3wYJ38fdp1BylDias0h6Up/UtAdkunHy2hJytmnpIpiZLd4wpicBe4ZfTDxQdGgvnYTxACWt6i6XmQqMqdCbrVVqo4vVEQBVZfPJ0zNC+lxtUju1G+HXIvWRvAlW1EFIrROvdwPH/+RQzXKuLOPF78TznmMDwXjhzoe8wIDAQAB';
        $aop->signType = 'RSA2';
        $aop->postCharset = 'UTF-8';
        $aop->format = 'json';
        $request = new \AlipayTradeWapPayRequest();
        //SDK已经封装掉了公共参数，这里只需要传入业务参数，沙箱环境的product_code只能是FAST_INSTANT_TRADE_PAY
        $info = json_encode(['body'=>'商品简述','subject'=>'商品名','out_trade_no'=>md5(time()),
            'timeout_express'=>'30m','total_amount'=>'0.01','product_code'=>'QUICK_WAP_WAY'],JSON_UNESCAPED_UNICODE);
        $request->setNotifyUrl("nothing");
        $request->setBizContent($info);
        //这里和普通的接口调用不同，使用的是sdkExecute
        $response = $aop->pageExecute($request);
        //htmlspecialchars是为了输出到页面时防止被浏览器将关键参数html转义，实际打印到日志以及http传输不会有这个问题
        return htmlspecialchars_decode($response);//就是orderString 可以直接给客户端请求，无需再做处理。
    }

//    public function test(){
//        $model = new \app\api\model\dealer\Order();
//        $lists = $model->where("team_money_resource","neq","")->select();
//        foreach ($lists as $key => $value){
//            $nowV = json_decode($value['team_money_resource'],true);
//            echo "<table style='text-align:center;margin:0 auto;width:500px;'>";
//            foreach ($nowV as $k => $v){
//                echo "<tr><td>昵称</td><td>分润金额</td><td>奖金类型</td></tr>";
//                echo "<tr style='border:1px solid #888;'>";
//                echo "<td style='border:1px solid #888;'>" . $v['real_name'] . "</td>";
//                echo "<td style='border:1px solid #888;'>" . $v['money'] . "</td>";
//                echo "<td style='border:1px solid #888;'>团队奖</td>";
//                echo "</tr>";
//            }
//            echo "</table>";
//        }
//    }
}