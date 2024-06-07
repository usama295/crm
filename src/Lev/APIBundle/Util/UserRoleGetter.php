<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 12/05/15
 * Time: 09:50
 */

namespace App\Lev\APIBundle\Util;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

class UserRoleGetter
{
    /**
     * @var RoleHierarchyInterface
     */
    protected $roleHierarchy;

    public function __construct(RoleHierarchyInterface $roleHierarchy)
    {
        $this->roleHierarchy = $roleHierarchy;
    }

    public function getAllRoles(UserInterface $user)
    {

        $roles = array();
        foreach ($user->getRoles() as $role) {
            $roles[] = $role;
            $roles = array_merge($roles, $this->flattenHierarchy($role));
        }

        $roles = array_values(array_unique($roles));
        foreach ($roles as $key => $role) {
            $roles[$key] = str_replace('ROLE_', '', $role);
        }

        return $roles;
    }

    protected function flattenHierarchy($role)
    {
        $roles = array();
        $hierarchy = $this->roleHierarchy->getHierarchyArray();

        if (array_key_exists($role, $hierarchy)) {
            $roles = array_merge($roles, array($role));
            $roles = array_merge($roles, $hierarchy[$role]);
            foreach ($hierarchy[$role] as $hRole) {
                $roles = array_merge($roles, $this->flattenHierarchy($hRole));
            }
        }

        return $roles;
    }
}