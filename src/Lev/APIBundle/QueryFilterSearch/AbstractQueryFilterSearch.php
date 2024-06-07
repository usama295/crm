<?php
namespace App\Lev\APIBundle\QueryFilterSearch;

use App\Lev\APIBundle\Config\APIConfig;
use App\Lev\APIBundle\Controller\AbstractAPIController as Controller;

/**
 * Class AbstractQueryFilterSearch
 *
 * @category QueryFilterSearch
 * @package Lev\APIBundle\QueryFilter
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 */
abstract class AbstractQueryFilterSearch implements QueryFilterSearchInterface
{
    /**
     * @inheritdoc
     *
     * @return QueryBuilder
     * @throws \Exception
     */
    public function filter(APIConfig $config, $qb, array $filter = array())
    {
        $config->checkFilterableFields(array_keys($filter));

        if (count($filter) > 0) {
            foreach ($config->getFilterableFields() as $field) {

                $fieldname = $field->getName();

                // Field not allowed to filter, pass
                if (!in_array($fieldname, array_keys($filter))) {
                    continue;
                }

                // Value is null, pass
                if ((empty($filter[$fieldname]) && $filter[$fieldname] !== "0")
                    && $filter[$fieldname] !== 0 && $filter[$fieldname] !== (float) 0
                ) {
                    continue;
                }

                $value = $filter[$fieldname];

                if (is_array($field->getFilter())) {
                    if (!array_key_exists('type', $field->getFilter())) {
                        throw new \Exception(
                            "Filter ERROR: {$fieldname} filter config is an array, but no 'type' found"
                            , Controller::ERR_FILTER
                        );
                    }
                    if (!array_key_exists('config', $field->getFilter())) {
                        throw new \Exception(
                            "Filter ERROR: {$fieldname} filter config is an array, but no 'config' found"
                            , Controller::ERR_FILTER
                        );
                    }
                    $type = $field->getFilter();
                    $type = $type['type'];
                } else {
                    $type = $field->getFilter();
                }

                switch ($type) {

                    case 'daterange':
                        $qb = $this->filterDateRange($qb, $config, $field, $value);
                        break;

                    case 'daterange_min':
                    case 'daterangemin':
                    case 'dateRangeMin':
                    case 'daterangeMin':
                        $qb = $this->filterDateRangeMin($qb, $config, $field, $value);
                        break;

                    case 'daterange_max':
                    case 'daterangemax':
                    case 'dateRangeMax':
                    case 'daterangeMax':
                        $qb = $this->filterDateRangeMax($qb, $config, $field, $value);
                        break;

                    case 'date':
                        $qb = $this->filterDate($qb, $config, $field, $value);
                        break;

                    case 'calendarrange':
                    case 'calendarRange':
                    case 'calendar_range':
                        $qb = $this->filterCalendarRange($qb, $config, $field, $value);
                        break;

                    // Not sure...
                    case 'time':
                        $qb = $this->filterTime($qb, $config, $field, $value);
                        break;

                    case 'object_id':
                    case 'objectid':
                    case 'objectId':
                        $qb = $this->filterObjectId($qb, $config, $field, $value);
                        break;

                    case 'object_id_in':
                    case 'objectidin':
                    case 'objectid_in':
                    case 'objectIdIn':
                        $qb = $this->filterObjectIdIn($qb, $config, $field, $value);
                        break;

                    case 'boolean':
                        $qb = $this->filterBoolean($qb, $config, $field, $value);
                        break;

                    case 'integer':
                    case 'int':
                        $qb = $this->filterInteger($qb, $config, $field, $value);
                        break;

                    case 'integerin':
                    case 'integer_in':
                    case 'integerIn':
                    case 'intin':
                    case 'int_in':
                    case 'intIn':
                        $qb = $this->filterIntegerIn($qb, $config, $field, $value);
                        break;

                    case 'integerrange':
                    case 'integerRange':
                    case 'integer_range':
                    case 'intrange':
                    case 'intRange':
                    case 'int_range':
                        $qb = $this->filterIntegerRange($qb, $config, $field, $value);
                        break;

                    case 'float':
                        $qb = $this->filterInteger($qb, $config, $field, $value);
                        break;

                    case 'floatrange':
                    case 'floatRrange':
                    case 'float_range':
                        $qb = $this->filterFloatRange($qb, $config, $field, $value);
                        break;

                    case 'string':
                        $qb = $this->filterString($qb, $config, $field, $value);
                        break;

                    case 'stringsearch':
                    case 'stringSearch':
                    case 'string_search':
                        $qb = $this->filterStringSearch($qb, $config, $field, $value);
                        break;

                    case 'stringin':
                    case 'stringIn':
                    case 'string_in':
                        $qb = $this->filterStringIn($qb, $config, $field, $value);
                        break;

                    default:
                        $qb = $this->getExtraFilter($type, $qb, $config, $field, $value);
                        if (!$qb) {
                            throw new \Exception(
                                "Filter ERROR: {$fieldname} has an invalid filter type '{$type}'"
                                , Controller::ERR_FILTER
                            );
                        }
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
        return false;
    }

}
