<?php

namespace App\Lev\CRMBundle\Repository\Oauth2;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * OfficeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RefreshTokenRepository extends EntityRepository
{
    public function expire(UserInterface $user)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->update('App\Lev\CRMBundle\Entity\Oauth2\RefreshToken', 'e')
            ->set('e.expiresAt', $qb->expr()->literal(time()))
            ->where('e.expiresAt > :now')
            ->andWhere('e.user = :user_id')
            ->setParameter('user_id', $user->getId())
            ->setParameter('now', time())
            ->getQuery()
            ->execute();
    }
}