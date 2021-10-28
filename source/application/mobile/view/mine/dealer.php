<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="stylesheet" href="/assets/mobile/css/app.css">
    <link rel="stylesheet" href="/assets/mobile/css/dealer.css">
    <title>会员中心</title>

</head>
<body style="display:none;">
    <div class="container b-f">
        <!-- 头部背景图 -->
        <div class="dealer-bg">
            <img mode="widthFix" style="width:100%;" src="/assets/mobile/images/dealer-bg.png">
        </div>
        <!-- widget -->
        <div class="widget-body b-f dis-flex flex-dir-column flex-y-center">
            <!-- 用户信息 -->
            <div class="widget widget__base m-top20 b-f dis-flex flex-dir-column">
                <div class="base__user f-30">
                    <!-- 用户头像 -->
                    <div class="user-avatar">
                        <img style="border-radius:150px;width:86px;height:86px;" class="avatarUrl" src="">
                    </div>
                    <div class="user-nickName f-32"></div>
                    <div class="user-referee f-24 col-9"></div>
                </div>
                <div class="base__capital dis-flex flex-dir-column">
                    <!-- 佣金卡片 -->
                    <div class="capital-card dis-flex">
                        <div class="card-left flex-box dis-flex flex-dir-column flex-x-around">
                            <div class="f-28 col-f">
                                <span space="ensp" class="withdraw_money"></span>
                            </div>
                            <div class="f-28 col-f" style="display:none;">
                                <span space="ensp" class="freeze_money">待提现 0.00 元</span>
                            </div>
                        </div>
                        <div class="card-right flex-box dis-flex flex-x-end flex-y-center">
                            <div onclick="location.href='/?s=/mobile/mine/apply'" class="withdraw-btn f-26">去提现</div>
                        </div>
                    </div>
                    <!-- 已提现金额 -->
                    <div class="capital-already clear">
                        <div class="already-left f-28 fl">已提现金额</div>
                        <div class="already-right f-28 fr total_money"></div>
                    </div>
                </div>
            </div>

            <!-- 操作列表 -->
            <div class="widget widget__operat clear b-f">
                <div class="operat__item">
                    <navigator onclick="location.href='/?s=/mobile/mine/withdraw'">
                        <div class="item__icon">
                            <span class="iconfont icon-zhangben" style="color:#F9BA21;"></span>
                        </div>
                        <div class="item__text f-28">提现明细</div>
                    </navigator>
                </div>
                <div class="operat__item">
                    <navigator onclick="location.href='/?s=/mobile/mine/order'">
                        <div class="item__icon">
                            <span class="iconfont icon-dingdan" style="color:#FF7575;"></span>
                        </div>
                        <div class="item__text f-28">收入明细</div>
                    </navigator>
                </div>
                <div class="operat__item" onclick="location.href='/?s=/mobile/mine/team'">
                    <navigator>
                        <div class="item__icon">
                            <span class="iconfont icon-tuandui" style="color:#59C78E;"></span>
                        </div>
                        <div class="item__text f-28">我的团队</div>
                    </navigator>
                </div>
            </div>
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
<script src="/assets/mobile/js/dealer.js?v=<?php echo rand(1,999); ?>"></script>
</html>