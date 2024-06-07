<?php
/**
 * This file is part off Lev\APIBundle
 *
 * PHP version 5.4
 *
 * @category Controller
 * @package  Lev\APIBundle\Controller\ORM
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 * @link     none
 */

namespace App\Lev\APIBundle\Controller\ORM;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use App\Lev\APIBundle\Controller\AbstractAPIController as BaseAbstractAPIController;
use App\Lev\APIBundle\QueryFilterSearch\ORM\QueryFilterSearch;

/**
 * Class AbstractAPIController
 *
 * @category Controller
 * @package  Lev\APIBundle\Controller
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 */
abstract class AbstractAPIController extends BaseAbstractAPIController
{

    /**
     * @inheritdoc
     * @return \Doctrine\ORM\EntityManager
     */
    public function getManager()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * @inheritdoc
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRepository()
    {
        return $this->getManager()->getRepository($this->getModelClass());
    }

    /**
     * @inheritdoc
     */
    public function getPaginatorAdapter($qb)
    {
//        $qb->hydrate($this->getQueryHydrate());

        return new DoctrineORMAdapter($qb);
    }

    /**
     * @inheritdoc
     */
    public function getQueryFilterSearch()
    {
        return new QueryFilterSearch;
    }

    /**
     * Return a formated field name for Doctrine
     *
     * It permits to send related classes names to sort or filter
     * name   = e.name
     * b.name = b.name
     *
     * @param string $fieldname Field name
     *
     * @return string
     */
    public function getDoctrineFieldName($fieldname, $alias = 'e')
    {
        $fieldname = explode('.', $fieldname);
        if (count($fieldname) === 1) {
            array_unshift($fieldname, $alias);
        }

        return implode('.', $fieldname);
    }

    /**
     * Sort Querybuilder
     *
     * @param QueryBuilder $qb A query builder
     * @param string $field    The Field
     * @param string $sort     Sort order
     *
     * @return  \Doctrine\*\QueryBuilder
     */
    public function sortQueryBuilder($qb, array $sort)
    {
        foreach ($sort as $fieldname => $order) {
            $fieldname = $this->getDoctrineFieldName($fieldname);
            $qb->addOrderBy($fieldname, $order);
        }

        return $qb;
    }

    /**
     * Return base query Builder
     *
     * @param Request $request The Request
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilder(Request $request)
    {
        $qb = $this->getRepository()->createQueryBuilder('e');

        return $qb;
    }

    /**
     * Get record by request data
     *
     * @param Request $request The Request
     * @param integer $id      The record primary key
     *
     * @return mixed
     */
    public function getOneByRequest(Request $request, $id, $hydrate = false)
    {
        return $this->getQueryBuilder($request)
            ->andWhere('e.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Check if a value already exist for the field
     *
     * @param Request $request The Request
     * @param string  $field   The field name
     * @param mixed   $value   The value
     * @param mixed   $id      The record's id
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function countUniqueByRequest(Request $request, $field, $value, $id)
    {
        return $this->getQueryBuilder($request)
            ->andWhere("{$field} = :val")
            ->andWhere("e.id != :id")
            ->setParameter('val', $value)
            ->setParameter('id', $id)
            ->getQuery()
            ->get();
    }



}
