<?php
namespace App\Lev\CRMBundle\Import\DataFixture;

use App\Lev\CRMBundle\Entity\StaffRole;

class LoadStaffRoleData extends AbstractDataFixture implements DataFixtureInterface

{
    /**
     * {@inheritDoc}
     */
    public function run()
    {
        $manager = $this->getManager();
        $staffRoles = array(
            array(
                'name' => 'Admin',
                'roles' => array(),
                'superadmin' => true,
            ),
            array(
                'name' => 'Marketing Representative',
                'roles' => array('ROLE_STAFF', 'ROLE_PRODUCT_VIEW'),
                'superadmin' => false,
            ),
            array(
                'name' => 'Call Center Representative',
                'roles' => array('ROLE_STAFF', 'ROLE_OFFICE'),
                'superadmin' => false,
            ),
            array(
                'name' => 'Sales Representative',
                'roles' => array('ROLE_STAFFROLE', 'ROLE_OFFICE', 'ROLE_PRODUCT_VIEW'),
                'superadmin' => false,
            ),
        );

        $count = 0;
        $this->progressStart('staffRoles', count($staffRoles));
        foreach ($staffRoles as $sf) {
            $staffRole = new StaffRole($sf['name'], $sf['roles']);
            $staffRole->setSuperadmin($sf['superadmin']);
            $manager->persist($staffRole);
            $manager->flush();
            $this->addReference('staffrole', $staffRole->getId(), $staffRole);
            $count++;
            $this->progressAdvance();
        }
        $this->progressFinish();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2;
    }
}
