<?php

namespace App\Lev\CRMBundle\Controller\API;

use FOS\RestBundle\Controller\Annotations\RouteResource;
use App\Lev\APIBundle\Config\APIConfig;
use App\Lev\CRMBundle\Controller\AbstractAPICRMController;

/**
 * @RouteResource("AdvisoryZipCode")
 */
class AdvisoryZipCodeController extends AbstractAPICRMController
{

    /**
     * @inheritdoc
     */
    public function getModelClass()
    {
        return '\App\Lev\CRMBundle\Entity\AdvisoryZipCode';
    }

    /**
     * @inheritdoc
     */
    public function configure(APIConfig $config)
    {

        $fields = array(
            array('name' => 'id', 'exposed' => true, 'saved' => false),
            array('name' => 'zipCode', 'exposed' => true, 'saved' => true, 'filter' => 'string', 'search' => true),
        );

        $config->setQueryMaxPerPage(20)
            ->setFieldsFromArray($fields)
            ->setDefaultRoles('advisoryzipcode')
            ->setQuerySort(array(
                'zipCode' => 'ASC'
            ));
    }

}
