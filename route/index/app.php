<?php

use think\facade\Route;

Route::get('/','index/index')->name('page.root');

// 注册
//唯一验证规则必须定义在 page.signup 之前，否则按照惰性匹配规则该路由将无法匹配到。

//在项目里注册多个路由规则后，系统会依次遍历注册过的满足请求类型的路由规则，
//一旦匹配到正确的路由规则后则开始执行最终的调度方法，后续规则就不再检测。
Route::post('signup/send_code', 'register/send_code')->name('signup.send_code');
Route::post('signup/check_unique', 'register/check_unique')->name('signup.check_unique');
Route::get('signup', 'register/create')->name('page.signup');
Route::post('signup', 'register/save')->name('page.signup.save');

// 验证填写的手机验证码是否正确
Route::post('verify/valid_code', 'verify/valid_code')->name('verify.valid_code');

// 用户登录与退出
Route::get('login', 'login/create')->name('page.login');
Route::post('login', 'login/save')->name('page.login.save');
Route::post('logout', 'login/delete')->name('page.logout');