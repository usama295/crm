<?php

namespace App\Lev\APIBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension;
use App\Lev\APIBundle\DependencyInjection\Security\Factory\ApiKeyFactory;

/**
 * Class LevAPIBundle
 *
 * @see https://github.com/uecode/api-key-bundle
 * @package Lev\APIBundle
 */
class LevAPIBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }

    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
