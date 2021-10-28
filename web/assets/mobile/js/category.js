$(function () {
    $.ajax({
        type : "get",
        dataType : "json",
        url : "/?s=/api/category/index&wxapp_id=10001",
        success: function (data) {
            var sidebar = "";
            var itemChild = "";
            data.data.list.forEach(item => {
                sidebar += `
                        <li class="">${item.name}</li>
                        </ul>
                    </div>
                  </aside>
                `;
                if(item.child === undefined){
                    itemChild += `
                        <section class="menu-right padding-all j-content" style="display:none;position:relative;left:0px;bottom:0;">
                            <h5 style="display:block;text-align:center;width:100%;margin-top:3%;">敬请期待~~~</h5>
                        </section>
                    `;
                }else if(item.child.length > 0){
                    itemChild += `
                        <section class="menu-right padding-all j-content"  style="display:none;">
                                <ul>
                    `;
                    item.child.forEach(childItems => {
                        itemChild += `
                                <li class="w-3"  data-category_id="${childItems.category_id}">
                                    <a href="/?s=/mobile/classify/lists&category_id=${childItems.category_id}">
                                    </a>
                                    <img src="${childItems.image.file_path}">
                                    <span>
                                        ${childItems.name}
                                    </span>
                                </li>
                        `;
                    })
                    itemChild += `
                            </ul>
                        </section>
                        `;
                }
            });
            $(".insertSidebar").html(sidebar);
            $("#sidebar").parent().append(itemChild);
            $("#sidebar ul li").first().click();
            // $(".w-3").click(function () {
            //     var category_id = $(this).data("category_id");
            //     window.localStorage.setItem("category_id",category_id);
            //     location.href=$(this).find('a').attr('href');
            // });
        }
    });
});