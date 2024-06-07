<?php

namespace App\Lev\CRMBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Lev\APIBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Put;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;
use Swagger\Annotations as SWG;

class SystemController extends AbstractController
{

    /**
     * @SWG\Tag(name="System")
     * @SWG\Response(
     *     response=200,
     *     description="Returns System details")
     * @Get("/system", name="system_get_permissions")
     */
    public function getSystem(Request $request)
    {
        $this->denyAccessUnlessGranted(
            'IS_AUTHENTICATED_FULLY'
            , null
            , 'You don\'t have access to this page'
        );
        $data = array();

        return $this->renderJsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @SWG\Tag(name="System")
     * @SWG\Response(
     *     response=200,
     *     description="Returns System Prmission details")
     * @Get("/system/permissions", name="system_get_permissions")
     */
    public function getPermissions(Request $request)
    {
        $this->denyAccessUnlessGranted(
            'ROLE_ADMIN'
            , null
            , 'You don\'t have access to this page'
        );

        try {
            $data = array();
            list($data['headers'], $data['permissions']) = $this->getPermissionsHeaders(
                $this->getPermissionsChoices()
            );
        } catch (\Exception $e) {
            return $this->renderError($e);
        }

        return $this->renderJsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @SWG\Tag(name="System")
     * @SWG\Response(
     *     response=200,
     *     description="Update System Prmission details")
     * @Put("/system/permissions", name="system_put_permissions")
     */
    public function postPermissions(Request $request)
    {
        $this->denyAccessUnlessGranted(
            'ROLE_ADMIN'
            , null
            , 'You don\'t have access to this page'
        );

        try {

            $dataRequest = $request->request->all();
            $roles       = array();
            $superadmins = array();
            foreach(range(0, count($dataRequest['headers']) - 1) as $key) {
                $roles[$key]       = array();
                $superadmins[$key] = 0;
            }

            if (is_object($dataRequest['permissions'])) {
                $dataRequest['permissions'] = get_object_vars($dataRequest['permissions']);
            }
            foreach ($dataRequest['permissions'] as $permission) {
                if (is_object($permission)) {
                    $permission = get_object_vars($permission);
                }
                foreach ($permission['records'] as $key => $value) {
                    if ($permission['name'] === 'ROLE_ADMIN') {
                        $superadmins[$key] = (boolean) $value;
                        continue;
                    }
                    if ($value === 1) {
                        $roles[$key][] = $permission['name'];
                    }
                }
            }

            $staffroles = $this->getManager()
                ->getRepository('App\Lev\CRMBundle\Entity\StaffRole')
                ->findAll();

            foreach($staffroles as $key => $staffrole) {
                $staffrole
                    ->setRoles($roles[$key])
                    ->setSuperadmin($superadmins[$key]);
                $this->getManager()->persist($staffrole);
            }
            $this->getManager()->flush();

            $data = array();
            list($data['headers'], $data['permissions']) = $this->getPermissionsHeaders(
                $this->getPermissionsChoices()
            );
        } catch (\Exception $e) {
            return $this->renderError($e);
        }

        return $this->renderJsonResponse($data, Response::HTTP_OK);
    }

    protected function getPermissionsChoices()
    {
        $permissionsChoices = array();
        $permissionsYaml = Yaml::parse(file_get_contents(__DIR__ . '/../../../../../config/api_roles.yaml'));
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
