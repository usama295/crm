<?php

namespace App\Lev\CRMBundle\Tests\Controller\API;

use App\Lev\CRMBundle\Test\BaseAPICRUDControllerTestCase;

class ProjectControllerTest extends BaseAPICRUDControllerTestCase
{
    public function getUri()
    {
        return '/api/v1/projects';
    }

    public function getExposedFields()
    {
        $exposedFields = array(
            'id' => 'integer',
            //'jobCategory' => 'array',
            'comments' => 'string',
            'status' => 'string',
            //'jobManager' => 'array',
            'paymentType' => 'string',
            //'discount' => 'string',
            //'sale' => 'objectid',
            //'laborCosts' => 'string',
            //'activities' => 'array',
        );
        return $exposedFields;
    }

    public function samplePostProvider()
    {
        $records = array(
            array(array(
                'jobCategory' => array('doors', 'roofing'),
                'comments'    => 'Some comments about that',
                'status'      => 'on-hold',
                'jobManager'  => 2,
                'sale'        => 3,
                'projectEstimate' => array(
                    array(
                        'type' => 'material',
                        'cost' => 200,
                        'product' => 'Windows',
                        'contractor' => 1,
                        'note' => 'Some cool note',
                    ),
                    array(
                        'type' => 'financial',
                        'cost' => 50,
                        'product' => 'Windows',
                        'contractor' => 1,
                        'institution' => 2,
                        'note' => 'Some cool note',
                    ),
                ),
                'activities'     => array(
                    array(
                        'name'  => 'Some task',
                        'type'  => 'doors',
                        'startDate'  => \DateTime::createFromFormat('Y-m-d', '2015-09-01')->format(\DateTime::ISO8601),
                        'endDate'  => \DateTime::createFromFormat('Y-m-d', '2015-09-15')->format(\DateTime::ISO8601),
                        'completedAt'  => '',
                        'comments'  => 'Everything is alright',
                        'assignee'  => 2,
                    ),
                    array(
                        'name'  => 'Another task',
                        'type'  => 'windows',
                        'startDate'  => \DateTime::createFromFormat('Y-m-d', '2015-08-22')->format(\DateTime::ISO8601),
                        'endDate'  => \DateTime::createFromFormat('Y-m-d', '2015-09-23')->format(\DateTime::ISO8601),
                        'completedAt'  => \DateTime::createFromFormat('Y-m-d', '2015-09-23')->format(\DateTime::ISO8601),
                        'comments'  => 'Everything is alright',
                        'assignee'  => 5,
                    ),
                    array(
                        'name'  => 'More task',
                        'type'  => 'gutters',
                        'startDate'  => \DateTime::createFromFormat('Y-m-d', '2015-09-05')->format(\DateTime::ISO8601),
                        'endDate'  => \DateTime::createFromFormat('Y-m-d', '2015-09-06')->format(\DateTime::ISO8601),
                        'completedAt'  => '',
                        'comments'  => 'Everything is alright',
                        'assignee'  => 3,
                    ),
                ),
            )),
        );

        return $records;
    }

    public function sampleUpdateProvider()
    {
        $records = array(
            array(array(
                'jobCategory' => array('doors', 'windows'),
                'comments'    => 'Some comments about that + and more',
                'status'      => 'completed',
                'jobManager'  => 3,
                'sale'        => 5,
                )),
        );

        return $records;
    }

    /**
     * Choose some out of the user's crew
     * @return array
     */
    public function getOneNotFound()
    {
        return array(999);
    }

}
