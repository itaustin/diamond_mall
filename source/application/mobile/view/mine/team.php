<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>我的团队</title>
    <link rel="stylesheet" href="/assets/mobile/css/app.css">
    <link rel="stylesheet" href="/assets/mobile/css/team.css">
    <link rel="stylesheet" href="/assets/mobile/css/dropload.css">

</head>
<body>
<div class="container">
    <!-- 顶部选项卡 -->
    <div class="swiper-tab dis-flex box-align-center flex-y-center topTabBar">

    </div>
    <!-- 团队总人数 -->
    <div class="widget-people f-28 col-9">有效客户数：<span class="people-num"></span>人 | 客户总人数：<span class="total-num"></span>人</div>
    <!-- 我的团队列表 -->
    <div>
        <div class="widget-list b-f teamBody">

            <!-- 没有更多 -->
            <!--<div class="no-more f-30">亲, 没有更多了</div>-->
        </div>
    <!-- 没有记录 -->
<!--    <div class="zuowey-notcont">-->
<!--        <span class="iconfont icon-wushuju"></span>-->
<!--        <span class="cont">亲，暂无团队记录哦</span>-->
<!--    </div>-->
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
<script src="/assets/mobile/js/mine/team.js?<?php echo rand(1,999); ?>"></script>
</html>