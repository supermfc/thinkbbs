<?php
// +----------------------------------------------------------------------
// | 控制台配置
/*

	配置成功后，php think 即可看到自己注册的指令
	然后，把以下命令加到Cron计划任务中，即可定时执行命令

	0 * * * * cd /home/vagrant/Code/ThinkBBS && php think bbs:active-user >> /dev/null 2>&1

	也可以手动执行：
	php think bbs:active-user

*/
// +----------------------------------------------------------------------
return [
    // 指令定义
    'commands' => [
    	'bbs:active-user' => 'app\common\command\ActiveUser',
    	'bbs:sync-last-active' => 'app\common\command\SyncLastActive',
    ],
];
