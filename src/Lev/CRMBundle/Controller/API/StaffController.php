<?php

namespace App\Lev\CRMBundle\Controller\API;

use FOS\RestBundle\Controller\Annotations\RouteResource;
use App\Lev\APIBundle\Config\APIConfig;
use App\Lev\APIBundle\Controller\ORM\AbstractAPIController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Get;
use Swagger\Annotations as SWG;

/**
 * @RouteResource("Staffs")
 */
class StaffController extends AbstractAPIController
{
    /**
     * @inheritdoc
     */
    public function getModelClass()
    {
        return '\App\Lev\CRMBundle\Entity\Staff';
    }

    /**
     * @inheritdoc
     */
    public function configure(APIConfig $config)
    {
        $fields = array(
            array('name' => 'id', 'exposed' => true, 'saved' => false),
            array('name' => 'roles', 'exposed' => true, 'saved' => false),
            array('name' => 'username', 'exposed' => true, 'saved' => false, 'filter' => 'string_search', 'search' => true),
            array('name' => 'firstName', 'exposed' => true, 'saved' => true, 'filter' => 'string_search', 'search' => true),
            array('name' => 'lastName', 'exposed' => true, 'saved' => true, 'filter' => 'string_search', 'search' => true),
            array('name' => 'superAdmin', 'exposed' => true, 'saved' => true, 'filter' => 'boolean', 'boolean' => true),
            array('name' => 'enabled', 'exposed' => true, 'saved' => true, 'filter' => 'boolean'),
            array('name' => 'email', 'exposed' => true, 'saved' => true, 'filter' => 'string', 'search' => true),
            array('name' => 'office', 'exposed' => true, 'saved' => true, 'filter' => 'objectid', 'object' => '\App\Lev\CRMBundle\Entity\Office'),
            array('name' => 'staffroles', 'exposed' => true, 'saved' => true, 'collection' => '\App\Lev\CRMBundle\Entity\StaffRole'),
            array('name' => 'positionTitle', 'exposed' => true, 'saved' => true),
            array('name' => 'employmentType', 'exposed' => true, 'saved' => true),
            array('name' => 'employmentDateStart', 'exposed' => true, 'saved' => true, 'date' => true),
            array('name' => 'employmentDateEnd', 'exposed' => true, 'saved' => true, 'date' => true),
            array('name' => 'salesCapabComp', 'exposed' => true, 'saved' => true),
            array('name' => 'projectCapabComp', 'exposed' => true, 'saved' => true),
            array('name' => 'marketingCapabComp', 'exposed' => true, 'saved' => true, 'boolean' => true),
            array('name' => 'certifiedRenovator', 'exposed' => true, 'saved' => true),
            array('name' => 'certificationId', 'exposed' => true, 'saved' => true),
            array('name' => 'lswpJobTraining', 'exposed' => true, 'saved' => true),
            array('name' => 'certificationExpiration', 'exposed' => true, 'saved' => true, 'date' => true),
            array('name' => 'attachments', 'exposed' => false, 'saved' => false),
            array('name' => 'deletedAttachments', 'exposed' => false, 'saved' => false),
            array('name' => 'insertedAttachments', 'exposed' => false, 'saved' => false),
            array('name' => 'addressStreet', 'exposed' => true, 'saved' => true),
            array('name' => 'addressCity', 'exposed' => true, 'saved' => true),
            array('name' => 'addressState', 'exposed' => true, 'saved' => true),
            array('name' => 'addressZip', 'exposed' => true, 'saved' => true),
            array('name' => 'addressLat', 'exposed' => true, 'saved' => true),
            array('name' => 'addressLng', 'exposed' => true, 'saved' => true),
            array('name' => 'phoneHome', 'exposed' => true, 'saved' => true),
            array('name' => 'phoneMobile', 'exposed' => true, 'saved' => true),
            array('name' => 'phoneWork', 'exposed' => true, 'saved' => true),
            array('name' => 'phoneTwilio', 'exposed' => true, 'saved' => true),
            array('name' => 'emergencyContactName', 'exposed' => true, 'saved' => true),
            array('name' => 'emergencyContactPhone', 'exposed' => true, 'saved' => true),
            array('name' => 'emergencyContactRelation', 'exposed' => true, 'saved' => true),
            array('name' => 'driverLicenceNumber', 'exposed' => true, 'saved' => true),
            array('name' => 'driverLicenceState', 'exposed' => true, 'saved' => true),
            array('name' => 'driverLicenceExpiration', 'exposed' => true, 'saved' => true, 'date' => true),
            array('name' => 'autoLiabInsProvider', 'exposed' => true, 'saved' => true),
            array('name' => 'autoLiabInsCoverage', 'exposed' => true, 'saved' => true),
            array('name' => 'autoLiabInsExpiration', 'exposed' => true, 'saved' => true, 'date' => true),
            array('name' => 'workersCompInsProvider', 'exposed' => true, 'saved' => true),
            array('name' => 'workersCompInsCoverage', 'exposed' => true, 'saved' => true),
            array('name' => 'workersCompInsExpiration', 'exposed' => true, 'saved' => true, 'date' => true),
            array('name' => 'liabInsProvider', 'exposed' => true, 'saved' => true),
            array('name' => 'liabInsCoverage', 'exposed' => true, 'saved' => true),
            array('name' => 'liabInsExpiration', 'exposed' => true, 'saved' => true, 'date' => true),
            array('name' => 'password', 'exposed' => false, 'saved' => false),
            array('name' => 'confirm_password', 'exposed' => false, 'saved' => false),
        );

        $config->setQueryMaxPerPage(20)
            ->setFieldsFromArray($fields)
            ->setDefaultRoles('staff')
            ->setQuerySort(array(
                'lastName' => 'ASC',
                'firstName' => 'ASC',
            ));
    }

    /**
     * @inheritdoc
     */
    public function getQueryBuilder(Request $request)
    {
        $qb = parent::getQueryBuilder($request);
        $qb->leftJoin('e.staffroles', 'staffroles');

        return $qb;
    }

    /**
     * Return the user office
     * @SWG\Tag(name="Staff")
     * @SWG\Response(
     *     response=200,
     *     description="Get list of offices")
     * @return \Lev\CRMBundle\Entity\Office|null
     */
    protected function getOffice()
    {
        return null === $this->getUser()
            ? null
            : $this->getUser()->getOffice();
    }

    /**
     * @SWG\Tag(name="Staff")
     * @SWG\Response(
     *     response=200,
     *     description="Successfully created staff")
     * @inheritdoc
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

            /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
            $userManager = $this->get('fos_user.user_manager');

            $record = $userManager->createUser();
            // $record
            //     ->setOffice($this->getOffice())
            //     ->setEnabled(true);

            $record = $this->updateRecord($record, $request);
            $plainPassword = $this->getPlainPassword($request);
            $record->setPlainPassword($plainPassword);
            $record->setUsername($record->getEmail());
            $userManager->updatePassword($record);
            $errors = $this->getValidator()->validate($record);

            if (count($errors) > 0) {
                return $this->renderValidationErrors($errors);
            }
            $userManager->updateUser($record);
            $data = $this->prepareRecord($record);

        } catch (\Exception $e) {
            return $this->renderError($e);
        }

        return $this->renderJsonResponse($data, Response::HTTP_CREATED);
    }

    protected function getPlainPassword(Request $request)
    {
        $data = $request->request->all();

        if (!array_key_exists('password', $data)) {
            throw new \Exception('Password is required to create a Staff', self::ERR_VALIDATE);
        }
        if (!array_key_exists('password', $data) || !array_key_exists('confirm_password', $data)) {
            throw new \Exception('Password/Confirmation is required to create a Staff', self::ERR_VALIDATE);
        }
        if ($data['password'] !== $data['confirm_password']) {
            throw new \Exception('Password/Confirmation don\'t match', self::ERR_VALIDATE);
        }

        return $data['password'];
    }

    /**
     * @SWG\Tag(name="Staff")
     * @SWG\Response(
     *     response=200,
     *     description="Successfully updated staff")
     * @inheritdoc
     */
    public function putAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted(
            $this->getRoleName('UPDATE')
            , null
            , 'You don\'t have access to this page'
        );

        try {
            $record = $this->getOneByRequest($request, $id, true);

            if (!$record) {
                throw new \Exception(
                    'Record not found'
                    , self::ERR_RECORD_NOT_FOUND
                );
            }

            if (!$request->isMethod('PUT')) {
                throw new \Exception(
                    'Method not allowed (expected PUT)'
                    , Response::HTTP_METHOD_NOT_ALLOWED
                );
            }

            /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
            $userManager = $this->get('fos_user.user_manager');

            $record = $this->updateRecord($record, $request);
            if ($this->isPasswordSent($request)) {
                $plainPassword = $this->getPlainPassword($request);
                $record->setPlainPassword($plainPassword);
                $userManager->updatePassword($record);
            }
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

        return $this->renderJsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @SWG\Tag(name="Staff")
     * @SWG\Response(
     *     response=200,
     *     description="Successfully deleted staff")
     * @inheritdoc
     */
    public function deleteAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted(
            $this->getRoleName('DELETE')
            , null
            , 'You don\'t have access to this page'
        );

        try {

            if (!$request->isMethod('DELETE')) {
                throw new \Exception(
                    'Method not allowed (expected DELETE)'
                    , Response::HTTP_METHOD_NOT_ALLOWED
                );
            }

            if ($id === 1 || $id === '1') {
                throw new \Exception(
                    'User cannot be deleted'
                    , self::ERR_RECORD_NOT_FOUND
                );
            }

            $record = $this->getOneByRequest($request, $id);

            if (null === $record) {
                throw new \Exception(
                    'Record not found'
                    , self::ERR_RECORD_NOT_FOUND
                );
            }

            $staffService = $this->get('lev_crm.service.staff');
            $staffService->deleteStaff($record);

            $data = array(
                'message' => 'Staff deleted, all related records moved to new owner'
            );

        } catch (\Exception $e) {
            return $this->renderError($e);
        }

        return $this->renderJsonResponse($data, Response::HTTP_OK);
    }

    protected function isPasswordSent(Request $request)
    {
        $data = $request->request->all();

        if (!array_key_exists('password', $data) || !array_key_exists('confirm_password', $data)) {
            return false;
        }

        return true;
    }

     /**
     * @SWG\Tag(name="Staff")
     * @SWG\Response(
     *     response=200,
     *     description="Get sales representatives")
     * @Get("/staffs/salesreps", name="staff_sales_reps")
     */
    public function getSalesReps(Request $request)
    {
        return $this->getChoices($request, 4);
    }

    /**
     * @SWG\Tag(name="Staff")
     * @SWG\Response(
     *     response=200,
     *     description="Get neartest sales representatives")
     * @todo - lat/lng filter
     * @Get("/staffs/nearsalesreps/{lat}/{lng}", name="staff_near_sales_reps")
     */
    public function getNearestSalesReps(Request $request)
    {
        return $this->getChoices($request, 4);
    }

    /**
     * @SWG\Tag(name="Staff")
     * @SWG\Response(
     *     response=200,
     *     description="Get marketing representatives")
     * @Get("/staffs/marketingreps", name="staff_marketing_reps")
     */
    public function getMarketingReps(Request $request)
    {
        return $this->getChoices($request, 2);
    }

    /**
     * @SWG\Tag(name="Staff")
     * @SWG\Response(
     *     response=200,
     *     description="Get call representatives")
     * @Get("/staffs/callcentersreps", name="staff_callcenters_reps")
     */
    public function getCallCentersReps(Request $request)
    {
        return $this->getChoices($request, 3);
    }

    /**
     * @inheritdoc
     */
    public function getChoices(Request $request, $staffroleId)
    {
        try {
            if (!$request->isMethod('GET')) {
                throw new \Exception(
                    'Method not allowed (expected GET)',
                    Response::HTTP_METHOD_NOT_ALLOWED
                );
            }

            $qb = $this->getQueryBuilder($request)
                ->where('staffroles.id = ' .$staffroleId)
                ->orderBy('e.lastName', 'ASC')
                ->addOrderBy('e.firstName', 'ASC');

            if ($request->get('officeid', false)) {
              $qb->innerJoin('e.office', 'office')
                  ->andWhere('office.id = :officeid')
                  ->setParameter('officeid', $request->get('officeid', false));
            }
            $qb->andWhere('e.enabled = 1');

            $currentPage = $request->query->get('page', 1);
            $maxPerPage = $request->query->get(
                'limit', 99999999
            );
            // Pagination with Pagerfanta
            $adapter = $this->getPaginatorAdapter($qb);
            $data = $this->paginate($adapter, $maxPerPage, $currentPage);

        } catch (\Exception $e) {
            return $this->renderError($e);
        }

        return $this->renderJsonResponse($data, Response::HTTP_OK);
    }

}
