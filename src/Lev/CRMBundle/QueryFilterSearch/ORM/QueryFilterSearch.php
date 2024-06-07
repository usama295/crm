<?php

namespace App\Lev\CRMBundle\QueryFilterSearch\ORM;

use App\Lev\APIBundle\Config\APIConfig;
use App\Lev\APIBundle\Config\Field;
use Doctrine\ORM\QueryBuilder;
use App\Lev\APIBundle\QueryFilterSearch\ORM\QueryFilterSearch as BaseQueryFilterSearch;
use App\Lev\APIBundle\Controller\AbstractAPIController as Controller;

/**
 * Class ORM QueryFilterSearch
 *
 * @category QueryFilterSearch
 * @package Lev\APIBundle\QueryFilter
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 */
class QueryFilterSearch extends BaseQueryFilterSearch
{

    /**
     * @inheritdoc
     *
     * @return QueryBuilder
     */
    public function filterDemoed($qb, APIConfig $config, Field $field, $value)
    {
        /** @var $qb \Doctrine\ORM\QueryBuilder */

        $statusFieldname   = $this->getDoctrineFieldName('status');
        $pitchMissReasonFieldname = $this->getDoctrineFieldName('pitchMissReason');
        $now = new \DateTime();

        if ($value === true || $value === 1 || $value === 'true' | $value === '1') {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->eq($statusFieldname, ':status'),
                $qb->expr()->isNotNull($pitchMissReasonFieldname)
            ));
        } else {
          $qb->andWhere($qb->expr()->neq($statusFieldname, ':status'))
              ->andWhere($qb->expr()->isNull($pitchMissReasonFieldname));
        }
        $qb->setParameter('status', 'sold');

        return $qb;
    }

    /**
     * @inheritdoc
     *
     * @return QueryBuilder
     */
    public function filterCustomerFullName($qb, APIConfig $config, Field $field, $value)
    {
        /** @var $qb \Doctrine\ORM\QueryBuilder */

        $searchableFields = array(
          'customer.primaryFirstName',
          'customer.primaryLastName',
        );

        $search = explode(' ', $value);

        foreach ($search as $key => $value) {
            $args = array();
            foreach ($searchableFields as $field) {
                $param             = str_replace('.', '', "{$field}{$key}");
                $doctrineFieldname = $this->getDoctrineFieldName($field);
                if ($field !== 'id') {
                    $args[] = "\$qb->expr()->like(\"{$doctrineFieldname}\","
                        . " \":{$param}\")";
                } else {
                    $args[] = "\$qb->expr()->eq(\"{$doctrineFieldname}\","
                        . " \":{$param}\")";
                }
            }

            $or = '$qb->andWhere($qb->expr()->orX('
                . implode(', ', $args) .
                '));';
            eval($or);

            foreach ($searchableFields as $field) {
                $param = str_replace('.', '', "{$field}{$key}");
                if ($field !== 'id') {
                    $qb->setParameter($param, "%{$value}%");
                } else {
                    $qb->setParameter($param, $value);
                }
            }
        }

        return $qb;
    }



    /**
     * @inheritdoc
     */
    public function getExtraFilter($type, $qb, $config, $field, $value)
    {
        switch ($type) {

            case 'demoed':
                $qb = $this->filterDemoed($qb, $config, $field, $value);
                break;

            case 'customer_name':
            case 'customerName':
            case 'customer_fullname':
            case 'customer_full_name':
            case 'customerFullName':
            case 'customerFullname':
                $qb = $this->filterCustomerFullName($qb, $config, $field, $value);
                break;

           default:
        }

        return $qb;
    }

}
