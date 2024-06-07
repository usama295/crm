<?php

namespace App\Lev\CRMBundle\Controller\API;

use FOS\RestBundle\Controller\Annotations\RouteResource;
use App\Lev\APIBundle\Config\APIConfig;
use App\Lev\CRMBundle\Controller\AbstractAPICRMController;


/**
 * @RouteResource("Contractor")
 */
class ContractorController extends AbstractAPICRMController
{

    /**
     * @inheritdoc
     */
    public function getModelClass()
    {
        return '\App\Lev\CRMBundle\Entity\Contractor';
    }

    /**
     * @inheritdoc
     */
    public function configure(APIConfig $config)
    {

        $fields = array(
            array('name' => 'id', 'exposed' => true, 'saved' => false),
            array('name' => 'name', 'exposed' => true, 'saved' => true, 'filter' => 'string_search', 'search' => true),
        );

        $config->setQueryMaxPerPage(20)
            ->setFieldsFromArray($fields)
            ->setDefaultRoles('contractor')
            ->setQuerySort(array(
                'name' => 'ASC'
            ));
    }

}
