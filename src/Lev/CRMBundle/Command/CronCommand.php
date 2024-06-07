<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Lev\CRMBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Matthieu Bontemps <matthieu@knplabs.com>
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Luis Cordova <cordoval@gmail.com>
 * @author Rafael Goulart <rafaelgou@gmail.com>
 */
class CronCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('lev:cron')
            ->setDescription('Runs CRON tasks.')
            ->setDefinition(array())
            ->setHelp(<<<EOT
The <info>lev:cron</info> runs CRON tasks:

  <info>php app/console lev:cron --env=test</info>
EOT
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $callService = $this->getContainer()->get('lev_crm.service.call');
        $logs = $callService->runCronTasks();
        $this->getContainer()->get('logger')->info(
            "CRON TASKS: {$logs['pitch-miss']} pitch-miss, {$logs['no-pitch']} no-pitch"
        );
    }

}
