<?php
/**
 * date: 2020/2/24 1:49 下午
 * @author 佐威网络科技-PaulAustin
 * @version v1.0
 * Description:
 */
namespace app\mobile\controller;

use app\common\enum\OrderType as OrderTypeEnum;
use app\common\service\order\Complete as OrderCompleteService;
use app\store\model\Goods;

class Order extends Controller
{
    public function index(){
        $this->redirect($this->order_lists());
    }

    public function order_lists(){
        return $this->fetch("order_lists");
    }

    public function checkout(){
        if(strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false){
            $this->assign("type","wechat");
        } else {
            $this->assign("type","other");
        }
        return $this->fetch("checkout");
    }

    public function detail(){
        return $this->fetch("detail");
    }

    public function refund(){
        return $this->fetch("refund");
    }

    public function comment(){
        return $this->fetch("comment");
    }

    public function cartOrder(){
        if(strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false){
            $this->assign("type","wechat");
        } else {
            $this->assign("type","other");
        }
        return $this->fetch("cart_order");
    }

    public function express(){
        return $this->fetch("express");
    }

    public function grant(){
        $order_id = $this->request->param('order_id');
        $order = \app\api\model\Order::detail($order_id);
        $goods_id = $order['goods'][0]['goods_id'];
        $mall_no = Goods::where('goods_id',$goods_id)->field('mall_no')->find()['mall_no'];
        if($mall_no == 1){
            $model = \app\api\model\Order::getUserOrderDetail($order_id, $order['user']['user_id']);
            $OrderCompleteService = new OrderCompleteService(OrderTypeEnum::MASTER);
            $OrderCompleteService->complete([$model], 10001);
            return [
                'code' => 1,
                'msg'  => "结算成功"
            ];
        }else{
            return [
                'code' => 0,
                'msg'  => '无法结算'
            ];
        }
    }
}