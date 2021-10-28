<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>新闻详情</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<meta name="description" content="Write an awesome description for your new site here. You can edit this line in _config.yml. It will appear in your document head meta (for Google search results) and in your feed.xml site description.">

<link rel="stylesheet" href="/assets/mobile/lib/weui.min.css">
<link rel="stylesheet" href="/assets/mobile/css/jquery-weui.css">
<link rel="stylesheet" href="/assets/mobile/css/style.css">

</head>
<body ontouchstart>
<!--主体-->
<div class="weui-content">
  <article class="weui-article">
    <h1>{{$article.article_title}}</h1>
    <h3 class="wy-news-time">{{$article.create_time}}</h3>
    <section class="wy-news-info">
        {{$article.article_content}}
    </section>
  </article>
  
</div>

<script src="/assets/mobile/lib/jquery-2.1.4.js"></script> 
<script src="/assets/mobile/lib/fastclick.js"></script> 
<script type="text/javascript" src="/assets/mobile/js/jquery.Spinner.js"></script>
<script>
  $(function() {
    FastClick.attach(document.body);
  });
</script>

<script src="/assets/mobile/js/jquery-weui.js"></script>
</body>
</html>
