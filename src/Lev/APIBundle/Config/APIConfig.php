<?php
namespace App\Lev\APIBundle\Config;

use Symfony\Component\HttpFoundation\Response;
use App\Lev\APIBundle\Controller\AbstractAPIController;
/**
 * Class APIConfig
 *
 * @package Lev\APIBundle\Config
 */
class APIConfig
{

    /**
     * @var array
     */
    protected $fields = array();

    /**
     * @var array
     */
    protected $options = array(
        'roles' => array(
            'VIEW'   => 'ROLE_ADMIN',
            'CREATE' => 'ROLE_ADMIN',
            'UPDATE' => 'ROLE_ADMIN',
            'DELETE' => 'ROLE_ADMIN',
        ),
        'query' => array(
            'sort'       => array('id' => 'ASC'),
            'maxPerPage' => 20
        )
    );

    /**
     * @param Field $field
     * @return $this
     */
    public function addField(Field $field)
    {
        $this->fields[$field->getName()] = $field;

        return $this;
    }

    /**
     * @param array $fieldConfig
     * @return $this
     */
    public function addFieldFromArray(array $fieldConfig)
    {
        $field = new Field($fieldConfig);
        $this->fields[$field->getName()] = $field;

        return $this;
    }

    /**
     * @param array $fieldConfig
     * @return $this
     */
    public function setFieldsFromArray(array $fields)
    {
        foreach($fields as $fieldConfig) {
            $this->addFieldFromArray($fieldConfig);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     *
     * @return $this
     */
    public function setFields(array $fields)
    {
        foreach($fields as $field) {
            $this->addField($field);
        }

        return $this;
    }

    /**
     * Get Field by Fieldname
     *
     * @return Field
     * @throws \Exception
     */
    public function getField($fieldname)
    {
        if (!array_key_exists($fieldname, $this->fields)) {
            throw new \Exception(
                "'{$fieldname}' field is not configured on the API Controller.",
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->fields[$fieldname];
    }

    /**
     * Get Field by Fieldname
     *
     * @return $this
     * @throws \Exception
     */
    public function removeField($fieldname)
    {
        if (!array_key_exists($fieldname, $this->fields)) {
            throw new \Exception(
                "'{$fieldname}' field is not configured on the API Controller.",
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        unset($this->fields[$fieldname]);

        return $this;
    }


    /**
     * @param array $roles
     * @return $this
     */
    public function setRoles(array $roles)
    {
        $this->options['roles'] = array();
        foreach ($roles as $basename => $role) {
            $this->addRole($basename, $role);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->options['roles'];
    }

    /**
     * @param array $fields
     * @return $this
     */
    public function setExposed(array $fields)
    {
        foreach ($this->fields as $field) {
            $field->setExposed(in_array($field->getName(), $fields));
            $this->addField($field);
        }

        return $this;
    }

    /**
     * @param array $fields
     * @return $this
     */
    public function getExposed()
    {
        $fields = array();

        foreach ($this->fields as $field) {
            if ($field->isExposed()) {
                $fields[] = $field->getName();
            }
        }

        return $fields;
    }

    /**
     * @param array $fields
     * @return $this
     */
    public function setSaved(array $fields)
    {
        foreach ($this->fields as $field) {
            $field->setSaved(in_array($field->getName(), $fields));
            $this->addField($field);
        }

        return $this;
    }

    /**
     * @param array $fields
     * @return $this
     */
    public function getSaved()
    {
        $fields = array();

        foreach ($this->fields as $field) {
            if ($field->isSaved()) {
                $fields[] = $field->getName();
            }
        }

        return $fields;
    }

    /**
     * @param $basename
     * @param $role
     * @return $this
     */
    public function addRole($basename, $role)
    {
        $this->options['roles'][$basename] = $role;

        return $this;
    }

    /**
     * @param $module
     * @return $this
     */
    public function setDefaultRoles($module)
    {
        $module = strtoupper($module);
        $roles  = array(
            'VIEW'   => "ROLE_{$module}_VIEW",
            'CREATE' => "ROLE_{$module}_CREATE",
            'UPDATE' => "ROLE_{$module}_UPDATE",
            'DELETE' => "ROLE_{$module}_DELETE",
        );
        $this->setRoles($roles);

        return $this;
    }

    /**
     * @param array $sort
     * @return $this
     */
    public function setQuerySort(array $sort)
    {
        $this->options['query']['sort'] = $sort;

        return $this;
    }

    /**
     * @return array
     */
    public function getQuerySort()
    {
        return $this->options['query']['sort'];
    }

    /**
     * @param $maxPerPage
     * @return $this
     */
    public function setQueryMaxPerPage($maxPerPage)
    {
        $this->options['query']['maxPerPage'] = $maxPerPage;

        return $this;
    }

    /**
     * @return integer
     */
    public function getQueryMaxPerPage()
    {
        return $this->options['query']['maxPerPage'];
    }

    /**
     * Get Filterable Fields
     *
     * - An array of \Lev\APIBundle\Config\Fields
     *
     * @return array
     */
    public function getFilterableFields()
    {
        $fields = array();

        foreach ($this->fields as $field) {
            if ($field->getFilter()) {
                $fields[] = $field;
            }
        }

        return $fields;
    }

    /**
     * Get Searchable Fields
     *
     * - An array of fieldnames
     *
     * @return array
     */
    public function getSearchableFields()
    {
        $fields = array();

        foreach ($this->fields as $field) {
            if ($field->isSearchable()) {
                $fields[] = $field->getName();
            }
        }

        return $fields;
    }

    public function checkFilterableFields(array $fieldnames)
    {
        foreach($fieldnames as $fieldname) {
            $field = $this->getField($fieldname);
            if (!$field->isFilterable()) {
                throw new \Exception(
                    "'{$fieldname}' is not filterable.",
                    AbstractAPIController::ERR_FILTER
                );

            }
        }
    }

    // public function osama(req)
    // {
    //     $em = $this->getDoctrine()->getManager();

    //     $RAW_QUERY = 'SELECT * FROM customers where customers.isdeleted = 0;';
        
    //     $statement = $em->getConnection()->prepare($RAW_QUERY);
    //     $statement->execute();

    //     $result = $statement->fetchAll();

    //     return $result;
    // }


}