<?php

namespace App\Lev\CRMBundle\Import\DataFixture;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Console\Output\Output;

interface DataFixtureInterface {

    /**
     * Constructor
     *
     * @param Registry           $doctrine
     * @param ContainerInterface $container
     * @param Output             $output
     */
    public function __construct(Registry $doctrine, ContainerInterface $container = null, Output $output = null);

    /**
     * Set Output
     *
     * @param Output $output
     */
    public function setOutput(Output $output);

    /**
     * @inheritdoc
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer();

    /**
     * @inheritdoc
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     */
    public function getDoctrine();

    /**
     * @inheritdoc
     * @return \Doctrine\ORM\EntityManager
     */
    public function getManager();

    /**
     * Get Repository
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRepository();

    /**
     * Add Reference
     * @param  string $scope
     * @param  string $name
     * @param  mixed  $value
     * @return \Lev\CRMBundle\Import\DataFixture\DataFixtureInterface
     */
    public function addReference($scope, $name, $value);

    /**
     * Get Reference
     * @param  string $scope
     * @param  string $name
     * @return mixed
     */
    public function getReference($scope, $name);

    /**
     * Get Order
     * @return integer
     */
    public function getOrder();

    /**
     * Run data fixture
     * @return void
     */
    public function run();

    /**
     * Progress Start
     * @param  string $scope [description]
     * @param  integer $units [description]
     * @return void
     */
    public function progressStart($scope, $units = null);

    /**
     * Progress Advance
     * @return void
     */
    public function progressAdvance();

    /**
     * Progress Finish
     * @return void
     */
    public function progressFinish();

}
