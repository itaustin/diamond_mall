$(function () {
    if(Number(referee_id) !== 0 || Number(referee_id) == null){
        window.localStorage.setItem("referee_id",Number(referee_id));
    }
    $.ajax({
        type : "get",
        dataType : "json",
        url : "/?s=/api/page/index",
        data : {page_id: 0,wxapp_id:10001},
        success:function (data) {
            /**
             * 图像轮播
             */
            var banner = "";
            data.data.items[1].data.forEach(function (item ,key) {
                var link;
                if(item.linkUrl == ""){
                    link = "javascript:void(0);";
                }else{
                    link = item.linkUrl
                }
                banner += `
                    <div class="swiper-slide">
                        <a href="${link}">
                            <img src="${item.imgUrl}" alt="">
                        </a>
                    </div>
                `;
            });
            $(".banner-input").html(banner);
            var mySwiper = new Swiper ('.swiper-container', {
                loop: true, // 循环模式选项
                autoHeight: true, //高度随内容变化
                
                // 如果需要分页器
                pagination: {
                    el: '.swiper-pagination',
                }
            })
            /**
             * 文章快报
             */
            var article = "";
            data.data.items[2]['data'].forEach(function (item, key) {
                article += `
                    <div class="swiper-slide"><a href="/?s=/mobile/article/newsInfo&aid=${item.article_id}">${item.article_title}</a></div>
                `;
            });
            $(".swiper-news-input").html(article);
            // $(".swiper-news").swiper({
            //     loop: true,
            //     direction: 'vertical',
            //     paginationHide :true,
            //     autoplay: 3000
            // });
            /**
             * 1号报单商城
             */
            var onemember = "";
            data.data.items[2].data.forEach(item => {
                onemember += `
                    <div class="aui-hot-list-img" style="width:100%;" onclick='location.href="/?s=/mobile/goods/detail/&goods_id=${item.goods_id}"'>
                        <img src="${item.goods_image}" alt="">
                        <h1>${item.goods_name}</h1>
                        <h2>￥${item.goods_price}<i class="icon icon-car"></i></h2>
                    </div>
                `;
            });
            $(".onemember").html(onemember);
            if(data.data.items[2].data.length <= 0){
                $(".morelinks").css("display","none");
                $(".onealsolikeDisplay").css("display","none");
            }

            /**
             * 2号报单商城
             */
            var twomember = "";
            data.data.items[7].data.forEach(item => {
                twomember += `
                    <div class="aui-hot-list-img" onclick='location.href="/?s=/mobile/goods/detail/&goods_id=${item.goods_id}"'>
                        <img src="${item.goods_image}" alt="">
                        <h1>${item.goods_name}</h1>
                        <h2>￥${item.goods_price}<i class="icon icon-car"></i></h2>
                    </div>
                `;
            });
            $(".twomember").html(twomember);
            if(data.data.items[7].data.length <= 0){
                $(".twomorelinks").css("display","none");
                $(".twoalsolikeDisplay").css("display","none");
            }
        }
    })
});