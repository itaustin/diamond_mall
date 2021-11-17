<?php

namespace app\api\model;

use app\api\model\Order as OrderModel;
use app\common\library\alipay\AlipayUser as AliPayUserModel;
use app\store\model\Setting as SettingModel;
use think\Cache;
use app\common\library\wechat\WxUser;
use app\common\exception\BaseException;
use app\common\model\User as UserModel;
use app\api\model\dealer\Referee as RefereeModel;
use app\api\model\dealer\Setting as DealerSettingModel;
use think\Cookie;
use think\Session;

/**
 * 用户模型类
 * Class User
 * @package app\api\model
 */
class User extends UserModel
{
    private $token;
    private $setting;

    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [
        'open_id',
        'is_delete',
        'wxapp_id',
        'create_time',
        'update_time'
    ];

    protected function initialize()
    {
        parent::initialize();
        $this->setting = SettingModel::getItem("teamcommission",10001)['1']['first_team'];
    }

    /**
     * 获取用户信息
     * @param $token
     * @return null|static
     * @throws \think\exception\DbException
     */
//    public static function getUser($token)
//    {
//        $openid = Cache::get($token)['openid'];
//        return self::detail(['username' => $openid], ['address', 'addressDefault']);
//    }

    public static function getUser($token)
    {
        $username = Cache::get($token)['username'];
        return self::detail(['username' => $username], ['address', 'addressDefault', 'grade']);
    }

    public static function setMemberInc($dealer_id,$level){
        $fields = [1 => 'first_num', 2 => 'second_num', 3 => 'third_num'];
        $model = static::detail($dealer_id);
        $model->setInc($fields[$level]);
    }

    /**
     * 用户登录
     * @param array $post
     * @return string
     * @throws BaseException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
//    public function login($post)
//    {
//        // 微信登录 获取session_key
//        $session = $this->wxlogin($post['code']);
//        // 自动注册用户
//        $refereeId = isset($post['referee_id']) ? $post['referee_id'] : null;
//        $userInfo = json_decode(htmlspecialchars_decode($post['user_info']), true);
//        $user_id = $this->register($session['openid'], $userInfo, $refereeId);
//        // 生成token (session3rd)
//        $this->token = $this->token($session['openid']);
//        // 记录缓存, 7天
//        Cache::set($this->token, $session, 86400 * 7);
//        return $user_id;
//    }

    /**
     * 用户登录
     * @param array $post
     * @return string
     * @throws BaseException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function login_bak($post)
    {
        /**
         * 调试模拟登陆
         * $this->token = $this->token("oIuSD5xyieo550vRLRUvfTaarjZE");
         * Cache::set($this->token, [
         * "openid" => "oIuSD5xyieo550vRLRUvfTaarjZE",
         * "user_info" => [
         * "nickName" => "A_Paul Austin",
         * "gender"   => 1,
         * "language" => 'zh-cn',
         * "city"     => 'Los',
         * "province" => '阿尔及利亚',
         * "country"  => '',
         * "avatarUrl"=> ''
         * ],
         * ], 86400 * 7);
         */
        // 微信登录 获取session_key
        $session = $this->wxloging($post['code']);
        // 自动注册用户
        $refereeId = isset($post['referee_id']) ? $post['referee_id'] : null;
//        $user = self::detail(['open_id' => $session['openid']]);
//        if(empty($user) && $refereeId == null){
//            throw new BaseException(['code' => 20,'msg' => '请扫描邀请码进入']);
//        }
        $user_id = $this->register($session['openid'], $session['user_info'], $refereeId);
        // 生成token (session3rd)
        $this->token = $this->token($session['openid']);
        // 记录缓存, 7天
        Cache::set($this->token, $session, 86400 * 7);
        return $user_id;
    }

    /**
     * 用户登录
     * @param $post
     */
    public function login($post){
        $model = new self();
//        unset($post['password']);
        $result = $model->where(array_merge($post,[
            "is_delete" => 0
        ]))->find();
        if(empty($result)){
            throw new BaseException(["msg" => "用户名或密码错误"]);
        }
        // 生成token
        $this->token = $this->token($result['username']);
        // 记录缓存, 7天
        Cache::set($this->token, $result, 86400 * 7);
        // 生成登录状态Session
        Session::set('zuowey_mobile',[
            "is_login" => 1,
            "detail" => $result->toArray(),
            "token" => $this->token
        ]);
        return $result['user_id'];
    }

    public function get_userinfo($code){
        if(!Cookie::get('user_info')){
            $session = $this->wxloging($code);
            Cookie::set('user_info',$session);
            return $session;
        }else{
            return Cookie::get('user_info');
        }
    }

    public function login_typetwo($post)
    {
        /**
         * 调试模拟登陆
         * $this->token = $this->token("oIuSD5xyieo550vRLRUvfTaarjZE");
         * Cache::set($this->token, [
         * "openid" => "oIuSD5xyieo550vRLRUvfTaarjZE",
         * "user_info" => [
         * "nickName" => "A_Paul Austin",
         * "gender"   => 1,
         * "language" => 'zh-cn',
         * "city"     => 'Los',
         * "province" => '阿尔及利亚',
         * "country"  => '',
         * "avatarUrl"=> ''
         * ],
         * ], 86400 * 7);
         */
        // 微信登录 获取session_key
        $session = $post;
        // 自动注册用户
        $refereeId = isset($post['referee_id']) ? $post['referee_id'] : null;
        $user = self::detail(['open_id' => $session['openid']]);
        if(empty($user) && $refereeId == null){
            throw new BaseException(['code' => 20,'msg' => '请扫描邀请码进入']);
        }
        $user_id = $this->register($session['openid'], $session['user_info'], $refereeId);
        // 生成token (session3rd)
        $this->token = $this->token($session['openid']);
        // 记录缓存, 7天
        Cache::set($this->token, $session, 86400 * 7);
        return $user_id;
    }

    public function login_pass($post){
        $model = new self;
        $result = $model->where($post)->find();
        if(!$result){
            throw new BaseException([
                'code' => 0,
                'msg' => '用户名或密码错误'
            ]);
        }
        // 生成token (session3rd)
        $this->token = $this->token($result['username']);
        $session = [
            'openid' => $result['username'],
            'username' => $result['username'],
            'user_info' => [
                "avatarUrl" => $result['avatarUrl'],
                "city" => $result['city'],
                "gender" => $result['gender'],
                "nickName" => $result['nickName'],
                "province" => $result['province']
            ]
        ];
        Session::set('zuowey_mobile',[
            "is_login" => 1,
            "detail" => $result->toArray(),
            "token" => $this->token
        ]);
//        curl("https://auction.itaustin.cn/mobile/index/set_session_id&session_id=" . session_id());
//        $data = json_decode(curlPost(
//            "https://auction.itaustin.cn/mobile/passport/remote_login",
//            [
//                "username" => $post['username'],
//                "password" => $post['password']
//            ]
//        ));
//        echo "<pre>";
//        print_r([
//            "username" => $post['username'],
//            "password" => $post['password']
//        ]);
//        echo "</pre>";
//        echo "<pre>";
//        print_r($data);
//        echo "</pre>";
//        exit();
        // 记录缓存, 7天
        Cache::set($this->token, $session, 86400 * 7);
        return $result['user_id'];
    }

    public function register_ali($post){
        // 自动注册用户
        $model = new UserModel();
        if($model->where('mobile_phone',$post['mobile_phone'])->find()){
            throw new BaseException(['code' => 0,'msg' => '账号已经注册，请点击右上角登录按钮进行登录']);
        }
        $code = Cache::get('sms_'.$post['mobile_phone']);
        if(number_format($code) !== number_format($post['code'])){
            throw new BaseException(['code' => 0,'msg' => '手机验证码错误']);
        }
        $refereeId = $model->where('mobile_phone',$post['referee_id'])->field('user_id')->find()['user_id'];
        if(empty($refereeId)){
            throw new BaseException(['code' => 0,'msg' => "推荐人不存在，请核实"]);
        }
        $session = Cookie::get('user_info');
        $user = self::detail(['open_id' => $session['openid']]);
        if(!empty($user)){
            throw new BaseException(['code' => 0,'msg' => "该微信用户已被其他账号绑定"]);
        }
        $user_id = $this->register($session['openid'],array_merge($session['user_info'],
            [
                'mobile_phone' => $post['mobile_phone'],
                'password' => zuowey_hash($post['password']),
            ]),$refereeId);
        // 先放置自己
//        $GLOBALS['user_ids'][] = $user_id;
//        $GLOBALS['lower_layer_user_ids'][] = $user_id;
//        $data = $this->getParentMember($user_id);

        /**
         * 第一种设置大盘分红方式
         */
//        $this->bonus($data);

        /**
         * 按照金额设置大盘积分
         */
        // 设置大盘分红第二种方式
//        $this->bonus_lower_layer($data,$user_id);
        // 设置大盘分红第二种方式结束
        $this->token = $this->token($session['openid']);
        // 记录缓存, 7天
        Cache::set($this->token, $session, 86400 * 7);
        return $user_id;
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

    /**
     * 检测用户是什么星级
     */
    public function checkMemberGrade($user_id){
        $userGradeFirst = RefereeModel::where('dealer_id',$user_id)->where("level",1)->count();
        $userGradeSecond = RefereeModel::where('dealer_id',$user_id)->where("level",2)->count();
        $grade = 0;
        // 一星
        if($userGradeFirst >= $this->setting['level_one']['first'] && bcadd($userGradeFirst,$userGradeSecond) >= $this->setting['level_one']['second']){
            $grade = 1;
        }
        // 二星
        if($userGradeFirst >= $this->setting['level_two']['first'] && bcadd($userGradeFirst,$userGradeSecond) >= $this->setting['level_two']['second']){
            $grade = 2;
        }
        // 三星
        if($userGradeFirst >= $this->setting['level_three']['first'] && bcadd($userGradeFirst,$userGradeSecond) >= $this->setting['level_three']['second']){
            $grade = 3;
        }
        // 四星
        if($userGradeFirst >= $this->setting['level_four']['first'] && bcadd($userGradeFirst,$userGradeSecond) >= $this->setting['level_four']['second']){
            $grade = 4;
        }
        return $grade;
    }

    public function bonus($data){
        unset($GLOBALS['user_ids']);
        foreach ($data as $key => $user_id){
            $GLOBALS['user_ids'] = [];
            $GLOBALS['user_ids'][] = $user_id;
            $level = $this->checkMemberGrade($user_id);
            if($level == 0) continue;
            $nowData = $this->getParentMember($user_id);
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

    /**
     * 金额分红设置大盘积分
     */
    public function bonus_lower_layer($data,$self_user_id){
        unset($GLOBALS['lower_layer_user_ids']);
        $orderModel = new OrderModel();
        foreach ($data as $key => $user_id){
            $GLOBALS['lower_layer_user_ids'] = [];
            $GLOBALS['lower_layer_user_ids'][] = $user_id;
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

    /**
     * 获取token
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * 微信公众号登录
     */
    private function wxloging($code)
    {
        // 获取当前公众号信息
        $wxConfig = Wxapp::getWxappCache();
        // 验证appid和appsecret是否填写
        if (empty($wxConfig['app_id']) || empty($wxConfig['app_secret'])) {
            throw new BaseException(['msg' => '请到 [后台-公众号设置] 填写appid 和 appsecret']);
        }
        $WxUser = new WxUser($wxConfig['app_id'],$wxConfig['app_secret']);
        if(!$session = $WxUser->openidKey($code)){
            throw new BaseException(['msg' => $WxUser->getError()]);
        }
        return $session;
    }

    /**
     * 微信登录
     * @param $code
     * @return array|mixed
     * @throws BaseException
     * @throws \think\exception\DbException
     */
    private function wxlogin($code)
    {
        // 获取当前公众号信息
        $wxConfig = Wxapp::getWxappCache();
        // 验证appid和appsecret是否填写
        if (empty($wxConfig['app_id']) || empty($wxConfig['app_secret'])) {
            throw new BaseException(['msg' => '请到 [后台-公众号设置] 填写appid 和 appsecret']);
        }
        // 微信登录 (获取session_key)
        $WxUser = new WxUser($wxConfig['app_id'], $wxConfig['app_secret']);
        if (!$session = $WxUser->sessionKey($code)) {
            throw new BaseException(['msg' => $WxUser->getError()]);
        }
        return $session;
    }

    /**
     * 生成用户认证的token
     * @param $openid
     * @return string
     */
    private function token($openid)
    {
        $wxapp_id = self::$wxapp_id;
        // 生成一个不会重复的随机字符串
        $guid = \getGuidV4();
        // 当前时间戳 (精确到毫秒)
        $timeStamp = microtime(true);
        // 自定义一个盐
        $salt = 'token_salt';
        return md5("{$wxapp_id}_{$timeStamp}_{$openid}_{$guid}_{$salt}");
    }

    /**
     * 自动注册用户
     * @param $open_id
     * @param $data
     * @param int $refereeId
     * @return mixed
     * @throws \Exception
     * @throws \think\exception\DbException
     */
    private function register($open_id, $data, $refereeId = null)
    {
        // 查询用户是否已存在
        $user = self::detail(['open_id' => $open_id]);
        $model = $user ?: $this;
        $data['nickName'] = preg_replace('/[\xf0-\xf7].{3}/', '', $data['nickName']);
        $this->startTrans();
        try {
            // 保存/更新用户记录
            if (!$model->allowField(true)->save(array_merge($data, [
                'open_id' => $open_id,
                'wxapp_id' => self::$wxapp_id
            ]))) {
                throw new BaseException(['msg' => '用户注册失败']);
            }
            // 记录推荐人关系
            if (!$user && $refereeId > 0) {
                RefereeModel::createRelation($model['user_id'], $refereeId);
            }
            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            throw new BaseException(['msg' => $e->getMessage()]);
        }
        return $model['user_id'];
    }

    /**
     * 个人中心菜单列表
     * @return array
     */
    public function getMenus()
    {
        $menus = [
            'address' => [
                'name' => '收货地址',
                'url' => 'pages/address/index',
                'icon' => 'map'
            ],
            'coupon' => [
                'name' => '领券中心',
                'url' => 'pages/coupon/coupon',
                'icon' => 'lingquan'
            ],
            'my_coupon' => [
                'name' => '我的优惠券',
                'url' => 'pages/user/coupon/coupon',
                'icon' => 'youhuiquan'
            ],
            'sharing_order' => [
                'name' => '拼团订单',
                'url' => 'pages/sharing/order/index',
                'icon' => 'pintuan'
            ],
            'my_bargain' => [
                'name' => '我的砍价',
                'url' => 'pages/bargain/index/index?tab=1',
                'icon' => 'kanjia'
            ],
            'dealer' => [
                'name' => '分销中心',
                'url' => 'pages/dealer/index/index',
                'icon' => 'fenxiaozhongxin'
            ],
            'help' => [
                'name' => '我的帮助',
                'url' => 'pages/user/help/index',
                'icon' => 'help'
            ],
        ];
        // 判断分销功能是否开启
        if (DealerSettingModel::isOpen()) {
            $menus['dealer']['name'] = DealerSettingModel::getDealerTitle();
        } else {
            unset($menus['dealer']);
        }
        return $menus;
    }

}
