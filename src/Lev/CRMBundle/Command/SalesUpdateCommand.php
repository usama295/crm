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
class SalesUpdateCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('lev:saleupdate')
            ->setDescription('Import base data.')
            ->setDefinition(array())
            ->setHelp(<<<EOT
The <info>lev:saleupdate</info> update sold price on sale:

  <info>php app/console lev:saleupdate --env=test</info>
EOT
        );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $prodCalcService = $this->getContainer()->get('lev_crm.service.productcalculator');
        $prodCalcService->uptadeAllCosts($output);

        $appointmentService = $this->getContainer()->get('lev_crm.service.appointment');
        $appointmentService->uptadeAllJobCeilings($output);

        $saleService = $this->getContainer()->get('lev_crm.service.sale');
        $saleService->uptadeAllSoldSales($output);

        $output->writeln(PHP_EOL . PHP_EOL . 'Sale Update finished'. PHP_EOL . PHP_EOL);
    }

    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
    }

}
