<?php

namespace Blankkids\WebmanBuild\Commands;

use Blankkids\WebmanBuild\Base;
use Blankkids\WebmanBuild\Templates\ControllerTemplate;
use Blankkids\WebmanBuild\Templates\EntityTemplate;
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
        $model_name = $name;
        $suffix = config('plugin.blankkids.webman-build.app.file_name_format.model', '');
        if ($suffix && !strpos($model_name, $suffix)) {
            $model_name .= $suffix;
        }
        $model_name = str_replace('\\', '/', $model_name);
        $namespace = config('plugin.blankkids.webman-build.app.domain_path', 'app') . DIRECTORY_SEPARATOR . $module_name . DIRECTORY_SEPARATOR . 'model';
        $file = config('plugin.blankkids.webman-build.app.domain_path', 'app') . DIRECTORY_SEPARATOR . $module_name . DIRECTORY_SEPARATOR . 'model' . DIRECTORY_SEPARATOR . $model_name . '.php';

        ModelTemplate::create($model_name, $name, $namespace, $file);

        //创建模型实体
        $model_name = $name;
        $suffix = config('plugin.blankkids.webman-build.app.file_name_format.entity', '');
        if ($suffix && !strpos($model_name, $suffix)) {
            $model_name .= $suffix;
        }
        $model_name = str_replace('\\', '/', $model_name);
        $namespace = config('plugin.blankkids.webman-build.app.domain_path', 'app') . DIRECTORY_SEPARATOR . $module_name . DIRECTORY_SEPARATOR . 'entity';
        $file = config('plugin.blankkids.webman-build.app.domain_path', 'app') . DIRECTORY_SEPARATOR . $module_name . DIRECTORY_SEPARATOR . 'entity' . DIRECTORY_SEPARATOR . $model_name . '.php';

        EntityTemplate::create($model_name, $name, $namespace, $file);

        //创建控制器
        $controller_name = $name;
        $suffix = config('plugin.blankkids.webman-build.app.file_name_format.controller', '');
        if ($suffix && !strpos($controller_name, $suffix)) {
            $controller_name .= $suffix;
        }
        $controller_name = str_replace('\\', '/', $controller_name);
        $namespace = config('plugin.blankkids.webman-build.app.domain_path', 'app') . DIRECTORY_SEPARATOR . $module_name . DIRECTORY_SEPARATOR . 'port' . DIRECTORY_SEPARATOR . 'controller';
        $file = config('plugin.blankkids.webman-build.app.domain_path', 'app') . DIRECTORY_SEPARATOR . $module_name . DIRECTORY_SEPARATOR . 'port' . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . $controller_name . '.php';

        ControllerTemplate::create($controller_name, $namespace, $file);

        return self::SUCCESS;
    }


}
