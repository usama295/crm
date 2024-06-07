<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Lev\APIBundle\Security\Core\Role;

use Symfony\Component\Security\Core\Role\RoleHierarchy as BaseRoleHierarchy;
/**
 * RoleHierarchy defines a role hierarchy.
 *
 * @author Rafael Goulart <rafaelgou@gmail.com>
 */
class RoleHierarchy extends BaseRoleHierarchy
{
    protected $localHierarchy;

    /**
     * Constructor.
     *
     * @param array $hierarchy An array defining the hierarchy
     * @param array $apiRoles  An array defining the API roles
     */
    public function __construct(array $hierarchy, array $apiRoles = array(), array $otherRoles = array())
    {
        $this->localHierarchy = $this->mergeRoles($hierarchy, $apiRoles, $otherRoles);

        parent::__construct($this->localHierarchy);
    }

    protected function mergeRoles(array $hierarchy, array $apiRoles = array(), array $otherRoles = array())
    {
        foreach($otherRoles as $role => $label) {
            $hierarchy['ROLE_ADMIN'][] = $role;
        }

        foreach($apiRoles as $entity => $roles) {
            $entityRoles = array();
            $mainRole  = strtoupper('ROLE_' . $entity);
            foreach($roles as $role) {
                $entityRoles[] = strtoupper('ROLE_' . $entity . '_' . $role);
            }
            $hierarchy['ROLE_ADMIN'][] = $mainRole;
            $hierarchy[$mainRole]      = $entityRoles;
        }
        return $hierarchy;
    }

    public function getHierarchyArray()
    {
        return $this->localHierarchy;
    }

}
