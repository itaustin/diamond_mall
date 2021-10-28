var token = window.localStorage.getItem("token");
$.ajax({
    type: 'GET',
    url: '/?s=/api/cart/lists',
    data : {
        wxapp_id : 10001,
        token : token
    },
    dataType: 'json',
    success: function (data) {
        if(data.code !== -1 || data.code !== 0){
            $(".badge-nav-pre").prepend(`
                <span class="weui-badge " style="position: absolute;top: -.4em;right: 1em;">${data.data.goods_list.length}</span>
            `);
        }
    }
});