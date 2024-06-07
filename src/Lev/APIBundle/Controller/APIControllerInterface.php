<?php
/**
 * This file is part off Lev\APIBundle
 *
 * PHP version 5.4
 *
 * @category Controller
 * @package  Lev\APIBundle\Controller
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 * @link     none
 */

namespace App\Lev\APIBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Pagerfanta\Adapter\AdapterInterface;
use App\Lev\APIBundle\Config\APIConfig;
use Symfony\Component\Validator\ConstraintViolationListInterface;

interface APIControllerInterface
{
    /**
     * Configure API
     *
     * @param APIConfig $config
     * @return void
     */
    public function configure(APIConfig $config);

    /**
     * Return Doctrine Entity/Document Manager
     *
     * Proxy getter
     *
     * @return \Doctrine\*\*Manager
     */
    public function getManager();

    /**
     * Return  Doctrine Entity/Document Repository
     *
     * Proxy getter
     *
     * @return \Doctrine\*\*Repository
     */
    public function getRepository();

    /**
     * Return the Model Class
     *
     * @return string
     */
    public function getModelClass();

    /**
     * Return a formated field name for Doctrine
     *
     * It permits to send related classes names to sort or filter
     * name   = e.name
     * b.name = b.name
     *
     * @param string $field Field name
     *
     * @return string
     */
    public function getDoctrineFieldName($field);

    /**
     * Return a Paginator Adaptor
     *
     * @param QueryBuilder $qb A query builder
     *
     * @return \Pagerfanta\Adapter\AdapterInterface
     */
    public function getPaginatorAdapter($qb);

    /**
     * Sort Querybuilder
     *
     * @param QueryBuilder $qb   A query builder
     * @param array        $sort Sort order
     *
     * @return  \Doctrine\*\QueryBuilder
     */
    public function sortQueryBuilder($qb, array $sort);

    /**
     * Return base query Builder
     *
     * @param Request     $request  The Request
     *
     * @return \Doctrine\*\QueryBuilder
     */
    public function getQueryBuilder(Request $request);

    /**
     * Filter/Search QueryBuilder
     *
     * @param QueryBuilder $qb     A query builder
     * @param array        $filter An optional filter set
     * @param null         $search An option search set
     *
     * @return  \Lev\APIBundle\QueryFilterSearch\QueryFilterSearchInterface
     */
    public function getQueryFilterSearch();

    /**
     * Get record by request data
     *
     * @param Request $request The Request
     * @param integer $id      The record primary key
     * @param boolean $hydrate Hydrate or not
     *
     * @return mixed
     */
    public function getOneByRequest(Request $request, $id, $hydrate = false);

    /**
     * GET - Check if a value already exist for the field
     *
     * @param Request     $request  The Request
     * @param string      $field    The field name
     * @param mixed       $value    The value
     * @param mixed       $id       The record's id
     *
     * @return integer
     */
    public function countUniqueByRequest(Request $request, $field, $value, $id);

    /**
     * Get API Config
     *
     * @return APIConfig
     */
    public function getConfig();

    /**
     * Check if Class has toArray method
     *
     * @return bool|null|string
     */
    public function getClassArrayType();

    /**
     * Return Validator
     *
     * Proxy getter
     *
     * @return \Symfony\Component\Validator\ValidatorInterface
     */
    public function getValidator();

    /**
     * Method Paginate
     *
     * @param $adapter      The Paginator Adapter
     * @param $maxPerPage   Max per page
     * @param $currentPage  Current Page
     *
     * @return array
     */
    public function paginate($adapter, $maxPerPage, $currentPage);

    /**
     * Extract Exception Data to Expose to user
     *
     * @param \Exception $e     The Exception class
     * @param string     $title Error Title
     *
     * @return array
     */
    public function renderError(\Exception $e, $title = 'api');

    /**
     * Extract Exception Data to Expose to user
     *
     * @param \Exception $e The Exception class
     *
     * @return array
     */
    public function renderValidationErrors(ConstraintViolationListInterface $errors);

    /**
     * Update an record
     *
     * @param object                                    $record  The record
     *                                                           to be updated
     * @param \Symfony\Component\HttpFoundation\Request $request The request
     *
     * @return object
     */
    public function updateRecord($record, Request $request);

    /**
     * Prepare Record to return Array
     *
     * @todo ArrayObject option not working because of $_id <> $id
     *
     * @param object $record An entity record to prepare
     *
     * @return array
     */
    public function prepareRecord($record);

    /**
     * Render Json Response
     *
     * @param array   $data       Data to return
     * @param integer $statusCode HTTP Code
     *
     * @return JsonResponse
     */
    public function renderJsonResponse($data, $statusCode);

    /**
     * GET - collection of records
     *
     * @param Request $request      The Request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cgetAction(Request $request);

    /**
     * GET - one record
     *
     * @param Request $request      The Request
     * @param integer $id           The record primary key
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAction(Request $request, $id);

    /**
     * PUT - update a record
     *
     * @param Request $request      The Request
     * @param integer $id           The record primary key
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function putAction(Request $request, $id);

    /**
     * POST - create a record
     *
     * @param Request     $request  The Request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function postAction(Request $request);

    /**
     * DELETE - a record
     *
     * @param Request     $request  The Request
     * @param integer     $id       The record primary key
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, $id);

    /**
     * GET - Check if a value already exist for the field
     *
     * @Get("/unique/{field}/{value}/{id}")
     *
     * @param Request     $request  The Request
     * @param string      $field    The field name
     * @param mixed       $value    The value
     * @param mixed       $id       The record's id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function uniqueAction(Request $request, $field, $value, $id = null);

    /**
     * Get the role name by type
     *
     * @param  string $type Type Access
     * @return mixed
     */
    public function getRoleName($type);

}