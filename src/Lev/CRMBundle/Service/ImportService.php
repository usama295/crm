<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 07/05/15
 * Time: 18:58
 */

namespace App\Lev\CRMBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Lev\CRMBundle\Import\DataFixture\DataFixtureInterface;
use App\Lev\CRMBundle\Entity\Customer;
use App\Lev\CRMBundle\Entity\Appointment;
use App\Lev\CRMBundle\Entity\Sale;
use App\Lev\CRMBundle\Entity\Project;
use App\Lev\CRMBundle\Entity\History;
use Symfony\Component\Console\Output\Output;

/**
 * History Service
 *
 * @category Service
 * @package  Lev\CRMBundle\Service
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 * @link     none
 */
class ImportService
{

    /**
     * @var Registry
     */
    protected $doctrine;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var array
     */
    protected $fixtures = array();

    /**
     * @var \Symfony\Component\Console\Output\Output
     */
    protected $output;

    /**
     * Constructor
     *
     * @param Registry $doctrine
     */
    public function __construct(Registry $doctrine, ContainerInterface $container)
    {
        $this->doctrine  = $doctrine;
        $this->container = $container;
    }

    /**
     * Set Output
     *
     * @param Output $output
     * @return \Lev\CRMBundle\Service\ImportService
     */
    public function setOutput(Output $output)
    {
        $this->output = $output;
        return $this;
    }

    /**
     * Load Fixtures
     * @param  array  $fixtures
     * @return \Lev\CRMBundle\Service\ImportService
     */
    public function loadFixtures(array $fixtures)
    {
        foreach ($fixtures as $fixture) {
            $this->loadFixture(new $fixture($this->doctrine, $this->container, $this->output));
        }

        return $this;
    }

    /**
     * Load Fixture
     * @param  DataFixtureInterface $fixture [description]
     * @return \Lev\CRMBundle\Service\ImportService
     */
    public function loadFixture(DataFixtureInterface $fixture)
    {
        if (array_key_exists($fixture->getOrder(), $this->fixtures)) {
            throw new \Exception(
                "Order already exist for a data filter: " . $fixture->getOrder()
            );
        }
        $this->fixtures[$fixture->getOrder()] = $fixture;

        return $this;
    }

    /**
     * Run Import
     * @param \Symfony\Component\Console\Output\Output $output
     * @return void
     */
    public function run()
    {
        ksort($this->fixtures);
        foreach ($this->fixtures as $fixture) {
            $fixture->run();
        }
    }

}
