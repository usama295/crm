<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Lev\APIBundle\Command;

use OAuth2\OAuth2;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use FOS\OAuthServerBundle\Model\ClientManagerInterface;

/**
 * @author Matthieu Bontemps <matthieu@knplabs.com>
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Luis Cordova <cordoval@gmail.com>
 * @author Rafael Goulart <rafaelgou@gmail.com>
 */
class CreateOathClientCommand extends ContainerAwareCommand
{
 
    private $clientManager;
    public function __construct(ClientManagerInterface $clientManager)
    {
        parent::__construct();
        $this->clientManager = $clientManager;
    }

    /**
     * @see Command
     */
    public function configure()
    {
        parent::configure();
        $this
            ->setName('oauth:client:create')
            //->setName('fos:oauth-server:create-client')
            ->setDescription('Create an Oauth2 Client .')
            ->addOption(
                'redirect-uri',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Sets redirect uri for client. Use this option multiple times to set multiple redirect URIs.',
                null
            )
            ->addOption(
                'grant-type',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Sets allowed grant type for client. Use this option multiple times to set multiple grant types..',
                null
            )
            ->setHelp(<<<EOT
The <info>%command.name%</info> command creates a client:

<info>php %command.full_name% [--redirect-uri=...] [--grant-type=...] name</info>
EOT
            );
    }

    /**
     * @see Command
     */
     function execute(InputInterface $input, OutputInterface $output)
    {
        $redirectUri = $input->getOption('redirect-uri');
        $grantType   = $input->getOption('grant-type');

        $validGTs = array(
            OAuth2::GRANT_TYPE_AUTH_CODE,          // authorization_code
            OAuth2::GRANT_TYPE_IMPLICIT,           // token
            OAuth2::GRANT_TYPE_USER_CREDENTIALS,   // password
            OAuth2::GRANT_TYPE_CLIENT_CREDENTIALS, // client_credentials
            OAuth2::GRANT_TYPE_REFRESH_TOKEN ,     // refresh_token
            OAuth2::GRANT_TYPE_EXTENSIONS,         // extensions
        );

        if (count($grantType) === 0 ) {
            $message = 'You must inform at least one Grant Type!';
            $output->writeln('<error>' . str_repeat(' ', strlen($message) + 20) .  '</error>');
            $output->writeln("<error>         {$message}           </error>");
            $output->writeln('<error>' . str_repeat(' ', strlen($message) + 20) .  '</error>');
            exit;
        }

        foreach($grantType as $gt) {
            if(!in_array($gt, $validGTs)) {
                $output->writeln("<comment>Invalid Grant Type '$gt'</comment>");
                $output->writeln('<comment>Valids: ' . implode(', ', $validGTs) . '</comment>');
                exit;
            }
        }

        $clientManager = $this->clientManager;
        $client = $this->clientManager->createClient();


        $client->setRedirectUris($redirectUri);
        $client->setAllowedGrantTypes($grantType);
        $clientManager->updateClient($client);

        $message = 'Oauth2 Client created!';
        $output->writeln('<fg=black;bg=cyan>' . str_repeat(' ', strlen($message) + 38) .  '</fg=black;bg=cyan>');
        $output->writeln("<fg=black;bg=cyan>                  {$message}                    </fg=black;bg=cyan>");
        $output->writeln('<fg=black;bg=cyan>' . str_repeat(' ', strlen($message) + 38) .  '</fg=black;bg=cyan>');

        $output->writeln(sprintf('Redirect URIs : <comment>%s</comment>', implode(', ',$redirectUri)));
        $output->writeln(sprintf('Grant Types   : <comment>%s</comment>', implode(', ',$grantType)));
        $output->writeln(sprintf('Client ID     : <comment>%s</comment>', $client->getPublicId()));
        $output->writeln(sprintf('Client Secret : <comment>%s</comment>', $client->getSecret()));
    }

}
