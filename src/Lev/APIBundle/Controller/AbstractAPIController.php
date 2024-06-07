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
use Symfony\Component\HttpFoundation\Response;
use Pagerfanta\Pagerfanta;
use Doctrine\Common\Inflector\Inflector;
use App\Lev\APIBundle\Config\APIConfig;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use \Doctrine\Common\Collections\ArrayCollection;

/**
 * Class AbstractController
 *
 * @category Controller
 * @package  Lev\APIBundle\Controller
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 * @link     none
 */
abstract class AbstractAPIController extends AbstractController
{

    protected $classArrayType = null;

    /**
     * @var APIConfig
     */
    protected $config;

    public function __construct()
    {
        $this->config = new APIConfig;
        $this->configure($this->config);
    }

    /**
     * @inheritdoc
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @inheritdoc
     */
    protected function getClassArrayType()
    {
        if (null === $this->classArrayType) {
            $class = $this->getModelClass();
            $obj = new $class;
            if (method_exists($obj, 'toArray')) {
                $this->classArrayType = 'toArray';
            } else if (in_array('ArrayObject', class_parents($obj))) {
                $this->classArrayType = 'ArrayObject';
            } else {
                $this->classArrayType = false;
            }
        }

        return $this->classArrayType;
    }

    /**
     * @inheritdoc
     */
    protected function getValidator()
    {
        return $this->get('validator');
    }

    /**
     * @inheritdoc
     */
    protected function paginate($adapter, $maxPerPage, $currentPage)
    {
        // Pagination with Pagerfanta
        $pagerfanta = new Pagerfanta($adapter);

        if ($maxPerPage > 0) {
            $pagerfanta
                ->setMaxPerPage($maxPerPage)
                ->setCurrentPage($currentPage);
        } else {
            $pagerfanta
                ->setMaxPerPage(9999999999)
                ->setCurrentPage(1);
        }
        $results = $pagerfanta->getCurrentPageResults();

        $data = array(
            'pagination' => array(
                'paginate' => $pagerfanta->haveToPaginate(),
                'total' => $pagerfanta->getNbResults(),
                'limit' => $pagerfanta->getMaxPerPage(),
                'pages' => $pagerfanta->getNbPages(),
                'currentPage' => $pagerfanta->getCurrentPage(),
                'next' => $pagerfanta->hasNextPage() ? $pagerfanta->getNextPage() : false,
                'prev' => $pagerfanta->hasPreviousPage() ? $pagerfanta->getPreviousPage() : false,
            )
        );

        $data['results'] = array();
        foreach ($results as $item) {
            $data['results'][] = $this->prepareRecord($item);
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    protected function updateRecord($record, Request $request)
    {
        $data = $request->request->all();

        foreach ($data as $fieldname => $value) {

            $field = $this->getConfig()->getField($fieldname);

            if ($field->isSaved()) {

                if ($field->isDate()) {
                    if (is_object($value)) {
                        $tmp = get_object_vars($value);
                        $value = $tmp['date'];
                    }
                    $value = null === $value ? null : new \DateTime($value);
                } else if ($field->isTime()) {
                    $value = is_object($value) ? $value->date : "1900-01-01 {$value}";
                    $value = null === $value ? null : new \DateTime($value);
                } else if ($field->isObject()) {
                    if ($value === -1) {
                        $value = null;
                    }
                    if (null !== $value) {
                        $class = $field->getObjectClassname();
                        if (is_object($value)) {
                            $value = get_object_vars($value);
                        }
                        if (is_array($value)) {
                            $keys = array('id', '_id', '$id');
                            foreach ($keys as $key) {
                                if (array_key_exists($key, $value)) {
                                    $value = $value[$key];
                                    break;
                                }
                            }
                        }
                        if (!empty($value)) {
                            $value = $this->getManager()
                                ->getRepository($class)->find($value);
                        } else {
                            $value = null;
                        }
                    }
                } else if ($field->isCollection()) {
                    if (null !== $value) {
                        $class = $field->getCollectionClassname();
                        $newValue = new ArrayCollection();
                        foreach($value as $v) {
                            if (is_object($v)) {
                                $v = get_object_vars($v);
                            }
                            if (is_array($v)) {
                                $keys = array('id', '_id', '$id');
                                foreach ($keys as $key) {
                                    $v = array_key_exists($key, $v)
                                        ? $v[$key]
                                        : $v;
                                }
                            }
                            if (!empty($v)) {
                                $v = $this->getManager()
                                    ->getRepository($class)->find($v);
                                $newValue->add($v);
                            }
                        }
                        $value = $newValue;
                    }
                } else if ($field->isCurrency()) {
                    $value = str_replace('.', '', $value);
                    $value = str_replace(',', '', $value);
                    $value = (int)$value;
                }

                $method = Inflector::camelize('set_' . $fieldname);
                $record->$method($value);
            }
        }

        return $record;
    }

    /**
     * @inheritdoc
     * @todo ArrayObject option not working because of $_id <> $id
     */
    public function prepareRecord($record)
    {
        $data = array();

        switch ($this->getClassArrayType()) {
            case 'ArrayObject':
                $record = (array)$record;
                break;
            default:
            case 'toArray':
                $method = $this->getClassArrayType();
                $record = $record->$method();
                break;
        }

        if (is_array($record)) {
            foreach ($this->config->getFields() as $field) {
                if ($field->isExposed() && array_key_exists($field->getName(), $record)) {
                    $data[$field->getName()] = $record[$field->getName()];
                }
            }
            return $data;
        }

        foreach ($this->config->getFields() as $field) {
            if ($field->isExposed()) {
                $method = Inflector::camelize('get_' . $field->getName());
                if (!method_exists($record, $method)) {
                    $method = Inflector::camelize('is_' . $field->getName());
                }
                $data[$field->getName()] = $record->$method();
                if ($data[$field->getName()] === true) {
                    $data[$field->getName()] = 1;
                }
                if ($data[$field->getName()] === false) {
                    $data[$field->getName()] = 0;
                }
            }
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function cgetAction(Request $request)
    {

        $this->denyAccessUnlessGranted(
            $this->getRoleName('VIEW')
            , null
            , 'You don\'t have access to this page'
        );

        try {

            if (!$request->isMethod('GET')) {
                throw new \Exception(
                    'Method not allowed (expected GET)',
                    Response::HTTP_METHOD_NOT_ALLOWED
                );
            }

            $currentPage = $request->query->get('page', 1);
            $maxPerPage = $request->query->get(
                'limit', $this->config->getQueryMaxPerPage()
            );
            $qb = $this->getQueryBuilder($request);

            $sort = $request->query->get('sort', false);
            $sort = $sort ? json_decode($sort) : $this->config->getQuerySort();
            $sort = is_object($sort) ? get_object_vars($sort) : $sort;
            $sort = empty($sort) ? $this->config->getQuerySort() : $sort;
            if (is_array($sort)) {
                $qb = $this->sortQueryBuilder($qb, $sort);
            }

            $filter = $request->query->get('filter', array());
            if (is_string($filter) && strlen($filter) > 0) {
                $filter = json_decode($filter, true);
                if (null === $filter) {
                    throw new \Exception(
                        'Malformed filter - invalid JSON'
                        , self::ERR_FILTER
                    );
                }
            }

            if (count($filter) > 0) {
                $qb = $this->getQueryFilterSearch()->filter($this->getConfig(), $qb, $filter);
            }

            $search = $request->query->get('search', null);
            if (is_string($search) && strlen($search) !== 0) {
                $qb = $this->getQueryFilterSearch()->search($this->getConfig(), $qb, $search);
            }

            // Pagination with Pagerfanta
            $adapter = $this->getPaginatorAdapter($qb);
            $data = $this->paginate($adapter, $maxPerPage, $currentPage);

        } catch (\Exception $e) {
            return $this->renderError($e);
        }

        return $this->renderJsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @inheritdoc
     */
    public function getAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted(
            $this->getRoleName('VIEW')
            , null
            , 'You don\'t have access to this page'
        );

        try {
            if (!$request->isMethod('GET')) {
                throw new \Exception(
                    'Method not allowed (expected GET)'
                    , Response::HTTP_METHOD_NOT_ALLOWED
                );
            }

            $record = $this->getOneByRequest($request, $id);
            if (null === $record) {
                throw new \Exception(
                    'Record not found'
                    , self::ERR_RECORD_NOT_FOUND
                );
            }
            $data = $this->prepareRecord($record);
        } catch (\Exception $e) {
            return $this->renderError($e);
        }

        return $this->renderJsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @inheritdoc
     */
    public function putAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted(
            $this->getRoleName('UPDATE')
            , null
            , 'You don\'t have access to this page'
        );

        try {
            $record = $this->getOneByRequest($request, $id, true);

            if (!$record) {
                throw new \Exception(
                    'Record not found'
                    , self::ERR_RECORD_NOT_FOUND
                );
            }

            if (!$request->isMethod('PUT')) {
                throw new \Exception(
                    'Method not allowed (expected PUT)'
                    , Response::HTTP_METHOD_NOT_ALLOWED
                );
            }

            $record = $this->updateRecord($record, $request);
            $errors = $this->getValidator()->validate($record);

            if (count($errors) > 0) {
                return $this->renderValidationErrors($errors);
            }

            $this->getManager()->persist($record);
            $this->getManager()->flush();
            $data = $this->prepareRecord($record);

        } catch (\Exception $e) {
            return $this->renderError($e);
        }

        return $this->renderJsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @inheritdoc
     */
    public function postAction(Request $request)
    {
        $this->denyAccessUnlessGranted(
            $this->getRoleName('CREATE')
            , null
            , 'You don\'t have access to this page'
        );

        try {

            if (!$request->isMethod('POST')) {
                throw new \Exception(
                    'Method not allowed (expected POST)'
                    , Response::HTTP_METHOD_NOT_ALLOWED
                );
            }

            $class = $this->getModelClass();
            $record = new $class;

            $record = $this->updateRecord($record, $request);
            $errors = $this->getValidator()->validate($record);

            if (count($errors) > 0) {
                return $this->renderValidationErrors($errors);
            }

            $this->getManager()->persist($record);
            $this->getManager()->flush();
            $data = $this->prepareRecord($record);

        } catch (\Exception $e) {
            return $this->renderError($e);
        }

        return $this->renderJsonResponse($data, Response::HTTP_CREATED);
    }

    /**
     * @inheritdoc
     */
    public function deleteAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted(
            $this->getRoleName('DELETE')
            , null
            , 'You don\'t have access to this page'
        );

        try {

            if (!$request->isMethod('DELETE')) {
                throw new \Exception(
                    'Method not allowed (expected DELETE)'
                    , Response::HTTP_METHOD_NOT_ALLOWED
                );
            }

            $record = $this->getOneByRequest($request, $id);

            if (null === $record) {
                throw new \Exception(
                    'Record not found'
                    , self::ERR_RECORD_NOT_FOUND
                );
            }

            $this->getManager()->remove($record);
            $this->getManager()->flush();

            $data = array(
                'message' => 'Record deleted'
            );

        } catch (\Exception $e) {
            return $this->renderError($e);
        }

        return $this->renderJsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @inheritdoc
     */
    public function uniqueAction(Request $request, $field, $value, $id = null)
    {
        $this->denyAccessUnlessGranted($this->getRoleName('view'), null, 'You don\'t have access to this');

        try {
            $field = $this->getDoctrineFieldName($field);
            $recordCount = $this->countUniqueByRequest($request, $field, $value, $id);
            $data = array(
                'isUnique' => $recordCount > 0 ? false : true
            );
        } catch (\Exception $e) {
            return $this->renderError($e);
        }

        return $this->renderJsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @inheritdoc
     */
    protected function getRoleName($type)
    {
        $roles = $this->config->getRoles();

        return array_key_exists($type, $roles) ? $roles[$type] : 'ROLE_UNKNOWN';
    }

}
