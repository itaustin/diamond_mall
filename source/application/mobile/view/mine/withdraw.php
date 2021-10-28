<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>提现明细</title>
    <link rel="stylesheet" href="/assets/mobile/css/app.css">
    <link rel="stylesheet" href="/assets/mobile/css/dropload.css">
    <link rel="stylesheet" href="/assets/mobile/css/withdraw.css">

</head>
<body>
<div class="container b-f">
    <!-- 顶部选项卡 -->
    <div class="swiper-tab dis-flex box-align-center flex-y-center top-select-item">
        <block>
            <div style="width:75px;height:40px;" class="swiper-tab-item on" data-id="-1" bindtap="swichNav">全部</div>
        </block>
        <block>
            <div style="width:75px;height:40px;" class="swiper-tab-item" data-id="10" bindtap="swichNav">审核中</div>
        </block>
        <block>
            <div style="width:75px;height:40px;" class="swiper-tab-item" data-id="20" bindtap="swichNav">审核通过</div>
        </block>
        <block>
            <div style="width:75px;height:40px;" class="swiper-tab-item" data-id="40" bindtap="swichNav">提现成功</div>
        </block>
        <block>
            <div style="width:75px;height:40px;" class="swiper-tab-item" data-id="30" bindtap="swichNav">驳回</div>
        </block>
    </div>
    <!-- 提现明细列表 -->
    <div>
        <div class="widget-list" >

        </div>
        <!-- 没有记录 -->
<!--        <div class="zuowey-notcont" style="display:none;">-->
<!--            <span class="iconfont icon-wushuju"></span>-->
<!--            <span class="cont">亲，暂无提现记录哦</span>-->
<!--        </div>-->
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
<script src="/assets/mobile/js/layer.js"></script>
<script src="/assets/mobile/js/mine/withdrawlist.js"></script>
</html>