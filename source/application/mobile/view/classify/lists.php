<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>积分产品列表</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<meta name="description" content="Write an awesome description for your new site here. You can edit this line in _config.yml. It will appear in your document head meta (for Google search results) and in your feed.xml site description.
">
<link rel="stylesheet" href="/assets/mobile/lib/weui.min.css">
<link rel="stylesheet" href="/assets/mobile/css/jquery-weui.css">
<link rel="stylesheet" href="/assets/mobile/css/style.css">
<link rel="stylesheet" href="/assets/mobile/css/dropload.css">

<script>
    var category_id = "{{:input('category_id')}}";
</script>
</head>
<body ontouchstart>
<!--主体-->
<div class="weui-content" style="<!--padding-top:85px;-->">

</div>

<!--<div class="dropload-down"><div class="dropload-noData"></div></div>-->

<script src="/assets/mobile/lib/jquery-2.1.4.js"></script>
<script src="/assets/mobile/lib/fastclick.js"></script>

<script>
  $(function() {
        FastClick.attach(document.body);
  });
</script> 
<script src="/assets/mobile/js/jquery-weui.js"></script>
<!--<script src="/assets/mobile/js/zepto.min.js"></script>-->
<script src="/assets/mobile/js/dropload.min.js"></script>
<script>

    var itemIndex = 0;
    var data = false;

    var counter = 0;
    // 每页展示4个
    var num = 6;
    var pageStart = 0,pageEnd = 0;

    // var category_id = window.localStorage.getItem("category_id");

    // dropload
    var dropload = $('.weui-content').parent().dropload({
        scrollArea : window,
        loadDownFn : function(me) {
            $.ajax({
                type: 'GET',
                url: '/?s=/api/goods/lists',
                data: {
                    page : 1,
                    sortType:"all",
                    sortPrice:0,
                    category_id:category_id,
                    search:null,
                    wxapp_id:10001
                },
                dataType: 'json',
                success: function (data) {
                    if(data.data.list.data.length <= 0){
                        // 数据加载完
                        data = true;
                        // 锁定
                        me.lock();
                        // 无数据
                        me.noData();
                        me.resetload();
                        return false;
                    }
                    var result = '';
                    counter++;
                    pageEnd = num * counter;
                    pageStart = pageEnd - num;

                    var datas = data.data.list.data;

                    if (pageStart <= datas.length) {
                        for (var i = pageStart; i < pageEnd; i++) {
                            result += `
                                <div id="list" data-goods_id="${datas[i].goods_id}" class='goods_list demos-content-padded proListWrap'>
                                    <div class="pro-items">
                                      <a href="/?s=/mobile/goods/detail&goods_id=${datas[i].goods_id}"  class="weui-media-box weui-media-box_appmsg">
                                        <div class="weui-media-box__hd"><img class="weui-media-box__thumb" src="${datas[i].goods_image}" alt=""></div>
                                        <div class="weui-media-box__bd">
                                          <h1 class="weui-media-box__desc">${datas[i].goods_name}</h1>
                                          <div class="wy-pro-pri">¥<em class="num font-15">${datas[i].goods_min_price}</em></div>
                                          <ul class="weui-media-box__info prolist-ul">
                                            <li class="weui-media-box__info__meta"><em class="num">0</em>条评价</li>
                                            <li class="weui-media-box__info__meta"><em class="num">100%</em>好评</li>
                                          </ul>
                                        </div>
                                      </a>
                                    </div>
                                  </div>
                            `;
                            if ((i + 1) >= data.data.list.data.length) {
                                // 数据加载完
                                data = true;
                                // 锁定
                                me.lock();
                                // 无数据
                                me.noData();
                                break;
                            }
                        }
                        $('.weui-content').eq(itemIndex).append(result);
                        // 每次数据加载完，必须重置
                        me.resetload();
                    }
                },
                error: function (xhr, type) {
                    alert('Ajax error!');
                    // 即使加载出错，也得重置
                    me.resetload();
                }
            });
        }
    });
</script>
</body>
</html>
