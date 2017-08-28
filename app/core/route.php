<?php
/**
 * \Route::listenRule用于监听当前访问的路由地址
 * 1.监听地址与访问地址一致,则执行指定的函数,或者转发指定的路由.
 * 2.监听地址与访问地址不一致,则不做任何处理
 */

/**
 * 监听路由 模块.php?c=index&a=indexComment
 * 使用函数处理
 * \Route::listenRule("index/indexComment",function(){
 *      echo "index/indexComment";
 * });
 */

/**
 * 监听路由 模块.php?c=index&a=indexComment
 * 使用函数处理
 * \Route::listenRule(["index/indexComment","index/lst"],function($curRoute){
 *      echo $curRoute ; // index/indexComment or  index/lst
 * });
 */


/**
 * 监听路由 模块1.php?c=index&a=indexComment 转发到模块2.php?c=index&a=index
 * 使用函数处理
 * \Route::listenRule("module1/index/indexComment","module2/index/indexComment");
 */