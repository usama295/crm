<?php

namespace App\Lev\CRMBundle\Controller\API;

use FOS\RestBundle\Controller\Annotations\RouteResource;
use App\Lev\APIBundle\Config\APIConfig;
use App\Lev\CRMBundle\Controller\AbstractAPICRMController;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\Response;

/**
 * @RouteResource("ProductExtra")
 */
class ProductExtraController extends AbstractAPICRMController
{

    /**
     * @inheritdoc
     */
    public function getModelClass()
    {
        return '\App\Lev\CRMBundle\Entity\ProductExtra';
    }

    /**
     * @inheritdoc
     */
    public function configure(APIConfig $config)
    {
        $fields = array(
            array('name' => 'id', 'exposed' => true, 'saved' => false),
            array('name' => 'category', 'exposed' => true, 'saved' => true, 'filter' => 'string_search', 'search' => true),
            array('name' => 'name', 'exposed' => true, 'saved' => true, 'filter' => 'string_search', 'search' => true),
            array('name' => 'type', 'exposed' => true, 'saved' => true),
            array('name' => 'cost', 'exposed' => true, 'saved' => true),
        );

        $config->setQueryMaxPerPage(20)
            ->setFieldsFromArray($fields)
            ->setDefaultRoles('productextra')
            ->setQuerySort(array(
                'name' => 'ASC'
            ));
    }

}
