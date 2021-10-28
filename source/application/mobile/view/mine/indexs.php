<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>会员中心</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<meta name="description" content="Write an awesome description for your new site here. You can edit this line in _config.yml. It will appear in your document head meta (for Google search results) and in your feed.xml site description.
">

<link rel="stylesheet" href="/assets/mobile/lib/weui.min.css">
<link rel="stylesheet" href="/assets/mobile/css/jquery-weui.css">
<link rel="stylesheet" href="/assets/mobile/css/style.css">
<script>
    var code = "{{:input('code')}}";
</script>

</head>
<body ontouchstart>
<!--主体-->
<div class='weui-content'>
  <div class="wy-center-top">
    <div class="weui-media-box weui-media-box_appmsg">
      <div class="weui-media-box__hd"><img class="weui-media-box__thumb radius headimgurl" src="/assets/mobile/upload/headimg.jpg" alt=""></div>
      <div class="weui-media-box__bd">
        <h4 class="weui-media-box__title user-name">立即登录</h4>
<!--        <p class="user-grade">等级：普通会员</p>-->
<!--        <p class="user-integral">待返还金额：<em class="num">500.0</em>元</p>-->
      </div>
    </div>
<!--    <div class="xx-menu weui-flex">-->
<!--      <div class="weui-flex__item"><div class="xx-menu-list"><em>987</em><p>账户余额</p></div></div>-->
<!--      <div class="weui-flex__item"><div class="xx-menu-list"><em>459</em><p>我的积分</p></div></div>-->
<!--      <div class="weui-flex__item"><div class="xx-menu-list"><em>4</em><p>收藏商品</p></div></div>-->
<!--    </div>-->
  </div>
  <div class="weui-panel weui-panel_access">
    <div class="weui-panel__hd">
      <a href="/?s=/mobile/order/order_lists" class="weui-cell weui-cell_access center-alloder">
        <div class="weui-cell__bd wy-cell">
          <div class="weui-cell__hd"><img src="/assets/mobile/images/center-icon-order-all.png" alt="" class="center-list-icon"></div>
          <div class="weui-cell__bd weui-cell_primary"><p class="center-list-txt">全部订单</p></div>
        </div>
        <span class="weui-cell__ft"></span>
      </a>   
    </div>
    <div class="weui-panel__bd">
      <div class="weui-flex">
          <div class="weui-flex__item">
              <a href="/?s=/mobile/order/order_lists#all" class="center-ordersModule">
<!--                  <span class="weui-badge badge-delivery" style="position: absolute;top:5px;right:10px; font-size:10px;"></span>-->
                  <div class="imgicon"><img src="/assets/mobile/images/center-icon-order-all.png" /></div>
                  <div class="name">全部订单</div>
              </a>
          </div>
        <div class="weui-flex__item">
          <a href="/?s=/mobile/order/order_lists#payment" class="center-ordersModule">
            <span class="weui-badge badge-payment" style="position: absolute;top:5px;right:10px; font-size:10px;">0</span>
            <div class="imgicon"><img src="/assets/mobile/images/center-icon-order-dfk.png" /></div>
            <div class="name">待付款</div>
          </a>
        </div>
        <div class="weui-flex__item">
          <a href="/?s=/mobile/order/order_lists#received" class="center-ordersModule">
              <span class="weui-badge badge-received" style="position: absolute;top:5px;right:10px; font-size:10px;">0</span>
            <div class="imgicon"><img src="/assets/mobile/images/center-icon-order-dsh.png" /></div>
            <div class="name">待收货</div>
          </a>
        </div>
      </div>
    </div>
  </div>
  
<div class="weui-panel">
    <div class="weui-panel__bd">
      <div class="weui-media-box weui-media-box_small-appmsg">
        <div class="weui-cells">
          <a class="weui-cell weui-cell_access" href="/?s=/mobile/address/">
            <div class="weui-cell__hd"><img src="/assets/mobile/images/center-icon-dz.png" alt="" class="center-list-icon"></div>
            <div class="weui-cell__bd weui-cell_primary">
              <p class="center-list-txt">地址管理</p>
            </div>
            <span class="weui-cell__ft"></span>
          </a>
          <a class="weui-cell weui-cell_access weui-get-qrcode" href="javascript:void(0);">
            <div class="weui-cell__hd"><img src="/assets/mobile/images/center-icon-yhk.png" alt="" class="center-list-icon"></div>
            <div class="weui-cell__bd weui-cell_primary">
              <p class="center-list-txt">获取二维码</p>
            </div>
            <span class="weui-cell__ft"></span>
          </a>
            <a class="weui-cell weui-cell_access" href="/?s=/mobile/mine/team">
                <div class="weui-cell__hd"><img src="/assets/mobile/images/center-icon-jyjl.png" alt="" class="center-list-icon"></div>
                <div class="weui-cell__bd weui-cell_primary">
                    <p class="center-list-txt">我的粉丝（fans）</p>
                </div>
                <span class="weui-cell__ft"></span>
            </a>
          <a class="weui-cell weui-cell_access" href="/?s=/mobile/mine/dealer">
            <div class="weui-cell__hd"><img src="/assets/mobile/images/center-icon-dlmm.png" alt="" class="center-list-icon"></div>
            <div class="weui-cell__bd weui-cell_primary">
              <p class="center-list-txt">申请代理商</p>
            </div>
            <span class="weui-cell__ft"></span>
          </a>
          <a class="weui-cell weui-cell_access logout" href="login.html">
            <div class="weui-cell__hd"><img src="/assets/mobile/images/center-icon-out.png" alt="" class="center-list-icon"></div>
            <div class="weui-cell__bd weui-cell_primary">
              <p class="center-list-txt">退出账号</p>
            </div>
            <span class="weui-cell__ft"></span>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<!--底部导航-->
<div class="foot-black"></div>
<div class="weui-tabbar wy-foot-menu">
    <a href="/?s=/mobile/" class="weui-tabbar__item">
        <div class="weui-tabbar__icon foot-menu-home"></div>
        <p class="weui-tabbar__label">首页</p>
    </a>
    <a href="/?s=/mobile/classify" class="weui-tabbar__item">
    <div class="weui-tabbar__icon foot-menu-list"></div>
    <p class="weui-tabbar__label">分类</p>
  </a>
    <a href="/?s=/mobile/flow" class="weui-tabbar__item">
        <div class="weui-tabbar__icon foot-menu-cart badge-nav-pre"></div>
        <p class="weui-tabbar__label">购物车</p>
    </a>
    <a href="/?s=/mobile/mine" class="weui-tabbar__item weui-bar__item--on">
        <div class="weui-tabbar__icon foot-menu-member"></div>
        <p class="weui-tabbar__label">我的</p>
    </a>
</div>

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
<script src="/assets/mobile/js/mine.js?v=202003130001"></script>
<script src="/assets/mobile/js/layer.js"></script>
</body>
</html>
