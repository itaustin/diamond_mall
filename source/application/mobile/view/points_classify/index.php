<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>积分商城分类</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<link rel="stylesheet" href="/assets/mobile/lib/weui.min.css">
<link rel="stylesheet" href="/assets/mobile/css/jquery-weui.css">
<link rel="stylesheet" href="/assets/mobile/css/style.css">

</head>
<body>
<!--顶部搜索-->
<!--主体-->
<div class="wy-content">
    <div class="category-top">
        <header class='weui-header'>
              <div class="weui-search-bar" id="searchBar">
                <form class="weui-search-bar__form">
                  <div class="weui-search-bar__box">
                    <i class="weui-icon-search"></i>
        <!--                <input disabled type="search" class="weui-search-bar__input" id="searchInput" placeholder="搜索您想要的商品" required>-->
                    <input disabled type="search" class="weui-search-bar__input" id="searchInput" placeholder="搜索您想要的商品" required>
                    <a href="javascript:" class="weui-icon-clear" id="searchClear"></a>
                  </div>
                  <label class="weui-search-bar__label" id="searchText" style="transform-origin: 0px 0px 0px; opacity: 1; transform: scale(1, 1);">
                    <i class="weui-icon-search"></i>
                    <span>搜索商品</span>
                  </label>
                </form>
                <a href="javascript:" class="weui-search-bar__cancel-btn" id="searchCancel">取消</a>
              </div>
        </header>
    </div>
    <div class="menu-left scrollbar-none" id="sidebar">
        <ul class="insertSidebar">

        </ul>
    </div>
</div>
</body>
<script src="/assets/mobile/lib/jquery-2.1.4.js"></script>
<script src="/assets/mobile/lib/fastclick.js"></script>
<script src="/assets/mobile/js/points_category.js"></script>
<script>
  $(function() {
    FastClick.attach(document.body);
  });
</script>
<script src="/assets/mobile/js/jquery-weui.js"></script>
<script type="text/javascript">
	$(function($){
	    $("html").delegate("#sidebar ul li","click",function () {
            $(this).addClass('active').siblings('li').removeClass('active');
            var index = $(this).index();
            $('.j-content').eq(index).show().siblings('.j-content').hide();
        });
	})
</script>
</html>