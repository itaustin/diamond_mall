<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>订单详情</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="description" content="">

    <link rel="stylesheet" href="/assets/mobile/lib/weui.min.css">
    <link rel="stylesheet" href="/assets/mobile/css/jquery-weui.css">
    <link rel="stylesheet" href="/assets/mobile/css/style.css">
    <script>
        var pay_type = "{{$type}}";
    </script>

</head>
<body ontouchstart>
<!--主体-->
<header class="wy-header">
    <div class="wy-header-icon-back" onclick="history.back()"><span></span></div>
    <div class="wy-header-title">订单详情</div>
</header>
<div class="weui-content">
    <div class="wy-media-box weui-media-box_text address-select">

    </div>
    <div class="wy-media-box weui-media-box_text">
        <div class="weui-media-box__bd">
            <div class="weui-media-box_appmsg ord-pro-list">
                <div class="weui-media-box__hd"><a href="pro_info.html"><img class="weui-media-box__thumb" src="" alt=""></a></div>
                <div class="weui-media-box__bd">
                    <h1 class="weui-media-box__desc"><a href="javascript:void(0);" class="ord-pro-link goods_name"></a></h1>
                    <p class="weui-media-box__desc specData">规格：<span></span></p>
                    <div class="clear mg-t-10">
                        <div class="wy-pro-pri fl shop_total_pay_price"></div>
                        <div class="pro-amount fr num font-12">数量x<em class="total_num">...</em></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="weui-panel">
        <div class="weui-panel__bd">
            <div class="weui-media-box weui-media-box_small-appmsg">
                <div class="weui-cells">
                    <div class="weui-cell weui-cell_access">
                        <div class="weui-cell__bd weui-cell_primary">
                            <p class="font-14"><span class="mg-r-10">配送方式</span><span class="fr">快递</span></p>
                        </div>
                    </div>
                    <div class="weui-cell weui-cell_access" href="javascript:;">
                        <div class="weui-cell__bd weui-cell_primary">
                            <p class="font-14"><span class="mg-r-10">运费</span><span class="fr txt-color-red express_price">￥<em class="num">0.00</em></span></p>
                        </div>
                    </div>
                    <!--          <a class="weui-cell weui-cell_access" href="money.html">-->
                    <!--            <div class="weui-cell__bd weui-cell_primary">-->
                    <!--              <p class="font-14"><span class="mg-r-10">可用积分</span><span class="sitem-tip"><em class="num">1235</em>个</span></p>-->
                    <!--            </div>-->
                    <!--            <span class="weui-cell__ft"></span>-->
                    <!--          </a>-->
                    <!--          <a class="weui-cell weui-cell_access" href="coupon.html">-->
                    <!--            <div class="weui-cell__bd weui-cell_primary">-->
                    <!--              <p class="font-14"><span class="mg-r-10">优惠券</span><span class="sitem-tip"><em class="num">0</em>张可用</span></p>-->
                    <!--            </div>-->
                    <!--            <span class="weui-cell__ft"></span>-->
                    <!--          </a>-->
                </div>
            </div>
        </div>
    </div>
    <div class="wy-media-box weui-media-box_text pay_types">
        <div class="mg10-0 t-c">总金额：<span class="wy-pro-pri mg-tb-5 total_pay_price"></span></div>
        {{if condition='$type eq "wechat"'}}
        <div class="mg10-0"><a href="javascript:void(0);" class="weui-btn weui-btn_primary wechat_pay_now">微信支付</a>
        </div>
        {{else /}}
        <div class="mg10-0"><a href="javascript:void(0);" style="background:#1F99E1;" class="weui-btn weui-btn_primary alipay_pay_now">支付宝支付</a></div>
        {{/if}}
    </div>
</div>
<script src="/assets/mobile/lib/jquery-2.1.4.js"></script>
<script src="/assets/mobile/lib/fastclick.js"></script>
<script type="text/javascript" src="/assets/mobile/js/jquery.Spinner.js"></script>
<script>
    $(function () {
        FastClick.attach(document.body);
    });
</script>
<script type="text/javascript">
    $(function () {
        // $(".Spinner").Spinner({value:1, len:3, max:999});
    });
</script>
<script src="/assets/mobile/js/jquery-weui.js"></script>
<script src="https://res2.wx.qq.com/open/js/jweixin-1.4.0.js"></script>
<script src="/assets/mobile/js/layer.js"></script>
<script src="/assets/mobile/js/checkout.js?v=<?php echo rand(1, 999); ?>"></script>
</body>
</html>
