<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>订单详情</title>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <meta name="description" content="Write an awesome description for your new site here. You can edit this line in _config.yml. It will appear in your document head meta (for Google search results) and in your feed.xml site description.
" />

    <link rel="stylesheet" href="/assets/mobile/lib/weui.min.css" />
    <link rel="stylesheet" href="/assets/mobile/css/jquery-weui.css" />
    <link rel="stylesheet" href="/assets/mobile/css/style.css" />
    <script>
        var order_id = "{{:input('order_id')}}";
    </script>

</head>
<body ontouchstart="">
<!--主体-->
<!--<header class="wy-header">-->
<!--    <div class="wy-header-icon-back">-->
<!--        <span></span>-->
<!--    </div>-->
<!--    <div class="wy-header-title">-->
<!--        订单详情-->
<!--    </div>-->
<!--</header>-->
<div class="weui-content">
    <div class="wy-media-box weui-media-box_text address-select" style="background:url(/assets/mobile/images/top.png);background-size:100%;margin:0;padding:0;width:100%;height:80px;">
        <span style="display:block;margin:0 auto;color:white;text-align:center;margin-top:30px;float:left;margin-left:10px;font-size:14px;" class="state"></span>
    </div>
    <div class="wy-media-box weui-media-box_text address-select address-content">

    </div>

    <div class="wy-media-box weui-media-box_text express_content">

    </div>

    <div class="wy-media-box weui-media-box_text">
        <div class="weui-media-box__bd goods_lists">

        </div>
    </div>
    <div class="weui-panel" style="margin-bottom:3px;">
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
                            <p class="font-14"><span class="mg-r-10">运费</span><span class="fr txt-color-red">￥<em class="express_price">10.00</em></span></p>
                        </div>
                    </div>
                    <div class="weui-cell weui-cell_access" href="javascript:;">
                        <div class="weui-cell__bd weui-cell_primary">
                            <p class="font-14"><span class="mg-r-10">商品金额</span><span class="fr txt-color-red">￥<em class="goods_price"></em></span></p>
                        </div>
                    </div>
                    <a class="weui-cell weui-cell_access" href="money.html">
                        <div class="weui-cell__bd weui-cell_primary">
                            <p class="font-14"><span class="mg-r-10">积分</span></p>
                        </div> <span class="sitem-tip">+<em class="num">0</em></span> </a>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/assets/mobile/lib/jquery-2.1.4.js"></script>
<script src="/assets/mobile/lib/fastclick.js"></script>
<script type="text/javascript" src="/assets/mobile/js/jquery.Spinner.js"></script>
<script>
    $(function() {
        FastClick.attach(document.body);
    });
</script>
<script type="text/javascript">
    $(function(){
        $(".Spinner").Spinner({value:1, len:3, max:999});
    });
</script>
<script src="/assets/mobile/js/jquery-weui.js"></script>
<script src="/assets/mobile/js/layer.js"></script>
<script src="/assets/mobile/js/orderdetail.js"></script>
</body>
</html>