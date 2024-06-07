<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 29/04/15
 * Time: 00:28
 */

namespace App\Lev\CRMBundle\DependencyInjection;

use JMS\SerializerBundle\DependencyInjection\HandlerFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class UserSerializerHandlerFactory implements HandlerFactoryInterface
{
    public function getConfigKey()
    {
        return 'serializer_user';
    }

    public function getType(array $config)
    {
        return self::TYPE_SERIALIZATION;
    }

    public function addConfiguration(ArrayNodeDefinition $builder)
    {
        $builder
            ->addDefaultsIfNotSet()
            ->defaultValue(array())
        ;
    }

    public function getHandlerId(ContainerBuilder $container, array $config)
    {
        return 'serializer.user_handler';
    }
}