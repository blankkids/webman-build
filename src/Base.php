<?php

namespace Blankkids\WebmanBuild;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

class Base extends Command
{
    public function makeFolderByArr($moduleName, OutputInterface $output, $folders = [], $message = null)
    {
        if (empty($folders)) {
            $childPathArr = config('plugin.blankkids.webman-build.app.child_path', '');
        } else {
            $childPathArr = $folders;
        }

        $domainPath = config('plugin.blankkids.webman-build.app.domain_path', '');
        $modulePath = str_replace("{module_name}", $moduleName, $domainPath);

        foreach ($childPathArr as $k => $v) {
            $childPath = $modulePath . DIRECTORY_SEPARATOR . $v;

            $isExistFile = file_exists($childPath);
            if ($isExistFile) {
                if ($message) {
                    $msg = "Exception : Module " . $moduleName . " | MakeFolder | " . $v . " is exists !";
                    $output->writeln($msg, OutputInterface::OUTPUT_NORMAL);
                }
            } else {
                $res = mkdir(iconv("UTF-8", "GBK", $childPath), 0755, true);
                if ($res) {
                    if ($message) {
                        $msg = "Created : Module " . $moduleName . " | MakeFolder | make " . $v . " folder OK";
                        $output->writeln($msg, OutputInterface::OUTPUT_NORMAL);
                    }
                }
            }
            usleep(100);
        }

    }


}