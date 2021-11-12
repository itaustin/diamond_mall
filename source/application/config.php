<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    // +----------------------------------------------------------------------
    // | 应用设置
    // +----------------------------------------------------------------------

    // 应用调试模式
    'app_debug' => true,
    // 应用Trace
    'app_trace' => false,
    // 应用模式状态
    'app_status' => '',
    // 是否支持多模块
    'app_multi_module' => true,
    // 入口自动绑定模块
    'auto_bind_module' => false,
    // 注册的根命名空间
    'root_namespace' => [],
    // 扩展函数文件
    'extra_file_list' => [THINK_PATH . 'helper' . EXT],
    // 默认输出类型
    'default_return_type' => 'json',
    // 默认AJAX 数据返回格式,可选json xml ...
    'default_ajax_return' => 'json',
    // 默认JSONP格式返回的处理方法
    'default_jsonp_handler' => 'jsonpReturn',
    // 默认JSONP处理方法
    'var_jsonp_handler' => 'callback',
    // 默认时区
    'default_timezone' => 'PRC',
    // 是否开启多语言
    'lang_switch_on' => false,
    // 默认全局过滤方法 用逗号分隔多个
    'default_filter' => 'htmlspecialchars',
    // 默认语言
    'default_lang' => 'zh-cn',
    // 应用类库后缀
    'class_suffix' => false,
    // 控制器类后缀
    'controller_suffix' => false,

    // +----------------------------------------------------------------------
    // | 模块设置
    // +----------------------------------------------------------------------

    // 默认模块名
    'default_module' => 'store',
    // 禁止访问模块
    'deny_module_list' => ['common'],
    // 默认控制器名
    'default_controller' => 'Index',
    // 默认操作名
    'default_action' => 'index',
    // 默认验证器
    'default_validate' => '',
    // 默认的空控制器名
    'empty_controller' => 'Error',
    // 操作方法后缀
    'action_suffix' => '',
    // 自动搜索控制器
    'controller_auto_search' => false,

    // +----------------------------------------------------------------------
    // | 手机验证码
    // +----------------------------------------------------------------------
    'huawei_sms' => [
        'url'   =>  'https://rtcsms.cn-north-1.myhuaweicloud.com:10743',
        'APP_KEY' =>    '6OHkIhoD65Q2eFDUlJT7Rv7T7QUv',
        'APP_SECRET'    =>  '8j8ku022LuL96Fq4078uZ4pQ3Lpy',
        'sender'    =>  '8820051832318',
        'TEMPLATE_ID'   =>  '2cf34c6baf454266a786e8ec5870919c',
        'signature' =>  '芝麻信息',
        'sms_interface'    =>  [
            'sms_batchSendSms'  =>  '/sms/batchSendSms/v1',
            'sms_batchSendDiffSms'  =>  '/sms/batchSendDiffSms/v1'
        ],
        'callback'  =>  'http://www.zmxxzx.com/?s=/mobile/sms/notify'
    ],

    // +----------------------------------------------------------------------
    // | URL设置
    // +----------------------------------------------------------------------

    // PATHINFO变量名 用于兼容模式
    'var_pathinfo' => 's',
    // 兼容PATH_INFO获取
    'pathinfo_fetch' => ['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL'],
    // pathinfo分隔符
    'pathinfo_depr' => '/',
    // URL伪静态后缀
    'url_html_suffix' => '',
    // URL普通方式参数 用于自动生成
    'url_common_param' => false,
    // URL参数方式 0 按名称成对解析 1 按顺序解析
    'url_param_type' => 0,
    // 是否开启路由
    'url_route_on' => true,
    // 路由使用完整匹配
    'route_complete_match' => false,
    // 路由配置文件（支持配置多个）
    'route_config_file' => ['route'],
    // 是否强制使用路由
    'url_route_must' => false,
    // 域名部署
    'url_domain_deploy' => false,
    // 域名根，如thinkphp.cn
    'url_domain_root' => '',
    // 是否自动转换URL中的控制器和操作名
    'url_convert' => true,
    // 默认的访问控制器层
    'url_controller_layer' => 'controller',
    // 表单请求类型伪装变量
    'var_method' => '_method',
    // 表单ajax伪装变量
    'var_ajax' => '_ajax',
    // 表单pjax伪装变量
    'var_pjax' => '_pjax',
    // 是否开启请求缓存 true自动缓存 支持设置请求缓存规则
    'request_cache' => false,
    // 请求缓存有效期
    'request_cache_expire' => null,
    // 全局请求缓存排除规则
    'request_cache_except' => [],

    // +----------------------------------------------------------------------
    // | 模板设置
    // +----------------------------------------------------------------------

    'template' => [
        // 模板引擎类型 支持 php think 支持扩展
        'type' => 'Think',
        // 模板路径
        'view_path' => ROOT_PATH.DS.'../templates/',
        // 模板后缀
        'view_suffix' => 'html',
        // 模板文件名分隔符
        'view_depr' => DS,
        // 模板引擎普通标签开始标记
        'tpl_begin' => '{',
        // 模板引擎普通标签结束标记
        'tpl_end' => '}',
        // 标签库标签开始标记
        'taglib_begin' => '{',
        // 标签库标签结束标记
        'taglib_end' => '}',
    ],

    // 视图输出字符串内容替换
    'view_replace_str' => [],
    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl' => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',
    'dispatch_error_tmpl' => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',

    // +----------------------------------------------------------------------
    // | 异常及错误设置
    // +----------------------------------------------------------------------

    // 异常页面的模板文件
    'exception_tmpl' => THINK_PATH . 'tpl' . DS . 'think_exception.tpl',

    // 错误显示信息,非调试模式有效
    'error_message' => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg' => false,
    // 异常处理handle类 留空使用 \think\exception\Handle
    'exception_handle' => '\\app\\common\\exception\\ExceptionHandler',

    // +----------------------------------------------------------------------
    // | 日志设置
    // +----------------------------------------------------------------------

    'log' => [
        // 日志记录方式，内置 file socket 支持扩展
        'type' => 'File',
        // 日志保存目录
        'path' => LOG_PATH,
        // 日志记录级别
        'level' => [],
        // error和sql日志单独记录
        'apart_level' => ['begin', 'error', 'sql', 'zuowey-info'],
    ],

    // +----------------------------------------------------------------------
    // | Trace设置 开启 app_trace 后 有效
    // +----------------------------------------------------------------------
    'trace' => [
        // 内置Html Console 支持扩展
        'type' => 'Html',
    ],

    // +----------------------------------------------------------------------
    // | 缓存设置
    // +----------------------------------------------------------------------

//    'cache' => [
//        // 驱动方式
//        'type' => 'File',
//        // 缓存保存目录
//        'path' => CACHE_PATH,
//        // 缓存前缀
//        'prefix' => 'zuowey',
//        // 缓存有效期 0表示永久缓存
//        'expire' => 0,
//    ],

    'cache' => [
        // 驱动方式
        'type' => 'redis',
        'host' => '127.0.0.1',
        'select' => 3,
        'port' => '56379',
        'password'       => 'itaustin@gmail.c',
        // 缓存保存目录
        'path' => CACHE_PATH,
        // 缓存前缀
        'prefix' => 'diamond_cache_',
        // 缓存有效期 0表示永久缓存
        'expire' => 0,
    ],

    // +----------------------------------------------------------------------
    // | 会话设置
    // +----------------------------------------------------------------------

    'session' => [
//        'id'             => '',
        'select'         => '2',
        // SESSION_ID的提交变量,解决flash上传跨域
        'var_session_id' => '',
        // SESSION 前缀
        'prefix'         => 'diamond_session',
        // 驱动方式 支持redis memcache memcached
        'type'           => 'redis',
        'host'           => '127.0.0.1',
        'port'           => '56379',
        'password'       => 'itaustin@gmail.c',
        // 是否自动开启 SESSION
        'auto_start'     => true,
    ],

    // +----------------------------------------------------------------------
    // | Cookie设置
    // +----------------------------------------------------------------------
    'cookie'                 => [
        // cookie 名称前缀
        'prefix'    => 'diamond',
        // cookie 保存时间
        'expire'    => 0,
        // cookie 保存路径
        'path'      => '/',
        // cookie 有效域名
        //  cookie 启用安全传输
        'secure'    => false,
        // httponly设置
        'httponly'  => '',
        // 是否使用 setcookie
        'setcookie' => true,
    ],

    //分页配置
    'paginate' => [
        'type' => 'bootstrap',
        'var_page' => 'page',
        'list_rows' => 15,
    ],

    'alipay'                 => [
        'appId' => '2021002135654747',
        'gatewayUrl' => 'https://openapi.alipay.com/gateway.do',
//        'alipayRsaPublicKey' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAnUU/463QbHQPeNSp2EJjKI1SYAkV21jzFUwLMISw7oUIdYJrnCMS9whwIgSobkjK+iWYgi2Gxh/XecMOtUyjJAx1rKylXcHocBOVJMlLEH01Mikwq7F4RL5HshFGpr6MYbWolcogiY256e3e9dBZXJ7rZRjhDi9xOC99cWcJ+9HrIB9UeIVjMYKhtv5ZZQx+IRjWYCikr2G5i8WnuafJ3H9DDVX+UyUo43JsukiZXNXboVVLrX4OunZMoYSJtM+drjVthYi8v/Mnt6uKWrsUtBq3Ec4JEPJc1G3WRAZVuuAvcxzkrh4eRcQ2mHiz7MBP5gSJCVeUUjk66ElKHqCRgwIDAQAB',
        'alipayRsaPublicKey' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA5ROoNZWefUR4c/HGiyFIErA8DRcX7JDEkDR16SJm1quPF8guYdhNZi1cuw2YwztFc5RA31EjT2U1LSYN1XoP1oBqKLxEu104QoUcwU7XOhfast0ToMLGlxENjmbjUEhleTGSqbYbYa0Ytlb8cVqURS/uYLDYNT4OpH8I8XfvwTDRtU5x6ilacfAMkD8vmhEno6BAGnU7+i0XN53ir32T6pad8eg1A88fbjJ4EA1JjUGCFx11WLUGX8ekTl2mmX9veXGE0tyxbYv9Yg7ZmjFb1cYfQagob6JIRxER9oU7amk91KJWJATQ4ZV8iv1m6ByUfhPA5TXvuAsW4R5E1obSSwIDAQAB',
        'rsaPrivateKey' => 'MIIEpAIBAAKCAQEAnUU/463QbHQPeNSp2EJjKI1SYAkV21jzFUwLMISw7oUIdYJrnCMS9whwIgSobkjK+iWYgi2Gxh/XecMOtUyjJAx1rKylXcHocBOVJMlLEH01Mikwq7F4RL5HshFGpr6MYbWolcogiY256e3e9dBZXJ7rZRjhDi9xOC99cWcJ+9HrIB9UeIVjMYKhtv5ZZQx+IRjWYCikr2G5i8WnuafJ3H9DDVX+UyUo43JsukiZXNXboVVLrX4OunZMoYSJtM+drjVthYi8v/Mnt6uKWrsUtBq3Ec4JEPJc1G3WRAZVuuAvcxzkrh4eRcQ2mHiz7MBP5gSJCVeUUjk66ElKHqCRgwIDAQABAoIBAQCT939w2yvpnhigepWCHpPkp3IFYQbrZPjv5KfyciV1hDy9MoPEV0uUmJ/HodJGUL6IM68Va3gOGA9HRDomYYBsfjyIxbagS7xBFmuQPMMfNG5ET3S3VTXul4glBRQP9d9mXt/Kbm3GfU2Zxm+rnuc0uWCHsaytB/3h+uxvMZ63CQAusO1zHtwQG/tquYPmQ5s8GZpFSVEIZH+JaqBMrmJWZcxTJ5v7JiiEIQkY74KDXyu7F6py7uFQVxUTPxh232bEH1R/e7gCPEetgoN7GGfpWrp5QpdgVDzc7RNij4xAIC3oKlZhQfXhhLwa/W0Sypm7Ndxpb8cAY5LlMOGaw1qhAoGBAOg8kyKsEDKOfRZbFaMApI82x9aVnIabOql+r3vm9vd6iWVMQctlHGqxCc5y3K68S1COgCqfIqkWkYvvN66dGSv3EfdU28oh0nLsxrm5w/L+19UWphXCqyDO/JZXq/kPI5h2gxSMBhFqKGmoQmRRNj9xkC8G/EB98nX5U5G/3heFAoGBAK1c8RDqjHoHPvUaOUNa5GGjmXaSDSVUN4EOH7aUPIrRqO4svmBjje8g+zR3YXCNXpZ06suNKgGVxprSiLnae4PaOIicyZ0mH3gm+L1O05ztGa5VLeSlZrd9r+QJgMeF3teN2K1kgTXpuv+sqhAZyeIyQogUm3ZwG5OwngoHfB9nAoGAHOJDzoEl9pPAPQKRG1feH63JhwfOkvNFhYUrIDbmqnsEEaQ9XqWxthdUx+eDSPxERb2jdSmbrvoOmh/jhgUl91DgXCmiuG4idYa7ZKgVFejaQTdy6qvuisMTqUM4MV7Pp4u929Vaf9n7MsDpmP58x8FBFIhC4WaIvGGJIkyBu90CgYA/gqIJyXNnLQpibpX11/F815tb9ct9FmozDEKP96RqUr6papjf5PjVVubQZL+8pP227uQpZ/CwnfchNunB9Il1V1eIrK+rs5CpytUrPRqHDdFvrWLftbx4kkICr3yPG7r0itGLTBuN7a+FBPNUbK4qALGg+rOtYwqnQ3fSQsW1FwKBgQCE4x3WFMsW27+WvF1QsvO7+04BZRqFJCSLSVlNPBwkX0ciO+dXL//vYsfEuUoJ5UP0zTYFfL98Fe3fe7BCRZrZ0PQ6N8UmMkDCkfLrO/qGCnLTqcdm0vHhkgKue8+qKQC8IcGvT/P4gnQeA5+8IdkJC2Zk0SI+u3wYNz23oO8qCA=='
    ],

];
