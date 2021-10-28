<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <meta name="baidu-site-verification" content="ZVPGgtpUfW"/>
    <title>商城</title>
    <link href="http://www.a-ui.cn/favicon.ico" rel="icon" type="image/x-icon">
    <link href="http://www.a-ui.cn/iTunesArtwork@2x.png" sizes="114x114" rel="apple-touch-icon-precomposed">
    <link href="/assets/mobile/css/index/base.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/mobile/css/index/home.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="/assets/mobile/css/swipernewbest.css">
    <script>
        var referee_id = "{{:input('referee_id')}}";
    </script>

</head>
<body>
<section class="aui-flexView">
    <section class="aui-scrollView">
        <div class="swiper-container">
            <div class="swiper-wrapper banner-input">

            </div>
            <!-- 如果需要分页器 -->
            <div class="swiper-pagination"></div>
        </div>
        <div class="divHeight"></div>
        <div class="aui-flex aui-flex-title" style="background:none">
            <div class="aui-flex-box">
                <div id="show">
                </div>
                <h2>1号商城</h2>
            </div>
        </div>
        <div class="aui-recommend onemember">

        </div>
        <div class="" style="width:100%;display: inline-block;text-align:center;"><a
                    href="/?s=/mobile/classify/lists&category_id=10005" style="font-size:12px;text-align:center;">查看更多
                >></a></div>
        <div class="divHeight"></div>
        <!--        <div class="aui-flex aui-flex-title" style="background:none">-->
        <!--            <div class="aui-flex-box">-->
        <!--                <h2>2号商城</h2>-->
        <!--            </div>-->
        <!--        </div>-->
        <!--        <div class="aui-recommend twomember">-->
        <!---->
        <!--        </div>-->
        <!--        <div class="twomorelinks morelinks"><a href="/?s=/mobile/classify/lists&category_id=10005">查看更多 >></a></div>-->
        <div style="height:66px;"></div>
    </section>
    <footer class="aui-footer aui-footer-fixed">
        <a href="javascript:;" class="aui-tabBar-item aui-tabBar-item-active">
            <span class="aui-tabBar-item-icon">
                <i class="icon icon-loan"></i>
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
        <a href="/?s=/mobile/mine/" class="aui-tabBar-item ">
            <span class="aui-tabBar-item-icon">
                <i class="icon icon-info"></i>
            </span>
            <span class="aui-tabBar-item-text">我的</span>
        </a>
    </footer>
</section>
<script type="text/javascript" src="/assets/mobile/lib/jquery-2.1.4.js"></script>
<script src="/assets/mobile/js/jquery-weui.js"></script>
<!--<script src="https://unpkg.com/swiper/js/swiper.js"> </script>-->
<script src="/assets/mobile/js/swipenewbest.min.js"></script>
<script src="/assets/mobile/js/layer.js"></script>
<script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
<script src="/assets/mobile/js/index.js?v=<?php echo rand(0, 999); ?>"></script>
<script src="/assets/mobile/js/getcartnum.js"></script>
</body>
</html>
