<?php
namespace App\Lev\CRMBundle\Import\DataFixture;

use App\Lev\CRMBundle\Entity\Project;

class LoadProjectData extends AbstractDataFixture implements DataFixtureInterface
{

    protected $options = array(
        // "none", "on-hold", "completed", "canceled"
        'status' => array(
            ''          => null,
            'default'   => 'on-hold',
            'Active'    => 'on-hold',
            'Completed' => 'completed',
            'Canceled'  => 'canceled',
          ),
    );

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        $this->loadFromCSV();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 8;
    }

    public function loadFromCSV()
    {

        $manager = $this->getManager();
        $count = 0;
        $projects = $this->getCSV();

        $this->progressStart('projects', count($projects));
        try {
            foreach ($this->getCSV() as $item) {
                $count++;
                $createdBy = $this->getObjectBySalesforceUserId('staff', $item[10]);
                if (null === $createdBy) {
                  $createdBy = $this->getObject('staff', 1);
                }
                $jobManager = $this->getObjectBySalesforceUserId('staff', $item[37]);
                if (null === $jobManager) {
                  $jobManager = $this->getObject('staff', 1);
                }
                $sale = $this->getObjectBySalesforceId('sale', $item[60]); // ProspectId
                if (null === $sale) {
                  echo "null sale found: {$item[60]} on project id {$item[0]}";
                  continue;
                }
                $project = new Project();
                $project->setCreatedBy($createdBy);
                $project->setCreatedAt(new \DateTime($item[11]));
                $project
                    ->setSalesforceId($item[0])
                    ->setOffice($sale->getOffice())
                    // ->setComments($comments)
                    ->setStatus($this->getOption('status', $item[68]))
                    ->setSale($sale)
                    ->setCustomer($sale->getCustomer())
                    ->setJobManager($jobManager)
                    ->setJobCategory(explode(';', strtolower($item[41])))
                    ;
                $manager->persist($project);
                $count++;
                if ($count === 150) {
                  $manager->flush();
                  $manager->clear();
                  $count = 0;
                }
                $this->progressAdvance();
            }
            $manager->flush();
            $this->progressFinish();
        } catch (\Exception $e) {
           echo $e->getMessage();
           echo $e->getTraceAsString();
           exit;
        }

    }

    protected function getCSV()
    {
        $file = file(__DIR__ . '/../../../../../importData/project.csv');
        array_shift($file);
        return array_map('str_getcsv', $file);
    }

}
