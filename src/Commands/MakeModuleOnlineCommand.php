<?php

namespace Blankkids\WebmanBuild\Commands;

use Blankkids\WebmanBuild\Base;
use Blankkids\WebmanBuild\Templates\ControllerTemplate;
use Blankkids\WebmanBuild\Templates\EntityTemplate;
use Blankkids\WebmanBuild\Templates\EnumTemplate;
use Blankkids\WebmanBuild\Templates\ErrorTemplate;
use Blankkids\WebmanBuild\Templates\MapTemplate;
use Blankkids\WebmanBuild\Templates\ModelTemplate;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class MakeModuleOnlineCommand extends Base
{
    protected static $defaultName = 'blankkids-build-module:base-online';
    protected static $defaultDescription = '创建模块名称';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, '表名称')
            ->addArgument('module-name', InputArgument::REQUIRED, '模块名称')
            ->addArgument('curd-name', InputArgument::REQUIRED, '可选生成curd函数(-,c,u,r,d,bc,bu,br,bd,cj,cmd)');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $output->writeln("创建表名称： $name");
        $module_name = $input->getArgument('module-name');
        $output->writeln("创建模块名称： $module_name");
        $curd_name = $input->getArgument('curd-name');
        $output->writeln("生成curd函数： $curd_name");

        $this->makeFolderByArr($module_name, $output);

        //创建模型
        ModelTemplate::create($name, $module_name);

        //创建模型实体
        EntityTemplate::create($name, $module_name);

        //创建模型枚举
        EnumTemplate::create($name, $module_name);

        //创建映射‌枚举
        MapTemplate::create($name, $module_name);

        //创建错误码
        ErrorTemplate::create($name, $module_name);

        //创建控制器
        ControllerTemplate::create($name, $module_name);

        return self::SUCCESS;
    }


}
