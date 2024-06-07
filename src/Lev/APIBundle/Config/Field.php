<?php


namespace App\Lev\APIBundle\Config;

/**
 * Class Field
 * @package Lev\APIBundle\Config
 */
class Field {

    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $exposed = false;

    /**
     * @var bool
     */
    protected $saved = false;

    /**
     * @var mixed
     */
    protected $filter = false;

    /**
     * @var bool
     */
    protected $searchable = false;

    /**
     * @var bool
     */
    protected $isDate = false;

    /**
     * @var bool
     */
    protected $isTime = false;

    /**
     * @var bool
     */
    protected $isFile = false;

    /**
     * @var bool
     */
    protected $isFileMulti = false;

    /**
     * @var bool|string
     */
    protected $object = false;

    /**
     * @var bool|string
     */
    protected $collection = false;

    /**
     * @var bool
     */
    protected $isCurrency = false;

    public function __construct(array $config = array())
    {
        $setters = array(
            'name'        => 'setName',
            'exposed'     => 'setExposed',
            'expose'      => 'setExposed',
            'saved'       => 'setSaved',
            'save'        => 'setSaved',
            'filter'      => 'setFilter',
            'search'      => 'setSearchable',
            'searchable'  => 'setSearchable',
            'date'        => 'setDate',
            'isdate'      => 'setDate',
            'isDate'      => 'setDate',
            'time'        => 'setTime',
            'istime'      => 'setTime',
            'isTime'      => 'setTime',
            'file'        => 'setFile',
            'isFile'      => 'setFile',
            'isfile'      => 'setFile',
            'fileMulti'   => 'setFileMulti',
            'isFileMulti' => 'setFileMulti',
            'isFilemulti' => 'setFileMulti',
            'filemulti'   => 'setFileMulti',
            'isfilemulti' => 'setFileMulti',
            'object'      => 'setObject',
            'collection'  => 'setCollection',
            'currency'    => 'setCurrency',
            'iscurrency'  => 'setCurrency',
            'isCurrency'  => 'setCurrency',
        );

        foreach ($config as $key => $value) {
            if (array_key_exists($key, $setters)) {
                $method = $setters[$key];
                $this->$method($value);
            }
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isExposed()
    {
        return $this->exposed;
    }

    /**
     * @param boolean $exposed
     *
     * @return $this
     */
    public function setExposed($exposed)
    {
        $this->exposed = (bool) $exposed;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isSaved()
    {
        return $this->saved;
    }

    /**
     * @param boolean $saved
     *
     * @return $this
     */
    public function setSaved($saved)
    {
        $this->saved = (bool) $saved;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @return boolean
     */
    public function isFilterable()
    {
        return (bool) $this->filter;
    }

    /**
     * @param mixed $filter
     *
     * @return $this
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isSearchable()
    {
        return $this->searchable;
    }

    /**
     * @param boolean $searchable
     *
     * @return $this
     */
    public function setSearchable($searchable)
    {
        $this->searchable = $searchable;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isDate()
    {
        return $this->isDate;
    }

    /**
     * @param boolean $isDate
     *
     * @return $this
     */
    public function setDate($isDate)
    {
        $this->isDate = $isDate;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isTime()
    {
        return $this->isTime;
    }

    /**
     * @param boolean $isTime
     *
     * @return $this
     */
    public function setTime($isTime)
    {
        $this->isTime = $isTime;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isFile()
    {
        return $this->isFile;
    }

    /**
     * @param boolean $isFile
     *
     * @return $this
     */
    public function setFile($isFile)
    {
        $this->isFile = $isFile;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isFileMulti()
    {
        return $this->isFileMulti;
    }

    /**
     * @param boolean $isFileMulti
     *
     * @return $this
     */
    public function setFileMulti($isFileMulti)
    {
        $this->isFileMulti = $isFileMulti;

        return $this;
    }

    /**
     * @return bool
     */
    public function isObject()
    {
        return (bool) $this->object;
    }

    /**
     * @return string
     */
    public function getObjectClassname()
    {
        return (string) $this->object;
    }

    /**
     * @param bool|string $object
     *
     * @return $this
     */
    public function setObject($object)
    {
        $this->object = $object;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCollection()
    {
        return (bool) $this->collection;
    }

    /**
     * @return string
     */
    public function getCollectionClassname()
    {
        return (string) $this->collection;
    }

    /**
     * @param bool|string $object
     *
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isCurrency()
    {
        return $this->isCurrency;
    }

    /**
     * @param boolean $isCurrency
     *
     * @return $this
     */
    public function setCurrency($isCurrency)
    {
        $this->isCurrency = $isCurrency;

        return $this;
    }



}