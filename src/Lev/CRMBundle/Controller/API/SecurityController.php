<?php

namespace App\Lev\CRMBundle\Controller\API;

use App\Lev\APIBundle\Config\APIConfig;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Put;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;
use Swagger\Annotations as SWG;

class SecurityController extends StaffController
{
    /**
     * @inheritdoc
     */
    public function configure(APIConfig $config)
    {
        parent::configure($config);
        $config->getField('roles')->setSaved(false);
        $config->getField('office')->setSaved(false);
    }

    /**
     * @SWG\Tag(name="Security")
     * @SWG\Response(
     *     response=200,
     *     description="Get loggedin user details")
     * @Get("/security/loggedin", name="security_loggedin")
     */
    public function loggedin(Request $request)
    {
        $this->denyAccessUnlessGranted(
            'IS_AUTHENTICATED_FULLY'
            , null
            , 'You don\'t have access to this page'
        );

        try {
            if (!$request->isMethod('GET')) {
                throw new \Exception(
                    'Method not allowed (expected GET)'
                    , Response::HTTP_METHOD_NOT_ALLOWED
                );
            }

            /**
             * @var App\Lev\CRMBundle\Entity\Staff
             */
            $user = $this->getUser();


            $officesResult = $this->getManager()
                ->getRepository('LevCRMBundle:Office')
                ->createQueryBuilder('o')
                ->addOrderBy('o.name')
                ->getQuery()
                ->execute();
            $offices = array();
            foreach ($officesResult as $office) {
                $offices[] = $office->toArray();
            }

            $staffroles = array();
            foreach ($user->getStaffroles() as $staffrole) {
                $staffroles[] = $staffrole->toArrayShort();
            }

            $data = array(
                'loggedIn'    => true,
                'id'          => $user->getId(),
                'username'    => $user->getUsername(),
                'fullname'    => $user->getFullName(),
                'email'       => $user->getEmail(),
                'phoneTwilio' => !empty($user->getPhoneTwilio()) && null !== $user->getPhoneTwilio() && $user->getPhoneTwilio() !== ''
                    ? $user->getPhoneTwilio()
                    : $this->container->getParameter('twilio_from'),
                'office'      => $user->getOffice(),
                'staffroles'  => $staffroles,
                'roles'       => $this->get('lev_api.user.role.getter')->getAllRoles($user),
                'officesList' => $offices,
            );
        } catch (\Exception $e) {
            return $this->renderError($e);
        }

        return $this->renderJsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @SWG\Tag(name="Security")
     * @SWG\Response(
     *     response=200,
     *     description="Get loggin user profile")
     * @Get("/security/profile", name="security_get_profile")
     */
    public function getProfile(Request $request)
    {
        $this->denyAccessUnlessGranted(
            'IS_AUTHENTICATED_FULLY'
            , null
            , 'You don\'t have access to this page'
        );
        try {
            if (!$request->isMethod('GET')) {
                throw new \Exception(
                    'Method not allowed (expected GET)'
                    , Response::HTTP_METHOD_NOT_ALLOWED
                );
            }

            $record = $this->getOneByRequest($request, $this->getUser()->getId());
            if (null === $record) {
                throw new \Exception(
                    'Record not found'
                    , self::ERR_RECORD_NOT_FOUND
                );
            }
            $data = $this->prepareRecord($record);
        } catch (\Exception $e) {
            return $this->renderError($e);
        }

        return $this->renderJsonResponse($data, Response::HTTP_OK);
    }

    /**
    /**
     * @SWG\Tag(name="Security")
     * @SWG\Response(
     *     response=200,
     *     description="Update user profile")
     * @Put("/security/profile", name="security_put_profile")
     */
    public function postProfile(Request $request)
    {
        $this->denyAccessUnlessGranted(
            'IS_AUTHENTICATED_FULLY'
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

        return $this->renderJsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @SWG\Tag(name="Security")
     * @SWG\Response(
     *     response=200,
     *     description="User Logout")
     * @Get("/security/logout", name="security_logout")
     */
    public function logout(Request $request)
    {
        $data = array('message' => 'You were successfully logged out.');

        $this->getManager()
            ->getRepository('App\Lev\CRMBundle\Entity\Oauth2\AccessToken')
            ->expire($this->getUser());
        $this->getManager()
            ->getRepository('App\Lev\CRMBundle\Entity\Oauth2\RefreshToken')
            ->expire($this->getUser());

        return $this->renderJsonResponse($data, Response::HTTP_OK);
    }

    protected function getPermissionsChoices()
    {
        $permissionsChoices = array();
        $permissionsYaml = Yaml::parse(file_get_contents(__DIR__ . '/../../../../../app/config/api_roles.yml'));
        $permissionsChoices[] = array(
            'name'    => 'ROLE_ADMIN',
            'label'   => 'Administrator (full access)',
            'records' => array(),
        );

        foreach($permissionsYaml['parameters']['lev_api.other_roles'] as $roleId => $label) {
            $permissionsChoices[] = array(
                'name'    => $roleId,
                'label'   => $label,
                'records' => array(),
            );
        }

        foreach($permissionsYaml['parameters']['lev_api.crud_roles'] as $entity => $permissions) {
            $roleId    = strtoupper('ROLE_' . $entity);
            $roleLabel = ucfirst($entity) . ' FULL ACCESS';
            $permissionsChoices[] = array(
                'name'    => $roleId,
                'label'   => $roleLabel,
                'records' => array(),
            );
            foreach($permissions as $role) {
                $roleId    = strtoupper('ROLE_' . $entity . '_' . $role);
                $roleLabel = ucfirst($entity) . ' ' . strtoupper($role);
                $permissionsChoices[] = array(
                    'name'    => $roleId,
                    'label'   => $roleLabel,
                    'records' => array(),
                );
            }
        }

        return $permissionsChoices;
    }

    protected function getPermissionsHeaders(array $permissions)
    {
        $headers = array();

        $staffroles = $this->getManager()
            ->getRepository('App\Lev\CRMBundle\Entity\StaffRole')
            ->findAll();

        foreach($staffroles as $staffrole) {
            $headers[] = $staffrole->getName();
            foreach ($permissions as $key => $permission) {
                if ($permission['name'] === 'ROLE_ADMIN') {
                    $permissions[$key]['records'][] = $staffrole->isSuperadmin() ? 1 : 0;
                } else {
                    $permissions[$key]['records'][] =
                        in_array($permission['name'], $staffrole->getRoles()) ? 1 : 0;

                }
            }
        }

        return array($headers, $permissions);
    }

}
