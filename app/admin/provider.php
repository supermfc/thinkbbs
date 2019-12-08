<?php
// 这是系统自动生成的provider定义文件
return [
	// admin provider
    \tpadmin\service\upload\contract\Factory::class => \tpadmin\service\upload\Uploader::class,
    \tpadmin\service\auth\contract\Authenticate::class => \tpadmin\model\Adminer::class,
    \tpadmin\service\auth\guard\contract\Guard::class => \tpadmin\service\auth\guard\SessionGuard::class,
    \tpadmin\service\auth\contract\Auth::class => \tpadmin\service\auth\Auth::class,
];
