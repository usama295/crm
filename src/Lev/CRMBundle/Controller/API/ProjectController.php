<?php

namespace App\Lev\CRMBundle\Controller\API;

use FOS\RestBundle\Controller\Annotations\RouteResource;
use App\Lev\APIBundle\Config\APIConfig;
use App\Lev\CRMBundle\Controller\AbstractAPICRMController;
use App\Lev\CRMBundle\Entity\ProjectActivity;
use App\Lev\CRMBundle\Entity\ProjectEstimate;
use Symfony\Component\HttpFoundation\Request;
use Swagger\Annotations as SWG;

/**
 * @RouteResource("Project")
 */
class ProjectController extends AbstractAPICRMController
{

    /**
     * @inheritdoc
     */
    public function getModelClass()
    {
        return '\App\Lev\CRMBundle\Entity\Project';
    }

    /**
     * @inheritdoc
     */
    public function configure(APIConfig $config)
    {
        $fields = array(
            array('name' => 'id', 'exposed' => true, 'saved' => false),
            array('name' => 'customer.primaryFirstName', 'exposed' => false, 'saved' => false, 'search' => true),
            array('name' => 'customer.primaryLastName', 'exposed' => false, 'saved' => false, 'search' => true),
            array('name' => 'customer.phone1Number', 'exposed' => false, 'saved' => false, 'search' => true),
            array('name' => 'customer.phone2Number', 'exposed' => false, 'saved' => false, 'search' => true),
            array('name' => 'customer.phone3Number', 'exposed' => false, 'saved' => false, 'search' => true),
            array('name' => 'customer', 'exposed' => true, 'saved' => true,
                'filter' => 'objectid', 'object' => '\App\Lev\CRMBundle\Entity\Customer', 'filter' => 'customer_fullname'),
            array('name' => 'office', 'exposed' => true, 'saved' => false,
                'filter' => 'objectid', 'object' => '\App\Lev\CRMBundle\Entity\Office'),
            array('name' => 'sale', 'exposed' => true, 'saved' => true,
                'filter' => 'objectid', 'object' => '\App\Lev\CRMBundle\Entity\Sale'),
            array('name' => 'jobManager', 'exposed' => true, 'saved' => true,
                'filter' => 'objectid', 'object' => '\App\Lev\CRMBundle\Entity\Staff'),
            array('name' => 'jobCategory', 'exposed' => true, 'saved' => true),
            array('name' => 'comments', 'exposed' => true, 'saved' => true),
            array('name' => 'status', 'exposed' => true, 'saved' => true),
            array('name' => 'createdAt', 'exposed' => true, 'saved' => false, 'filter' => 'daterange', 'date' => true),
            array('name' => 'createdBy', 'exposed' => true, 'saved' => false,
                'filter' => 'objectid', 'object' => '\App\Lev\CRMBundle\Entity\Staff'),
            array('name' => 'activities', 'exposed' => true, 'saved' => false),
            array('name' => 'enabledActivities', 'exposed' => true, 'saved' => true),
            // array('name' => 'histories', 'exposed' => true, 'saved' => false),
            array('name' => 'historyNote', 'exposed' => true, 'saved' => false),
            array('name' => 'projectEstimate', 'exposed' => true, 'saved' => false),
            array('name' => 'attachments', 'exposed' => true, 'saved' => false),
            array('name' => 'deletedAttachments', 'exposed' => false, 'saved' => false),
            array('name' => 'insertedAttachments', 'exposed' => false, 'saved' => false),
            array('name' => 'startDate', 'exposed' => true, 'saved' => false, 'filter' => 'daterange', 'date' => true),
            array('name' => 'endDate', 'exposed' => true, 'saved' => false, 'filter' => 'daterange', 'date' => true),
            array('name' => 'installDate', 'exposed' => true, 'saved' => false, 'filter' => 'daterange', 'date' => true),
            array('name' => 'completedAt', 'exposed' => true, 'saved' => false, 'filter' => 'daterange', 'date' => true),
        );

        $config->setQueryMaxPerPage(20)
            ->setFieldsFromArray($fields)
            ->setDefaultRoles('project')
            ->setQuerySort(array(
                'createdAt' => 'DESC'
            ));
    }

    /**
     * @inheritdoc
     */
    public function getQueryBuilder(Request $request)
    {
        $qb = parent::getQueryBuilder($request);
        $qb->innerJoin('e.sale', 'sale')
            ->innerJoin('e.customer', 'customer')
            ->leftJoin('e.activities', 'activities')
            ->leftJoin('activities.assignee', 'assignee')
            ->leftJoin('e.estimates', 'estimates')
            ->leftJoin('estimates.institution', 'institution')
            ->leftJoin('estimates.contractor', 'contractor')
            ;

        return $qb;
    }

    /**
     * @inheritdoc
     * @SWG\Tag(name="Project")
     * @SWG\Response(
     *     response=200,
     *     description="update project")
     */
    protected function updateRecord($record, Request $request)
    {
        /** @var $record \App\Lev\CRMBundle\Entity\Project */
        $record = parent::updateRecord($record, $request);
        $data = $request->request->all();

        if (null === $record->getSale()->getId()) {
            throw new \Exception(
                'Sale invalid to create Project: id ' . print_r($data['sale'], true),
                self::ERR_VALIDATE
            );
        }

        $record->setCustomer($record->getSale()->getCustomer());

        $activitiesToRemove = array();
        foreach ($record->getActivities() as $activity) {
            $activitiesToRemove[$activity->getId()] = $activity;
        }

        if (array_key_exists('activities', $data) && !empty($data['activities'])) {

            foreach ($data['activities'] as $act) {
                if (is_object($act)){
                    $act = get_object_vars($act);
                }
                /** @var $activity \App\Lev\CRMBundle\Entity\ProjectActivity */
                $activity = (array_key_exists('id', $act) && !empty($act['id']))
                    ? $this->getProjectActivityById($act['id'])
                    : new ProjectActivity();
                if ($activity->getId()) {
                    unset($activitiesToRemove[$activity->getId()]);
                }
                $assignee = $this->getStaffById($act['assignee']);
                $activity
                    ->setProject($record)
                    ->setName($act['name'])
                    ->setAssignee($assignee)
                    ->setComments(array_key_exists('comments', $act) ? $act['comments'] : null)
                    ->setStartDate(array_key_exists('startDate', $act) && !empty($act['startDate']) ? new \DateTime($act['startDate']) : null)
                    ->setEndDate(array_key_exists('endDate', $act) && !empty($act['endDate']) ? new \DateTime($act['endDate']) : null)
                    ->setCompletedAt(array_key_exists('completedAt', $act) && !empty($act['completedAt']) ? new \DateTime($act['completedAt']) : null)
                    ->setType($act['type']);

                if ($activity->getId()) {
                    $this->getManager()->persist($activity);
                } else {
                    $record->addActivity($activity);
                }
            }
        }
        foreach($activitiesToRemove as $activity) {
            $this->getManager()->remove($activity);
            $record->removeActivity($activity);
        }

        $projectEstimateToRemove = array();
        foreach ($record->getEstimates() as $estimate) {
            $projectEstimateToRemove[$estimate->getId()] = $estimate;
        }

        if (array_key_exists('projectEstimate', $data) && !empty($data['projectEstimate'])) {

            foreach ($data['projectEstimate'] as $est) {
                if (is_object($est)){
                    $est = get_object_vars($est);
                }
                /** @var $estimate \App\Lev\CRMBundle\Entity\ProjectEstimate */
                $estimate = (array_key_exists('id', $est) && !empty($est['id']))
                    ? $this->getProjectEstimateById($est['id'])
                    : new ProjectEstimate();
                if ($estimate->getId()) {
                    unset($projectEstimateToRemove[$estimate->getId()]);
                }
                $contractor = !empty($est['contractor']) ? $this->getContractorById($est['contractor']) : null;
                $institution = !empty($est['institution']) ? $this->getInstitutionById($est['institution']) : null;
                $estimate
                    ->setProject($record)
                    ->setContractor($contractor)
                    ->setInstitution($institution)
                    ->setProduct($est['product'])
                    ->setCost($est['cost'])
                    ->setNote($est['note']);

                if ($estimate->getId()) {
                    $this->getManager()->persist($estimate);
                } else {
                    $record->addEstimate($estimate);
                }
            }
        }
        foreach($projectEstimateToRemove as $estimate) {
            $this->getManager()->remove($estimate);
            $record->removeEstimate($estimate);
        }

        // Force PreUpdate to run
        $record->setInstallDate(new \DateTime);

        return $record;
    }

    /**
     * 
     * @SWG\Tag(name="Project")
     * @SWG\Response(
     *     response=200,
     *     description="get project details")
     * Get record by request data
     *
     * @param Request $request The Request
     * @param integer $id      The record primary key
     *
     * @return mixed
     */
    public function getOneByRequest(Request $request, $id, $hydrate = false)
    {
        $this->getConfig()->addFieldFromArray(array(
           'name' => 'histories', 'exposed' => true, 'saved' => false
        ));
        $this->classArrayType = 'toArrayWithHistory';

        $qb = $this->getRepository()->createQueryBuilder('e');
        $qb->leftJoin('e.histories', 'histories');

        return $qb->andWhere('e.id = :id')
           ->setParameter('id', $id)
           ->getQuery()
           ->getOneOrNullResult();
    }
}
