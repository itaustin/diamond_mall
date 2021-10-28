
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0">
    <meta name="renderer" content="webkit"/>
    <meta name="force-rendering" content="webkit"/>
    <script>/*@cc_on window.location.href="http://support.dmeng.net/upgrade-your-browser.html?referrer="+encodeURIComponent(window.location.href); @*/</script>
    <title>积分商城</title>
    <link type="text/css" rel="stylesheet" href="/paimai_assets/css/style.css" />
    <script src="/assets/mobile/lib/jquery-2.1.4.js"></script>
    <script type="text/javascript">
        var category_id = "{{:input('category_id')}}";
        $(document).ready(function () {
            $(".tolist img").click(function () {
                $(".likebox").toggle();
                $(".shoplist").toggle();
            })

            //筛选
            $('.a_sx').click(function(){
                $("#sxtj").animate({right:"0"},500);
                $('.f_mask').show();
                $("body").css({'height':'100%','overflow':'hidden'});
                //$("body").css{}
            })

            $('.sx_3 a').click(function(){
                $('.f_mask').hide();
                $('#sxtj').animate({right:"-85%"},500);
                $("body").css({'height':'auto','overflow':''});
            })

            $('.f_mask').click(function(){
                $('.f_mask').hide();
                $('#sxtj').animate({right:"-85%"},500);
                $("body").css({'height':'auto','overflow':''});
            })
        })
    </script>
</head>

<body style="background:#ffffff;">
<div class="headerbox">
    <div class="header">
<!--        <div class="headerL">-->
<!--            <a onclick="javascript:history.back(-1)" class="goback"><img src="/paimai_assets/images/goback.png" /></a>-->
<!--        </div>-->
<!--        <div class="headerC0">-->
<!--            <a href="seacher.html" style="display:block; width:100%; height:100%"></a>-->
<!--        </div>-->
        <div class="headerC0">
            <a href="javascript:void()">积分商城</a>
        </div>
    </div>
</div>
<div class="clear"></div>
<!--<div class="shopType">-->
<!--    <ul>-->
<!--        <li class="on">-->
<!--            <a href="shoplist.html">综合</a>-->
<!--        </li>-->
<!--        <li>-->
<!--            <a href="shoplist.html">销量</a>-->
<!--        </li>-->
<!--        <li>-->
<!--            <a href="shoplist.html">价格</a>-->
<!--            <span class="pricebtn1"></span>-->
<!--            <span class="pricebtn2"></span>-->
<!--        </li>-->
<!--        <li>-->
<!--            <a href="dplist.html">店铺</a>-->
<!--        </li>-->
<!--        <li>-->
<!--            <a href="javascript:void()" class="a_sx">筛选</a>-->
<!--        </li>-->
<!--    </ul>-->
<!--</div>-->
<!--<div class="hbox1"></div>-->
<!--<div class="tolist"><img src="/paimai_assets/images/tolist.png" /></div>-->
<!--<div class="totop"><a href="javascript:scrollTo(0,0)"><img src="/paimai_assets/images/totop.png" /></a></div>-->
<!--<div class="kbox"></div>-->
<div class="likebox" style="margin-top:50px">
    <ul>

    </ul>
</div>
<div class="shoplist" style="display:none">
    <ul>
        <li>
            <a href="xq.html">
                <div class="listL"><img src="/paimai_assets/images/dp3.png" /></div>
                <div class="listR">
                    <div class="v1">高贵塔夫绸抹胸长拖尾婚纱高贵塔夫绸抹胸长拖尾婚纱</div>
                    <div class="v2"><span>包邮</span></div>
                    <div class="v3">
                        <p class="p1">￥899<span>￥1600</span></p>
                        <p class="p2">364人付款</p>
                    </div>
                </div>
            </a>
        </li>
        <li>
            <a href="xq.html">
                <div class="listL"><img src="/paimai_assets/images/dp4.png" /></div>
                <div class="listR">
                    <div class="v1">高贵塔夫绸抹胸长拖尾婚纱高贵塔夫绸抹胸长拖尾婚纱</div>
                    <div class="v2"><span>包邮</span></div>
                    <div class="v3">
                        <p class="p1">￥899<span>￥1600</span></p>
                        <p class="p2">364人付款</p>
                    </div>
                </div>
            </a>
        </li>
        <li>
            <a href="xq.html">
                <div class="listL"><img src="/paimai_assets/images/dp5.png" /></div>
                <div class="listR">
                    <div class="v1">高贵塔夫绸抹胸长拖尾婚纱高贵塔夫绸抹胸长拖尾婚纱</div>
                    <div class="v2"><span>包邮</span></div>
                    <div class="v3">
                        <p class="p1">￥899<span>￥1600</span></p>
                        <p class="p2">364人付款</p>
                    </div>
                </div>
            </a>
        </li>
        <li>
            <a href="xq.html">
                <div class="listL"><img src="/paimai_assets/images/dp6.png" /></div>
                <div class="listR">
                    <div class="v1">高贵塔夫绸抹胸长拖尾婚纱高贵塔夫绸抹胸长拖尾婚纱</div>
                    <div class="v2"><span>包邮</span></div>
                    <div class="v3">
                        <p class="p1">￥899<span>￥1600</span></p>
                        <p class="p2">364人付款</p>
                    </div>
                </div>
            </a>
        </li>
        <li>
            <a href="xq.html">
                <div class="listL"><img src="/paimai_assets/images/dp7.png" /></div>
                <div class="listR">
                    <div class="v1">高贵塔夫绸抹胸长拖尾婚纱高贵塔夫绸抹胸长拖尾婚纱</div>
                    <div class="v2"><span>包邮</span></div>
                    <div class="v3">
                        <p class="p1">￥899<span>￥1600</span></p>
                        <p class="p2">364人付款</p>
                    </div>
                </div>
            </a>
        </li>
        <li>
            <a href="xq.html">
                <div class="listL"><img src="/paimai_assets/images/dp8.png" /></div>
                <div class="listR">
                    <div class="v1">高贵塔夫绸抹胸长拖尾婚纱高贵塔夫绸抹胸长拖尾婚纱</div>
                    <div class="v2"><span>包邮</span></div>
                    <div class="v3">
                        <p class="p1">￥899<span>￥1600</span></p>
                        <p class="p2">364人付款</p>
                    </div>
                </div>
            </a>
        </li>
        <li>
            <a href="xq.html">
                <div class="listL"><img src="/paimai_assets/images/dp3.png" /></div>
                <div class="listR">
                    <div class="v1">高贵塔夫绸抹胸长拖尾婚纱高贵塔夫绸抹胸长拖尾婚纱</div>
                    <div class="v2"><span>包邮</span></div>
                    <div class="v3">
                        <p class="p1">￥899<span>￥1600</span></p>
                        <p class="p2">364人付款</p>
                    </div>
                </div>
            </a>
        </li>
        <li>
            <a href="xq.html">
                <div class="listL"><img src="/paimai_assets/images/dp4.png" /></div>
                <div class="listR">
                    <div class="v1">高贵塔夫绸抹胸长拖尾婚纱高贵塔夫绸抹胸长拖尾婚纱</div>
                    <div class="v2"><span>包邮</span></div>
                    <div class="v3">
                        <p class="p1">￥899<span>￥1600</span></p>
                        <p class="p2">364人付款</p>
                    </div>
                </div>
            </a>
        </li>
    </ul>
</div>
<!--<div class="sxbox">-->
<!--    <div class="sxbox0">-->
<!--        <div class="f_mask"></div>-->
<!--        <div id="sxtj">-->
<!--            <div class="sx_1">筛选</div>-->
<!--            <div class="sx_2">-->
<!--                <p class="tit">品牌</p>-->
<!--                <a href="javascript:void()">名斓</a>-->
<!--                <a href="javascript:void()">奥蒂莉亚</a>-->
<!--                <a href="javascript:void()">GCU</a>-->
<!--                <a href="javascript:void()">漫香农</a>-->
<!--                <a href="javascript:void()">蒂满庭</a>-->
<!--                <a href="javascript:void()">DearWhite</a>-->
<!--            </div>-->
<!--            <div class="sx_2">-->
<!--                <p class="tit">类别</p>-->
<!--                <a href="javascript:void()">婚纱</a>-->
<!--            </div>-->
<!--            <div class="sx_2">-->
<!--                <p class="tit">折扣和服务</p>-->
<!--                <a href="javascript:void()">包邮</a>-->
<!--                <a href="javascript:void()">赠送运费险</a>-->
<!--                <a href="javascript:void()">消费者保障</a>-->
<!--                <a href="javascript:void()">全球购</a>-->
<!--                <a href="javascript:void()">7天内退货</a>-->
<!--                <a href="javascript:void()">通用排序</a>-->
<!--            </div>-->
<!--            <div class="sx_2">-->
<!--                <p class="tit">价格区间</p>-->
<!--                <div class="pric">-->
<!--                    <input name="" type="text" /><label>-</label><input name="" type="text" />-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="sx_3">-->
<!--                <a href="javascript:void()">确定</a>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
<!--<div class="footbox">-->
<!--    <div class="footer">-->
<!--        <ul>-->
<!--            <li>-->
<!--                <a href="/?s=/mobile">-->
<!--                    <img src="/paimai_assets/images/f01.png" />-->
<!--                    <p>首页</p>-->
<!--                </a>-->
<!--            </li>-->
<!--            <li class="on">-->
<!--                <a href="/?s=/mobile/points">-->
<!--                    <img src="/paimai_assets/images/f2.png" />-->
<!--                    <p>积分商城</p>-->
<!--                </a>-->
<!--            </li>-->
<!--            <li>-->
<!--                <a href="/?s=/mobile/flow">-->
<!--                    <img src="/paimai_assets/images/f03.png" />-->
<!--                    <p>购物车</p>-->
<!--                </a>-->
<!--            </li>-->
<!--            <li>-->
<!--                <a href="/?s=/mobile/mine">-->
<!--                    <img src="/paimai_assets/images/f04.png" />-->
<!--                    <p>我的</p>-->
<!--                </a>-->
<!--            </li>-->
<!--        </ul>-->
<!--    </div>-->
<!--</div>-->
<script>
    $(function () {
        $.ajax({
            type : "post",
            dataType : "json",
            url : "/?s=/api/goods/getPointsGoods",
            data : {
                wxapp_id : 10001,
                points_category_id : category_id
            },
            success: function (data) {
                console.log(data);
                if(data.data.data.length > 0){
                    var html = '';
                    data.data.data.forEach(function (item , key) {
                        console.log(item);
                        html += `
                            <li>
                                <a href="/?s=/mobile/goods/detail/&goods_id=${item.goods_id}">
                                    <img src="${item.goods_image}" class="proimg"/>
                                    <p class="tit">${item.goods_name}</p>
                                    <p class="price">积分：${item.points}<img src="/paimai_assets/images/f3.png" /></p>
                                </a>
                            </li>
                        `
                    });
                    $(".likebox ul").html(html);
                }
            }
        });
    });
</script>
</body>
</html>
