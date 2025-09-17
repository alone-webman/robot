<?php

namespace AloneWebMan\RoBot\command;

use AloneWebMan\RoBot\console\CommandHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BotPluginCommand extends Command {

    protected static $defaultName        = 'alone:bot';
    protected static $defaultDescription = 'bot plugin operation <info>[help|add|set|list]</info>';

    protected function configure(): void {
        // 操作类型
        $this->addArgument('type', InputArgument::OPTIONAL, "type", "set");
        // 插件名称
        $this->addArgument('plugin', InputArgument::OPTIONAL, "plugin");
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        CommandHelper::saveRobotFile();
        $type = $input->getArgument("type") ?: "";
        $plugin = $input->getArgument("plugin") ?: "";
        $command = new CommandHelper($type ?: "set", $plugin);
        $command->start();
        return self::SUCCESS;
    }

}