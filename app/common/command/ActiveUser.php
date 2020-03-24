<?php
declare (strict_types = 1);

/*
使用 php think make:command common@ActiveUser 创建
然后修改项目config/console.php 文件注册指令
'commands' => [
        'bbs:active-user' => 'app\common\command\ActiveUser',
    ],

 */
namespace app\common\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

use app\common\model\User;

class ActiveUser extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('bbs:activeuser')
            ->setDescription('计算活跃用户');        
    }

    protected function execute(Input $input, Output $output)
    {
    	User::calculateAndCacheActiveUsers();
        // 指令输出
    	$output->writeln('计算活跃用户结束');
    }
}
