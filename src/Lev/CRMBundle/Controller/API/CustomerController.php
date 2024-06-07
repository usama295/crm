<?php

namespace App\Lev\CRMBundle\Controller\API;

use FOS\RestBundle\Controller\Annotations\RouteResource;
use App\Lev\APIBundle\Config\APIConfig;
use App\Lev\CRMBundle\Controller\AbstractAPICRMController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;

/**
 * @RouteResource("Customer")
 */
class CustomerController extends AbstractAPICRMController
{

    /**
     * @inheritdoc
     */
    public function getModelClass()
    {
        return '\App\Lev\CRMBundle\Entity\Customer';
    }

    /**
     * @inheritdoc
     */
    public function configure(APIConfig $config)
    {
        $fields = array(
            array('name' => 'id', 'exposed' => true, 'saved' => false, 'filter' => 'integer', 'search' => true),
            array('name' => 'fullname', 'exposed' => false, 'saved' => false, 'filter' => 'customer_name'),
            array('name' => 'office', 'exposed' => true, 'saved' => true, 'object' => '\App\Lev\CRMBundle\Entity\Office'),
            array('name' => 'gid', 'exposed' => true, 'saved' => false, 'filter' => 'string'),
            array('name' => 'primaryFirstName', 'exposed' => true, 'saved' => true, 'filter' => 'string_search', 'search' => true),
            array('name' => 'primaryLastName', 'exposed' => true, 'saved' => true, 'filter' => 'string_search', 'search' => true),
            array('name' => 'secondaryFirstName', 'exposed' => true, 'saved' => true, 'filter' => 'string_search'),
            array('name' => 'secondaryLastName', 'exposed' => true, 'saved' => true, 'filter' => 'string_search'),
            array('name' => 'secondaryRelationship', 'exposed' => true, 'saved' => true),
            array('name' => 'homeYearBuilt', 'exposed' => true, 'saved' => true),
            array('name' => 'homeYearPurchased', 'exposed' => true, 'saved' => true),
            array('name' => 'structureType', 'exposed' => true, 'saved' => true),
            array('name' => 'householdIncome', 'exposed' => true, 'saved' => true),
            array('name' => 'homeValue', 'exposed' => true, 'saved' => true),
            array('name' => 'addressStreet', 'exposed' => true, 'saved' => true),
            array('name' => 'addressCity', 'exposed' => true, 'saved' => true),
            array('name' => 'addressState', 'exposed' => true, 'saved' => true),
            array('name' => 'addressZip', 'exposed' => true, 'saved' => true),
            array('name' => 'addressLat', 'exposed' => true, 'saved' => true),
            array('name' => 'addressLng', 'exposed' => true, 'saved' => true),
            array('name' => 'phone1Number', 'exposed' => true, 'saved' => true, 'search' => true),
            array('name' => 'phone1Type', 'exposed' => true, 'saved' => true),
            array('name' => 'phone2Number', 'exposed' => true, 'saved' => true, 'search' => true),
            array('name' => 'phone2Type', 'exposed' => true, 'saved' => true),
            array('name' => 'phone3Number', 'exposed' => true, 'saved' => true, 'search' => true),
            array('name' => 'phone3Type', 'exposed' => true, 'saved' => true),
            array('name' => 'primaryPhone', 'exposed' => true, 'saved' => true),
            array('name' => 'bestTimeCall', 'exposed' => true, 'saved' => true),
            array('name' => 'email', 'exposed' => true, 'saved' => true, 'filter' => 'string_search', 'search' => true),
            array('name' => 'tcpa', 'exposed' => true, 'saved' => true),
            array('name' => 'wrongNumber', 'exposed' => true, 'saved' => true),
            array('name' => 'restrictionComments', 'exposed' => true, 'saved' => true),
            array('name' => 'histories', 'exposed' => false, 'saved' => false),
            array('name' => 'historyNote', 'exposed' => true, 'saved' => false),
            array('name' => 'createdAt', 'exposed' => true, 'saved' => false, 'filter' => 'daterange', 'date' => true),
            array('name' => 'createdBy', 'exposed' => true, 'saved' => false,
                'filter' => 'objectid', 'object' => '\App\Lev\CRMBundle\Entity\Staff'),
            array('name' => 'attachments', 'exposed' => true, 'saved' => false),
            array('name' => 'deletedAttachments', 'exposed' => false, 'saved' => false),
            array('name' => 'insertedAttachments', 'exposed' => false, 'saved' => false),
             array('name' => 'isdeleted', 'exposed' => true, 'saved' => true),
        );

        $config->setQueryMaxPerPage(20)
            ->setFieldsFromArray($fields)
            ->setDefaultRoles('customer')
            ->setQuerySort(array(
                'primaryLastName' => 'ASC',
                'primaryFirstName' => 'ASC',
            ));

            // print_r($config);
            // die();


    }
    /*
     * @SWG\Tag(name="Customer")
     * @SWG\Response(
     *     response=200,
     *     description="get Customer by request")
     */
    public function postAction(Request $request)
    {
        $this->denyAccessUnlessGranted($this->getRoleName('CREATE'), null, 'You don\'t have access to this');

        $this->getManager()->getConnection()->beginTransaction();
        try {

            if (!$request->isMethod('POST')) {
                throw new \Exception('Method not allowed (expected POST)', Response::HTTP_METHOD_NOT_ALLOWED);
            }

            $class = $this->getModelClass();
            $record = new $class;

            $record = $this->updateRecord($record, $request);
            $errors = $this->getValidator()->validate($record);

            if (count($errors) > 0) {
                return $this->renderValidationErrors($errors);
            }
            $this->getManager()->persist($record);
            $this->getManager()->flush();

            $generateIdService = $this->get('lev_crm.service.customer_generate_id');
            $record = $generateIdService->generate($record);

            $data = $this->prepareRecord($record);

            $this->getManager()->getConnection()->commit();
        } catch (\Exception $e) {
            $this->getManager()->getConnection()->rollBack();
            return $this->renderError($e, 'api_post');
        }

        return $this->renderJsonResponse($data, Response::HTTP_CREATED);
    }

    /**
     * @SWG\Tag(name="Customer")
     * @SWG\Response(
     *     response=200,
     *     description="get Customer by request")
     * Get record by request data
     *
     * @param Request $request The Request
     * @param integer $id      The record primary key
     *
     * @return mixed
     */
    public function getOneByRequest(Request $request, $id, $hydrate = false)
    {
        $this->getConfig()->addFieldFromArray(array(
           'name' => 'histories', 'exposed' => true, 'saved' => false
        ));
        $this->classArrayType = 'toArrayWithHistory';

        $qb = $this->getRepository()->createQueryBuilder('e');
        $qb->leftJoin('e.histories', 'histories');

        return $qb->andWhere('e.id = :id')
           ->setParameter('id', $id)
           ->getQuery()
           ->getOneOrNullResult();


    }

    public function getQueryBuilder(Request $request)
    {
         $query = $this->getRepository()->createQueryBuilder('e')
                ->where('e.isdeleted = 0');

                return $query;
    }


    public function  deleteAction(Request $request, $id)
    {

    $em = $this->getDoctrine()->getManager();     
$query = $em->getRepository('\App\Lev\CRMBundle\Entity\Customer')->createQueryBuilder('')
            ->update('\App\Lev\CRMBundle\Entity\Customer', 'u')

            ->set('u.isdeleted', ':isdeleted')
            ->setParameter('isdeleted', 1)

            ->where('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery();

$result = $query->execute();

 return $this->renderJsonResponse($result, Response::HTTP_OK);
}
}
