<?php

namespace App\Lev\APIBundle\QueryFilterSearch\ORM;

use App\Lev\APIBundle\Config\APIConfig;
use App\Lev\APIBundle\Config\Field;
use Doctrine\ORM\QueryBuilder;
use App\Lev\APIBundle\QueryFilterSearch\AbstractQueryFilterSearch;
use App\Lev\APIBundle\Controller\AbstractAPIController as Controller;

/**
 * Class ORM QueryFilterSearch
 *
 * @category QueryFilterSearch
 * @package Lev\APIBundle\QueryFilter
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 */
class QueryFilterSearch extends AbstractQueryFilterSearch
{

    /**
     * @inheritdoc
     *
     * @return QueryBuilder
     */
    public function search(APIConfig $config, $qb, $search = null)
    {
        if (null !== $search && count($config->getSearchableFields()) > 0) {
            $search = explode(' ', $search);

            foreach ($search as $key => $value) {
                $args = array();
                foreach ($config->getSearchableFields() as $field) {
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

                foreach ($config->getSearchableFields() as $field) {
                    $param = str_replace('.', '', "{$field}{$key}");
                    if ($field !== 'id') {
                        $qb->setParameter($param, "%{$value}%");
                    } else {
                        $qb->setParameter($param, $value);
                    }
                }
            }
        }

        return $qb;
    }

    /**
     * @inheritdoc
     *
     * @return QueryBuilder
     */
    public function filterDateRange($qb, APIConfig $config, Field $field, $value)
    {
        $param             = str_replace('.', '', $field->getName());
        $fieldname         = $field->getName();
        $doctrineFieldname = $this->getDoctrineFieldName($fieldname);

        if (!is_array($value)) {
            throw new \Exception(
                "Filter ERROR: {$fieldname} filter is daterange but value is not array { \"{$fieldname}\": {\"min\": ... , \"max\": ...}}"
                , Controller::ERR_FILTER
            );
        }

        if (!(array_key_exists('min', $value) && array_key_exists('max', $value)) && count($value) !== 2) {
            throw new \Exception(
                "Filter ERROR: {$fieldname} filter is daterange but 'min'/'max' is missing on value { \"{$fieldname}\": {\"min\": ... , \"max\": ...}} or an array [min, max]"
                , Controller::ERR_FILTER
            );
        }

        if (!(array_key_exists('min', $value) && array_key_exists('max', $value)) && count($value) === 2) {
            $value = array(
                'min' => $value[0],
                'max' => $value[1],
            );
        }

        if (!empty($value['min'])) {
            $qb->andWhere("{$doctrineFieldname} >= :{$param}min");
            $valueMin = new \DateTime($value['min']);
            $valueMin->setTime(0, 0, 0);
            $qb->setParameter("{$param}min", $valueMin);
        }

        if (!empty($value['max'])) {
            $qb->andWhere("{$doctrineFieldname} <= :{$param}max");
            $valueMax = new \DateTime($value['max']);
            $valueMax->setTime(23, 59, 59);
            $qb->setParameter("{$param}max", $valueMax);
        }
        return $qb;
    }

    /**
     * @inheritdoc
     *
     * @return QueryBuilder
     */
    public function filterDateRangeMin($qb, APIConfig $config, Field $field, $value)
    {
        $fieldname         = $field->getName();
        $doctrineFieldname = $this->getDoctrineFieldName($fieldname);

        $qb->andWhere("{$doctrineFieldname} >= :{$param}");
        $value = new \DateTime($value);
        $value->setTime(0, 0, 0);
        $qb->setParameter($param, "{$value}");

        return $qb;
    }

    /**
     * @inheritdoc
     *
     * @return QueryBuilder
     */
    public function filterDateRangeMax($qb, APIConfig $config, Field $field, $value)
    {
        $fieldname         = $field->getName();
        $doctrineFieldname = $this->getDoctrineFieldName($fieldname);

        $qb->andWhere("{$doctrineFieldname} <= :{$param}");
        $value = new \DateTime($value);
        $value->setTime(23, 59, 59);
        $qb->setParameter($param, "{$value}");

        return $qb;
    }

    /**
     * @inheritdoc
     *
     * @return QueryBuilder
     */
    public function filterDate($qb, APIConfig $config, Field $field, $value)
    {
        $fieldname         = $field->getName();
        $doctrineFieldname = $this->getDoctrineFieldName($fieldname);

        $qb->andWhere("{$doctrineFieldname} = :{$param}");
        $value = new \DateTime($value);
        $qb->setParameter($param, "{$value}");

        return $qb;
    }

    /**
     * @inheritdoc
     *
     * @return QueryBuilder
     */
    public function filterCalendarRange($qb, APIConfig $config, Field $field, $value)
    {
        $fieldname         = $field->getName();
        $doctrineFieldname = $this->getDoctrineFieldName($fieldname);

        $fieldFilter = $field->getFilter();

        if (!array_key_exists('config', $fieldFilter)) {
            throw new \Exception(
                "Filter ERROR: {$fieldname} filter config is an array, but no 'config' found"
                , 500
            );
        }

        $fieldConfig = $fieldFilter['config'];

        if (!array_key_exists('startField', $fieldConfig) || !array_key_exists('startField', $fieldConfig)) {
            throw new \Exception(
                "Filter ERROR: {$fieldname} filter is calendarrange but 'startField'/'endField' not found on config"
                , 500
            );
        }

        $startField = $this->getDoctrineFieldName($startField);
        $endField   = $this->getDoctrineFieldName($endField);

        $valueFrom = new \DateTime($value['from']);
        $valueFrom->setTime(0, 0, 0);
        $valueTo   = new \DateTime($value['to']);
        $valueTo->setTime(23, 59, 59);
        $qb->andWhere(
            $qb->expr()->orX(
                $qb->expr()->andX(
                    $qb->expr()->gte($startField, ":{$param}to"),
                    $qb->expr()->lte($startField, "{$param}from")
                ),
                $qb->expr()->andX(
                    $qb->expr()->gte($endField, ":{$param}to"),
                    $qb->expr()->lte($endField, ":{$param}from")
                ),
                $qb->expr()->andX(
                    $qb->expr()->lt($startField, ":{$param}to"),
                    $qb->expr()->gt($endField, ":{$param}from")
                )
            )
        );

        $qb->setParameter("{$param}from", "{$valueFrom}");
        $qb->setParameter("{$param}to", "{$valueTo}");

        return $qb;
    }

    /**
     * @inheritdoc
     * @todo Not tested
     *
     * @return QueryBuilder
     */
    public function filterTime($qb, APIConfig $config, Field $field, $value)
    {
        $fieldname         = $field->getName();
        $doctrineFieldname = $this->getDoctrineFieldName($fieldname);

        $qb->andWhere("{$doctrineFieldname} = :{$param}");
        $value = new \DateTime($value);
        $qb->setParameter($param, "{$value}");

        return $qb;
    }

    /**
     * @inheritdoc
     *
     * @return QueryBuilder
     */
    public function filterObjectId($qb, APIConfig $config, Field $field, $value)
    {

        $param             = str_replace('.', '', $field->getName());
        $fieldname         = $field->getName();
        $doctrineFieldname = $this->getDoctrineFieldName($fieldname);

        $qb->andWhere("{$doctrineFieldname} = :{$param}");
        $qb->setParameter($param, $value);

        return $qb;
    }

    /**
     * @inheritdoc
     *
     * @return QueryBuilder
     */
    public function filterObjectIdIn($qb, APIConfig $config, Field $field, $value)
    {
        $param             = str_replace('.', '', $field->getName());
        $fieldname         = $field->getName();
        $doctrineFieldname = $this->getDoctrineFieldName($fieldname);

        /** @var $qb \Doctrine\ORM\QueryBuilder */
        $qb->andWhere($qb->expr()->in("{$doctrineFieldname}", ":{$param}"));
        $qb->setParameter($param, $value);


        return $qb;
    }

    /**
     * @inheritdoc
     *
     * @return QueryBuilder
     */
    public function filterBoolean($qb, APIConfig $config, Field $field, $value)
    {
        $param             = str_replace('.', '', $field->getName());
        $fieldname         = $field->getName();
        $doctrineFieldname = $this->getDoctrineFieldName($fieldname);
        if ($value === '1' || $value === 1) {
            $value = true;
        }
        if ($value === '0' || $value === 0) {
            $value = false;
        }
        $qb->andWhere("{$doctrineFieldname} = :{$param}");
        $qb->setParameter($param, (bool) $value);

        return $qb;
    }

    /**
     * @inheritdoc
     *
     * @return QueryBuilder
     */
    public function filterInteger($qb, APIConfig $config, Field $field, $value)
    {
        $param             = str_replace('.', '', $field->getName());
        $fieldname         = $field->getName();
        $doctrineFieldname = $this->getDoctrineFieldName($fieldname);

        $qb->andWhere("{$doctrineFieldname} = :{$param}");
        $qb->setParameter($param, (int) $value);

        return $qb;
    }

    /**
     * @inheritdoc
     *
     * @return QueryBuilder
     */
    public function filterIntegerIn($qb, APIConfig $config, Field $field, $value)
    {
        $param             = str_replace('.', '', $field->getName());
        $fieldname         = $field->getName();
        $doctrineFieldname = $this->getDoctrineFieldName($fieldname);

        /** @var $qb \Doctrine\ORM\QueryBuilder */
        $qb->andWhere($qb->expr()->in($doctrineFieldname, ":{$param}"));
        $qb->setParameter($param, $value);

        return $qb;
    }

    /**
     * @inheritdoc
     *
     * @return QueryBuilder
     */
    public function filterIntegerRange($qb, APIConfig $config, Field $field, $value)
    {
        $param             = str_replace('.', '', $field->getName());
        $fieldname         = $field->getName();
        $doctrineFieldname = $this->getDoctrineFieldName($fieldname);

        if (!is_array($value)) {
            throw new \Exception(
                "Filter ERROR: {$fieldname} filter is intergerrange but value is not array { \"{$fieldname}\": {\"min\": ... , \"max\": ...}}"
                , Controller::ERR_FILTER
            );
        }

        if (!(array_key_exists('min', $value) && array_key_exists('max', $value))) {
            throw new \Exception(
                "Filter ERROR: {$fieldname} filter is intergerrange but 'min'/'max' is missing on value (expected  { \"{$fieldname}\": {\"min\": ... , \"max\": ...}})"
                , Controller::ERR_FILTER
            );
        }

        $qb->andWhere("{$doctrineFieldname} >= :{$param}min");
        $qb->setParameter("{$param}min", (int) $value['min']);

        $qb->andWhere("{$doctrineFieldname} <= :{$param}max");
        $qb->setParameter("{$param}max", (int) $value['max']);

        return $qb;
    }

    /**
     * @inheritdoc
     *
     * @return QueryBuilder
     */
    public function filterFloat($qb, APIConfig $config, Field $field, $value)
    {
        $param             = str_replace('.', '', $field->getName());
        $fieldname         = $field->getName();
        $doctrineFieldname = $this->getDoctrineFieldName($fieldname);

        $qb->andWhere("{$doctrineFieldname} = :{$param}");
        $qb->setParameter($param, (float) $value);

        return $qb;
    }

    /**
     * @inheritdoc
     *
     * @return QueryBuilder
     */
    public function filterFloatRange($qb, APIConfig $config, Field $field, $value)
    {
        $param             = str_replace('.', '', $field->getName());
        $fieldname         = $field->getName();
        $doctrineFieldname = $this->getDoctrineFieldName($fieldname);

        if (!is_array($value)) {
            throw new \Exception(
                "Filter ERROR: {$fieldname} filter is floatrange but value is not array { \"{$fieldname}\": {\"min\": ... , \"max\": ...}}"
                , Controller::ERR_FILTER
            );
        }

        if (!(array_key_exists('min', $value) && array_key_exists('max', $value))) {
            throw new \Exception(
                "Filter ERROR: {$fieldname} filter is floatrange but 'min'/'max' is missing on value (expected  { \"{$fieldname}\": {\"min\": ... , \"max\": ...}})"
                , Controller::ERR_FILTER
            );
        }

        $qb->andWhere("{$doctrineFieldname} >= :{$param}min");
        $qb->setParameter("{$param}min", (float) $value['min']);

        $qb->andWhere("{$doctrineFieldname} <= :{$param}max");
        $qb->setParameter("{$param}max", (float) $value['max']);

        return $qb;
    }

    /**
     * @inheritdoc
     *
     * @return QueryBuilder
     */
    public function filterString($qb, APIConfig $config, Field $field, $value)
    {
        $param             = str_replace('.', '', $field->getName());
        $fieldname         = $field->getName();
        $doctrineFieldname = $this->getDoctrineFieldName($fieldname);

        $qb->andWhere("{$doctrineFieldname} = :{$param}");
        $qb->setParameter($param, (string ) $value);

        return $qb;
    }

    /**
     * @inheritdoc
     *
     * @return QueryBuilder
     */
    public function filterStringIn($qb, APIConfig $config, Field $field, $value)
    {
        $param             = str_replace('.', '', $field->getName());
        $fieldname         = $field->getName();
        $doctrineFieldname = $this->getDoctrineFieldName($fieldname);

        $qb->andWhere($qb->expr()->in($doctrineFieldname, ":{$param}"));
        $qb->setParameter($param, $value);

        return $qb;
    }

    /**
     * @inheritdoc
     *
     * @return QueryBuilder
     */
    public function filterStringSearch($qb, APIConfig $config, Field $field, $value)
    {
        $param             = str_replace('.', '', $field->getName());
        $fieldname         = $field->getName();
        $search = explode(' ', $value);

        foreach ($search as $key => $val) {
            $paramKey          = str_replace('.', '', "{$param}{$key}");
            $doctrineFieldname = $this->getDoctrineFieldName($fieldname);
            $qb->andWhere($qb->expr()->like($doctrineFieldname, ":{$paramKey}"));
            $qb->setParameter($paramKey, "%{$value}%");
        }

        return $qb;
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
}
