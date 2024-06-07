<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Lev\APIBundle\Model;

use FOS\UserBundle\Model\UserManager as BaseModel;

use Doctrine\Common\Persistence\ObjectManager;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Util\CanonicalizerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Doctrine\Bundle\DoctrineBundle\Registry as DoctrineRegistry;

use FOS\UserBundle\Util\PasswordUpdaterInterface;
use FOS\UserBundle\Util\CanonicalFieldsUpdater;

/**
 * Abstract User Manager implementation which can be used as base class for your
 * concrete manager.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class UserManager extends BaseModel
{
    protected $objectManager;
    protected $class;
    protected $repository;

    private $passwordUpdater;
    private $canonicalFieldsUpdater;

    /**
     * Constructor.
     *
     * @param PasswordUpdaterInterface $encoderFactory
     * @param CanonicalFieldsUpdater  $usernameCanonicalizer
     * @param CanonicalFieldsUpdater  $emailCanonicalizer
     * @param ObjectManager           $om
     * @param string                  $class
     */
    public function __construct(PasswordUpdaterInterface $encoderFactory, CanonicalFieldsUpdater $canonicalFieldsUpdater,DoctrineRegistry $doctrine, $class)
    {
        parent::__construct($encoderFactory, $canonicalFieldsUpdater);

        $this->objectManager    = $doctrine->getManager();

        $this->repository    = $this->objectManager->getRepository($class);

        $metadata    = $this->objectManager->getClassMetadata($class);
        $this->class = $metadata->getName();

        $this->passwordUpdater = $encoderFactory;
        $this->canonicalFieldsUpdater = $canonicalFieldsUpdater;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteUser(UserInterface $user)
    {
        $this->objectManager->remove($user);
        $this->objectManager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritDoc}
     */
    public function findUserBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function findUsers()
    {
        return $this->repository->findAll();
    }

    /**
     * {@inheritDoc}
     */
    public function reloadUser(UserInterface $user)
    {
        $this->objectManager->refresh($user);
    }

    /**
     * Updates a user.
     *
     * @param UserInterface $user
     * @param Boolean       $andFlush Whether to flush the changes (default true)
     */
    public function updateUser(UserInterface $user, $andFlush = true)
    {
        $this->updateCanonicalFields($user);
        $this->updatePassword($user);

        $this->objectManager->persist($user);
        if ($andFlush) {
            $this->objectManager->flush();
        }
    }

       
    /**
     * Finds a user by username
     *
     * @param string $username
     *
     * @return UserInterface
     */
    public function findUserByUsername($username)
    {
        // @TODO User provider not working:
        // providers:
        //     fos_userbundle:
        //         id: fos_user.user_provider.username_email <= why not working?

        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            //return $this->findUserByEmail($username);
            return $this->findUserBy(array('emailCanonical' => $this->canonicalFieldsUpdater->canonicalizeEmail($username)));
        }
        //return $this->findUserBy(array('usernameCanonical' => $this->canonicalizeUsername($username)));
        return $this->findUserBy(array('usernameCanonical' => $this->canonicalFieldsUpdater->canonicalizeUsername($username)));
    }

}
