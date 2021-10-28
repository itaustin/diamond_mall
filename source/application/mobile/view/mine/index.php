
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0">
    <meta name="renderer" content="webkit"/>
    <meta name="force-rendering" content="webkit"/>
    <title>个人中心</title>
    <link type="text/css" rel="stylesheet" href="/paimai_assets/css/style.css" />
</head>

<body>
<div class="m0myheader">
    <div class="conbox">
        <div class="conboxL">
            <img src="/paimai_assets/images/tx.png" class="tt"/>
            <div class="btR">
                <p class="p1">加载中...</p>
<!--                <div class="v1">-->
<!--                    <img src="/paimai_assets/images/mmm.png" />-->
<!--                    <p>我的亲情账号 ></p>-->
<!--                </div>-->
            </div>
        </div>
<!--        <div class="conboxR">-->
<!--            <a href="shezhi.html">设置</a>-->
<!--        </div>-->
        <div class="clear"></div>
    </div>
</div>
<div class="clear"></div>
<div class="kbox"></div><div class="kbox"></div>
<div class="mypart2">
    <div class="con">
        <div class="pa2_tit">
            <p>我的订单</p>
            <a href="/?s=/mobile/order/order_lists#all">查看更多订单 ></a>
        </div>
        <ul>
            <li>
                <a href="/?s=/mobile/order/order_lists#payment">
                    <div class="ddimg">
                        <img src="/paimai_assets/images/my02.png" />
                    </div>
                    <p>待付款</p>
                </a>
            </li>
            <li>
                <a href="/?s=/mobile/order/order_lists#delivery">
                    <div class="ddimg">
                        <img src="/paimai_assets/images/my03.png" />
                    </div>
                    <p>待发货</p>
                </a>
            </li>
            <li>
                <a href="/?s=/mobile/order/order_lists#received">
                    <div class="ddimg">
                        <img src="/paimai_assets/images/my04.png" />
                    </div>
                    <p>待收货</p>
                </a>
            </li>
            <li>
                <a href="/?s=/mobile/order/order_lists#all">
                    <div class="ddimg">
                        <img src="/paimai_assets/images/my05.png" />
<!--                        <div class="num">2</div>-->
                    </div>
                    <p>全部订单</p>
                </a>
            </li>
            <li>
                <a href="/?s=/mobile/order/order_lists#comment">
                    <div class="ddimg">
                        <img src="/paimai_assets/images/my06.png" />
                    </div>
                    <p>待评价</p>
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="clear"></div>
<div class="kbox"></div><div class="kbox"></div>
<div class="mypart3">
    <ul>
        <li>
            <a href="/?s=/mobile/mine/points">
                <img src="/paimai_assets/images/my9.png" />
                <p>我的积分</p>
            </a>
        </li>
        <li>
            <a href="javascript:void()">
                <img src="/paimai_assets/images/charitable.png" />
                <p>慈善事业</p>
            </a>
        </li>
<!--        <li>-->
<!--            <a href="javascript:void()">-->
<!--                <img src="/paimai_assets/images/my14.png" />-->
<!--                <p>个人资料</p>-->
<!--            </a>-->
<!--        </li>-->
        <li>
            <a href="/?s=/mobile/address/">
                <img src="/paimai_assets/images/my8.png" />
                <p>收货地址</p>
            </a>
        </li>
    </ul>
</div>
<div class="clear"></div>
<div class="fbox"></div>
<div class="footbox">
    <div class="footer">
        <ul>
            <li>
                <a href="/?s=/mobile">
                    <img src="/paimai_assets/images/f01.png" />
                    <p>首页</p>
                </a>
            </li>
            <li>
                <a href="/?s=/mobile/points">
                    <img src="/paimai_assets/images/f02.png" />
                    <p>积分商城</p>
                </a>
            </li>
            <li>
                <a href="/?s=/mobile/flow">
                    <img src="/paimai_assets/images/f03.png" />
                    <p>购物车</p>
                </a>
            </li>
            <li class="on">
                <a href="/?s=/mobile/mine">
                    <img src="/paimai_assets/images/f4.png" />
                    <p>我的</p>
                </a>
            </li>
        </ul>
    </div>
</div>
<script src="/assets/mobile/lib/jquery-2.1.4.js"></script>
<script>
    var token = window.localStorage.getItem("token");
    $.ajax({
        type : "get",
        dataType : "json",
        url : "/?s=/api/user.index/detail",
        data : {
            wxapp_id : 10001,
            token : token
        },
        success: function (data) {
            if(data.data.userInfo !== false){
                var userInfo = data.data.userInfo;
                /**
                 * 用户个人基本信息展示
                 */
                if(userInfo.nickName === "" || userInfo.nickName == null){
                    $(".user-name").text('暂无昵称');
                }else{
                    $(".user-name").text(userInfo.nickName);
                }
                $(".p1").text(userInfo.active_code);
                //头像展示
                $(".headimgurl").attr("src",userInfo.avatarUrl);
                $(".badge-payment").text(data.data.orderCount.payment).text(data.data.orderCount.payment);
                $(".badge-received").text(data.data.orderCount.received);
                // getCartNum(token);
            } else {
                window.location = "/?s=/mobile/passport/login";
            }
        }
    });
</script>
</body>
</html>
