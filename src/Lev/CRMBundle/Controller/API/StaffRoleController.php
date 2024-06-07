<?php

namespace App\Lev\CRMBundle\Controller\API;

use FOS\RestBundle\Controller\Annotations\RouteResource;
use App\Lev\APIBundle\Config\APIConfig;
use App\Lev\CRMBundle\Controller\AbstractAPICRMController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;

/**
 * @RouteResource("StaffRole")
 */
class StaffRoleController extends AbstractAPICRMController
{
    /**
     * @inheritdoc
     */
    public function getModelClass()
    {
        return '\App\Lev\CRMBundle\Entity\StaffRole';
    }

    /**
     * @inheritdoc
     */
    public function configure(APIConfig $config)
    {
        $fields = array(
            array('name' => 'id', 'exposed' => true, 'saved' => false),
            array('name' => 'name', 'exposed' => true, 'saved' => true, 'filter' => 'string_search'),
            array('name' => 'shortname', 'exposed' => true, 'saved' => true, 'filter' => 'string'),
            array('name' => 'roles', 'exposed' => true, 'saved' => true),
            array('name' => 'superadmin', 'exposed' => true, 'saved' => true),
        );

        $config->setQueryMaxPerPage(20)
            ->setFieldsFromArray($fields)
            ->setDefaultRoles('staffrole')
        ;
    }

    /**
     * @inheritdoc
     */
    protected function getClassArrayType()
    {
        return 'toArray';
    }

    /**
     * @SWG\Tag(name="StaffRole")
     * @SWG\Response(
     *     response=200,
     *     description="Create new staffrole")
     */
    public function postAction(Request $request)
    {
        $this->denyAccessUnlessGranted(
            $this->getRoleName('CREATE')
            , null
            , 'You don\'t have access to this page'
        );

        try {

            if (!$request->isMethod('POST')) {
                throw new \Exception(
                    'Method not allowed (expected POST)'
                    , Response::HTTP_METHOD_NOT_ALLOWED
                );
            }

            $data = $request->request->all();
            $class = $this->getModelClass();
            $record = new $class($data['name']);

            $record = $this->updateRecord($record, $request);
            $errors = $this->getValidator()->validate($record);

            if (count($errors) > 0) {
                return $this->renderValidationErrors($errors);
            }

            $this->getManager()->persist($record);
            $this->getManager()->flush();
            $data = $this->prepareRecord($record);

        } catch (\Exception $e) {
            return $this->renderError($e);
        }

        return $this->renderJsonResponse($data, Response::HTTP_CREATED);
    }
}
