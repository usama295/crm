<?php
namespace App\Lev\CRMBundle\Test\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Lev\CRMBundle\Entity\Project;
use App\Lev\CRMBundle\Entity\ProjectActivity;
use App\Lev\CRMBundle\Entity\ProjectEstimate;

class LoadProjectData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $count = 0;
        foreach ($this->getSales() as $a) {
            $count++;
            $mod   = ($count % 3) + 1;

            $project = new Project();
            foreach($a as $method => $value) {

                if ($method === 'activities') {
                    foreach ($value as $act) {
                        $assignee = $this->getReference('staff' . $act['assignee']);
                        $projectActivity = new ProjectActivity;
                        $projectActivity
                            ->setName($act['name'])
                            ->setType($act['type'])
                            ->setStartDate(!empty($act['startDate']) ? new \DateTime($act['startDate']) : null)
                            ->setEndDate(!empty($act['endDate']) ? new \DateTime($act['endDate']) : null)
                            ->setCompletedAt(!empty($act['completedAt']) ? new \DateTime($act['completedAt']) : null)
                            ->setComments($act['comments'])
                            ->setProject($project)
                            ->setAssignee($assignee);
                        $manager->persist($projectActivity);
                    }
                } else if ($method === 'projectEstimate') {
                    foreach ($value as $est) {
                        $contractor = !empty($est['contractor']) ? $this->getReference('contractor' . $est['contractor']) : null;
                        $institution = !empty($est['institution']) ? $this->getReference('institution' . $est['institution']) : null;
                        $estimate = new ProjectEstimate;
                        $estimate
                            ->setProject($project)
                            ->setContractor($contractor)
                            ->setInstitution($institution)
                            ->setProduct($est['product'])
                            ->setCost($est['cost'])
                            ->setNote($est['note']);
                        $manager->persist($estimate);
                    }
                } else if ($method === 'jobManager') {
                    $jobManager = $this->getReference('staff' . $act['assignee']);
                    $project->setJobManager($jobManager);
                } else {
                    if (!is_string($method)) {
                        throw new \Exception('NOT STRING METHOD NAME' . print_r($method, true));
                    }
                    $project->$method($value);
                }
            }
            /**
             * @var $appointment \Lev\CRMBundle\Entity\Appointment
             */
            $sale = $this->getReference('sale' . $count);
            $project
                ->setOffice($sale->getOffice())
                ->setSale($sale)
                ->setCustomer($sale->getCustomer())
            ;
            $manager->persist($sale);
            $manager->flush();
            $this->addReference('sale' . (string) $sale->getId(), $sale);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 12;
    }

    public function getProjects()
    {
        // jobCategory: "doors", "gutters", "windows", "roofing", "siding", "trim"
        // status: "none", "on-hold", "completed", "canceled"
        // projectEstimator->type: "material", "financial", "misc", "contractor"
        // activities->type: "doors", "gutters", "windows", "roofing", "siding", "trim"
        return array(
            array(
                'setJobCategory' => array('doors', 'gutters'),
                'setComments'    => 'Some comments about that',
                'setStatus'      => 'on-hold',
                'jobManager'     => 2,
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
                        'type'  => array('doors', 'roofing'),
                        'startDate'  => '2015-09-01',
                        'endDate'  => '2015-09-15',
                        'completedAt'  => '',
                        'comments'  => 'Everything is alright',
                        'assignee'  => 2,
                    ),
                    array(
                        'name'  => 'Another task',
                        'type'  => 'windows',
                        'startDate'  => '2015-08-22',
                        'endDate'  => '2015-09-23',
                        'completedAt'  => '2015-09-23',
                        'comments'  => 'Everything is alright',
                        'assignee'  => 5,
                    ),
                    array(
                        'name'  => 'More task',
                        'type'  => 'gutters',
                        'startDate'  => '2015-09-05',
                        'endDate'  => '2015-09-06',
                        'completedAt'  => '',
                        'comments'  => 'Everything is alright',
                        'assignee'  => 3,
                    ),
                ),
            ),
       );
    }

}