<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>会员订单</title>
    <link rel="stylesheet" href="/assets/mobile/css/app.css">
    <link rel="stylesheet" href="/assets/mobile/css/dealer_order.css">
    <link rel="stylesheet" href="/assets/mobile/css/dropload.css">

</head>
<body>
    <div class="container">
        <!-- 顶部选项卡 -->
<!--        <div class="swiper-tab dis-flex box-align-center flex-y-center topTabBar">-->
<!--            <block>-->
<!--                <div style="width:125px;height:40px;" data-sell="1" class="swiper-tab-item on selling" >已结算</div>-->
<!--            </block>-->
<!--            <block >-->
<!--                <div style="width:125px;height:40px;" data-sell="0" class="swiper-tab-item" >未结算</div>-->
<!--            </block>-->
<!--            <block>-->
<!--                <div style="width:125px;height:40px;" data-sell="1" class="swiper-tab-item with-me" >其他与我有关订单</div>-->
<!--            </block>-->
<!--        </div>-->
        <!-- 订单列表 -->
        <div style="height: {{swiperHeight}}px;">
            <div class="widget-list b-f orderList">

                <!-- 没有更多 -->
<!--                <div class="no-more f-30">亲, 没有更多了</div>-->
            </div>
            <!-- 没有记录 -->
<!--            <div class="zuowey-notcont" wx:if="{{ !list.data.length && !isLoading }}">-->
<!--                <span class="iconfont icon-wushuju"></span>-->
<!--                <span class="cont">亲，暂无订单记录哦</span>-->
<!--            </div>-->
        </div>
    </div>
</body>
<script src="/assets/mobile/lib/jquery-2.1.4.js"></script>
<script src="/assets/mobile/lib/fastclick.js"></script>
<script type="text/javascript" src="/assets/mobile/js/jquery.Spinner.js"></script>
<script>
    $(function() {
        FastClick.attach(document.body);
    });
</script>
<script src="/assets/mobile/js/jquery-weui.js"></script>
<script src="/assets/mobile/js/dropload.min.js"></script>
<script src="/assets/mobile/js/mine/bonusorder.js?v=<?php echo rand(1,999); ?>"></script>
</html>