<?php

namespace App\Lev\APIBundle\QueryFilterSearch;

use App\Lev\APIBundle\Config\APIConfig;
use App\Lev\APIBundle\Config\Field;


/**
 * Interface QueryFilterSearchInterface
 *
 * @category QueryFilterSearch
 * @package Lev\APIBundle\QueryFilter
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 */
interface QueryFilterSearchInterface
{
    /**
     * Filter
     *
     * @param APIConfig    $config API Config
     * @param QueryBuilder $qb     The Query Builder
     * @param array        $filter The Filter
     *
     * @return QueryBuilder
     */
    public function filter(APIConfig $config, $qb, array $filter = array());

    /**
     * Search
     *
     * @param APIConfig    $config API Config
     * @param QueryBuilder $qb     The Query Builder
     * @param mixed        $search The Search
     *
     * @return QueryBuilder
     */
    public function search(APIConfig $config, $qb, $search = null);

    /**
     * Filter Date Range
     *
     * @param QueryBuilder $qb     The Query Builder
     * @param APIConfig    $config API Config
     * @param Field        $field  The Field
     * @param mixed        $value  The Value
     *
     * @return QueryBuilder
     */
    public function filterDateRange($qb, APIConfig $config, Field $field, $value);

    /**
     * Filter Date Range Min
     *
     * @param QueryBuilder $qb     The Query Builder
     * @param APIConfig    $config API Config
     * @param Field        $field  The Field
     * @param mixed        $value  The Value
     *
     * @return QueryBuilder
     */
    public function filterDateRangeMin($qb, APIConfig $config, Field $field, $value);

    /**
     * Filter Date Range Max
     *
     * @param QueryBuilder $qb     The Query Builder
     * @param APIConfig    $config API Config
     * @param Field        $field  The Field
     * @param mixed        $value  The Value
     *
     * @return QueryBuilder
     */
    public function filterDateRangeMax($qb, APIConfig $config, Field $field, $value);

    /**
     * Filter Date
     *
     * @param QueryBuilder $qb     The Query Builder
     * @param APIConfig    $config API Config
     * @param Field        $field  The Field
     * @param mixed        $value  The Value
     *
     * @return QueryBuilder
     */
    public function filterDate($qb, APIConfig $config, Field $field, $value);

    /**
     * Filter Calendar Date Range
     *
     * @param QueryBuilder $qb     The Query Builder
     * @param APIConfig    $config API Config
     * @param Field        $field  The Field
     * @param mixed        $value  The Value
     *
     * @return QueryBuilder
     */
    public function filterCalendarRange($qb, APIConfig $config, Field $field, $value);

    /**
     * Filter Time
     *
     * @param QueryBuilder $qb     The Query Builder
     * @param APIConfig    $config API Config
     * @param Field        $field  The Field
     * @param mixed        $value  The Value
     *
     * @return QueryBuilder
     */
    public function filterTime($qb, APIConfig $config, Field $field, $value);

    /**
     * Filter ObjectId
     *
     * @param QueryBuilder $qb     The Query Builder
     * @param APIConfig    $config API Config
     * @param Field        $field  The Field
     * @param mixed        $value  The Value
     *
     * @return QueryBuilder
     */
    public function filterObjectId($qb, APIConfig $config, Field $field, $value);

    /**
     * Filter ObjectIdIn
     *
     * @param QueryBuilder $qb     The Query Builder
     * @param APIConfig    $config API Config
     * @param Field        $field  The Field
     * @param mixed        $value  The Value
     *
     * @return QueryBuilder
     */
    public function filterObjectIdIn($qb, APIConfig $config, Field $field, $value);

    /**
     * Filter Boolean
     *
     * @param QueryBuilder $qb     The Query Builder
     * @param APIConfig    $config API Config
     * @param Field        $field  The Field
     * @param mixed        $value  The Value
     *
     * @return QueryBuilder
     */
    public function filterBoolean($qb, APIConfig $config, Field $field, $value);

    /**
     * Filter Integer
     *
     * @param QueryBuilder $qb     The Query Builder
     * @param APIConfig    $config API Config
     * @param Field        $field  The Field
     * @param mixed        $value  The Value
     *
     * @return QueryBuilder
     */
    public function filterInteger($qb, APIConfig $config, Field $field, $value);

    /**
     * Filter Integer
     *
     * @param QueryBuilder $qb     The Query Builder
     * @param APIConfig    $config API Config
     * @param Field        $field  The Field
     * @param mixed        $value  The Value
     *
     * @return QueryBuilder
     */
    public function filterIntegerIn($qb, APIConfig $config, Field $field, $value);

    /**
     * Filter Integer Range
     *
     * @param QueryBuilder $qb     The Query Builder
     * @param APIConfig    $config API Config
     * @param Field        $field  The Field
     * @param mixed        $value  The Value
     *
     * @return QueryBuilder
     */
    public function filterIntegerRange($qb, APIConfig $config, Field $field, $value);

    /**
     * Filter Float
     *
     * @param QueryBuilder $qb     The Query Builder
     * @param APIConfig    $config API Config
     * @param Field        $field  The Field
     * @param mixed        $value  The Value
     *
     * @return QueryBuilder
     */
    public function filterFloat($qb, APIConfig $config, Field $field, $value);

    /**
     * Filter Float Range
     *
     * @param QueryBuilder $qb     The Query Builder
     * @param APIConfig    $config API Config
     * @param Field        $field  The Field
     * @param mixed        $value  The Value
     *
     * @return QueryBuilder
     */
    public function filterFloatRange($qb, APIConfig $config, Field $field, $value);

    /**
     * Filter String
     *
     * @param QueryBuilder $qb     The Query Builder
     * @param APIConfig    $config API Config
     * @param Field        $field  The Field
     * @param mixed        $value  The Value
     *
     * @return QueryBuilder
     */
    public function filterString($qb, APIConfig $config, Field $field, $value);

    /**
     * Filter String IN
     *
     * @param QueryBuilder $qb     The Query Builder
     * @param APIConfig    $config API Config
     * @param Field        $field  The Field
     * @param mixed        $value  The Value
     *
     * @return QueryBuilder
     */
    public function filterStringIn($qb, APIConfig $config, Field $field, $value);

    /**
     * Filter String Search (like %%)
     *
     * @param QueryBuilder $qb     The Query Builder
     * @param APIConfig    $config API Config
     * @param Field        $field  The Field
     * @param mixed        $value  The Value
     *
     * @return QueryBuilder
     */
    public function filterStringSearch($qb, APIConfig $config, Field $field, $value);

    /**
     * Extra Filters
     *
     * It's supposed to be overwriten
     *
     * @param string       $type   The Extra Type
     * @param QueryBuilder $qb     The Query Builder
     * @param APIConfig    $config API Config
     * @param Field        $field  The Field
     * @param mixed        $value  The Value
     *
     * @return QueryBuilder|bool
     */
    public function getExtraFilter($type, $qb, $config, $field, $value);
}
