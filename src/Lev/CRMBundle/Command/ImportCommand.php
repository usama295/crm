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
class ImportCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('lev:import')
            ->setDescription('Import base data.')
            ->setDefinition(array())
            ->setHelp(<<<EOT
The <info>lev:import</info> import base data:

  <info>php app/console lev:import --env=test</info>
EOT
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $importService = $this->getContainer()->get('lev_crm.service.import');
        $importService
            ->setOutput($output)
            ->loadFixtures(array(
                // 'Lev\CRMBundle\Import\DataFixture\LoadClientData',
                // 'Lev\CRMBundle\Import\DataFixture\LoadOfficeData',
                // 'Lev\CRMBundle\Import\DataFixture\LoadStaffData',
                // 'Lev\CRMBundle\Import\DataFixture\LoadStaffRoleData',
                // 'Lev\CRMBundle\Import\DataFixture\LoadCustomerData',
                // 'Lev\CRMBundle\Import\DataFixture\LoadAppointmentData',
                // 'Lev\CRMBundle\Import\DataFixture\LoadSaleData',
                'Lev\CRMBundle\Import\DataFixture\LoadProjectData',
                // 'Lev\CRMBundle\Import\DataFixture\LoadProductData',
                // 'Lev\CRMBundle\Import\DataFixture\LoadProductExtraData',
            ));

        $importService->run($output);
    }

    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
    }

}
