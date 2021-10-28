
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>个人中心</title>
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport" />
    <meta content="yes" name="apple-mobile-web-app-capable" />
    <meta content="black" name="apple-mobile-web-app-status-bar-style" />
    <meta content="telephone=no" name="format-detection" />
    <link href="/assets/mobile/css/mine/style.css" rel="stylesheet" type="text/css" />
    <link href="/assets/mobile/css/index/home.css" rel="stylesheet" type="text/css"/>
    <script>
        var code = "{{:input('code')}}";
    </script>

</head>
<body>
<section class="aui-flexView">
    <section class="aui-scrollView">
        <div class="aui-flex aui-flex-one">
            <div class="aui-welfare-user">
                <img class="headimgurl" src="" alt="">
            </div>
            <div class="aui-flex-box">
                <h2 class="user-name">loading...</h2>
                <!--<p>商城会员</p>-->
            </div>
            <div class="aui-address" onclick="location.href='/?s=/mobile/address/'">
                <i class="icon icon-address"></i>
                <span>地址管理</span>
            </div>
        </div>
        <div class="divHeight"></div>
        <div class="aui-flex">
            <div class="aui-flex-box">
                <h1>我的订单</h1>
            </div>
        </div>
        <div class="aui-palace aui-palace-one">
            <a href="/?s=/mobile/order/order_lists#delivery" class="aui-palace-grid">
                <div class="aui-palace-grid-icon">
                    <img src="/assets/mobile/images/mine/nav-001.png" alt="">
                </div>
                <div class="aui-palace-grid-text">
                    <h2>待发货</h2>
                </div>
            </a>
            <a href="/?s=/mobile/order/order_lists#received" class="aui-palace-grid">
                <div class="aui-palace-grid-icon">
                    <img src="/assets/mobile/images/mine/nav-002.png" alt="">
                </div>
                <div class="aui-palace-grid-text">
                    <h2>待收货</h2>
                </div>
            </a>
            <a href="/?s=/mobile/order/order_lists#payment" class="aui-palace-grid">
                <div class="aui-palace-grid-icon">
                    <img src="/assets/mobile/images/mine/nav-003.png" alt="">
                </div>
                <div class="aui-palace-grid-text">
                    <h2>待付款</h2>
                </div>
            </a>
            <a href="/?s=/mobile/order/order_lists#all" class="aui-palace-grid">
                <div class="aui-palace-grid-icon">
                    <img src="/assets/mobile/images/mine/nav-004.png" alt="">
                </div>
                <div class="aui-palace-grid-text">
                    <h2>全部订单</h2>
                </div>
            </a>
        </div>
        <div class="divHeight"></div>
        <div class="aui-flex">
            <div class="aui-flex-box">
                <h1>商城俱乐部</h1>
            </div>
        </div>
        <div class="aui-image-text">
            <a href="javascript:;" class="aui-flex weui-get-qrcode">
                <div class="aui-flex-box">
                    <h2>获取二维码</h2>
                </div>
            </a>
            <a href="/?s=/mobile/mine/team" class="aui-flex ">
                <div class="aui-flex-box">
                    <h2>我的粉丝（fans）</h2>
                </div>
            </a>
            <a href="/?s=/mobile/mine/dealer" class="aui-flex ">
                <div class="aui-flex-box">
                    <h2>申请代理商</h2>
                </div>
            </a>
            <a href="/?s=/mobile/mine/changeaccount" class="aui-flex ">
                <div class="aui-flex-box">
                    <h2>个人资料</h2>
                </div>
            </a>
            <a href="/?s=/store/passport/login" class="aui-flex ">
                <div class="aui-flex-box">
                    <h2>省市区代理入口</h2>
                </div>
            </a>
<!--            <a href="javascript:getCode();" class="aui-flex ">-->
<!--                <div class="aui-flex-box">-->
<!--                    <h2>获取生活号二维码</h2>-->
<!--                </div>-->
<!--            </a>-->
            <a href="javascript:;" class="aui-flex logout">
                <div class="aui-flex-box">
                    <h2>退出账号</h2>
                </div>
            </a>
        </div>
    </section>
    <footer class="aui-footer aui-footer-fixed" style="margin-top: 300px">
        <a href="/?s=/mobile/" class="aui-tabBar-item">
            <span class="aui-tabBar-item-icon">
                <i class="icon icon-credit"></i>
            </span>
            <span class="aui-tabBar-item-text">首页</span>
        </a>
        <!--<a href="/?s=/mobile/classify/index" class="aui-tabBar-item ">
            <span class="aui-tabBar-item-icon">
                <i class="icon icon-credit"></i>
            </span>
            <span class="aui-tabBar-item-text">分类</span>
        </a>-->
        <a href="/?s=/mobile/flow" class="aui-tabBar-item ">
            <span class="aui-tabBar-item-icon">
                <i class="icon icon-ions"></i>
            </span>
            <span class="aui-tabBar-item-text">购物车</span>
        </a>
        <a href="javascript:;" class="aui-tabBar-item aui-tabBar-item-active">
                <span class="aui-tabBar-item-icon">
                    <i class="icon icon-my"></i>
                </span>
            <span class="aui-tabBar-item-text">我的</span>
        </a>
    </footer>

</section>
</body>
<script src="/assets/mobile/lib/jquery-2.1.4.js"></script>
<script src="/assets/mobile/lib/fastclick.js"></script>
<script>

</script>
<script>
    $(function() {
        FastClick.attach(document.body);
    });
</script>
<script src="/assets/mobile/js/jquery-weui.js"></script>
<script src="/assets/mobile/js/urlmanager.js"></script>
<script src="/assets/mobile/js/bluebird.js"></script>
<script src="/assets/mobile/js/html2canvas.min.js"></script>
<script src="/assets/mobile/js/qrcode.js"></script>
<script src="/assets/mobile/js/layer.js"></script>
<script>
    function getCode() {
        layer.open({
            content: '<img src="http://disinfectant.zmxxzx.com/?s=/mobile/qrcode/view&url=http://p.alipay.com/P/QeaUfMmF" /><br/><p style="text-align:center;">扫码关注芝麻部落</p>'
            ,btn: '关闭'
        });
    }
</script>
<script src="/assets/mobile/js/mine.js?v=<?php echo rand(1,999); ?>"></script>
</html>
