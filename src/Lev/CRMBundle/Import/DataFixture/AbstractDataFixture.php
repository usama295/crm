<?php

namespace App\Lev\CRMBundle\Import\DataFixture;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Helper\ProgressBar;

abstract class AbstractDataFixture implements DataFixtureInterface {

    /**
     * @var Registry
     */
    static protected $doctrine;

    /**
     * @var ContainerInterface
     */
    static protected $container;

    /**
     * @var \Symfony\Component\Console\Output\Output
     */
    static protected $output;

    /**
     * @var string
     */
    protected $entityClassName;

    /**
     * @var array
     */
    static protected $references = array();

    /**
     * @var \Symfony\Component\Console\Helper\ProgressBar
     */
    protected $bar;

    /**
     * @var array
     */
    static protected $defaults = array();

    /**
     * @inheritdoc
     */
    public function __construct(Registry $doctrine, ContainerInterface $container = null, Output $output = null)
    {
        if (! self::$doctrine) {
            self::$doctrine = $doctrine;
        }
        if (! self::$container) {
            self::$container = $container;
        }
        if (! self::$output) {
            self::$output = $output;
        }
    }

    /**
     * @inheritdoc
     */
    public function setOutput(Output $output)
    {
        self::$output = $output;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDoctrine()
    {
        return self::$doctrine;
    }

    /**
     * @inheritdoc
     */
    public function getContainer()
    {
        return self::$container;
    }

    /**
     * @inheritdoc
     */
    public function getManager()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * @inheritdoc
     */
    public function getRepository()
    {
        return $this->getManager()->getRepository();
    }

    /**
     * @inheritdoc
     */
    public function addReference($scope, $name, $value)
    {
        self::$references[$scope][$name] = $value;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getReference($scope, $id)
    {
        $id = is_object($id) ? $id->getId() : $id;
        // echo "getReference($scope, $id)" . PHP_EOL;
        $ref = array_key_exists($scope, self::$references)
            && array_key_exists($id, self::$references[$scope])
            ? self::$references[$scope][$id]
            : null;

        // echo !empty($ref) ? $ref->getId() . PHP_EOL :  "nulo para $scope, $id" . PHP_EOL;
        return $ref;

    }

    /**
     * @inheritdoc
     */
    public function progressStart($scope, $units = null)
    {
      if (null !== self::$output) {
        self::$output->writeln(PHP_EOL . PHP_EOL . 'Importing ' . strtoupper($scope));
        $this->bar = new ProgressBar(self::$output, $units);
        $this->bar->setFormat('verbose');
      }
    }

    /**
     * @inheritdoc
     */
    public function progressAdvance()
    {
      if (null !== self::$output && null !== $this->bar) {
        $this->bar->advance();
      }
    }

    /**
     * @inheritdoc
     */
    public function progressFinish()
    {
      if (null !== self::$output && null !== $this->bar) {
        $this->bar->finish();
        $this->bar = null;
      }
    }

    /**
     * Set Default
     * @param string $scope
     * @param mixed $value
     * @return \Lev\CRMBundle\Import\DataFixture\AbstractDataFixture
     */
    public function setDefault($scope, $value)
    {
        self::$defaults[$scope] = $value;
        return $this;
    }

    /**
     * Get Default
     * @param string $scope
     * @return mixed
     */
    public function getDefault($scope)
    {
        return array_key_exists($scope, self::$defaults)
            ? self::$defaults[$scope]
            : null;
    }

    /**
     * Get Default Reference
     * @param string $scope
     * @return mixed
     */
    public function getDefaultReference($scope)
    {
        if (!array_key_exists($scope, self::$references)
            && null === $this->getDefault($scope)) {
            return null;
        }

        $defaultRef = $this->getDefault($scope);
        if (is_object($defaultRef)) {
            return $defaultRef;
        }

        return $this->getReference($scope, $defaultRef);
    }

    /**
     * Get Default Reference or Default
     * @param string $scope
     * @return mixed
     */
    public function getReferenceOrDefault($scope, $id)
    {
        $ref = $this->getReference($scope, $id);
        if (null === $ref) {
            $ref = $this->getDefaultReference($scope);
        }
        return $ref;
    }

    /**
     * Get Object
     * @param string $scope
     * @param mixed  $id
     * @return mixed
     */
    public function getObject($scope, $id)
    {
        return $this->getManager()
                    ->getRepository('LevCRMBundle:' . ucfirst($scope))
                    ->findOneBy(array('id' => $id));
    }
    /**
     * Get Object Or Default
     * @param string $scope
     * @param mixed  $id
     * @return mixed
     */
    public function getObjectOrDefault($scope, $id)
    {
        $ref = $this->getReferenceOrDefault($scope, $id);
        if (is_object($ref)) {
          return $ref;
        }
        if (null !== $ref ) {
            $ref = $this->getObject($scope, $id);
        }
        return $ref;
    }

    /**
     * Get Option
     * @param string $scope
     * @param mixed  $value
     * @return mixed
     */
    public function getOption($scope, $value)
    {
        if (!array_key_exists($scope, $this->options)) {
            return null;
        }

        if (array_key_exists($value, $this->options[$scope])) {
            return $this->options[$scope][$value];
        }

        if (array_key_exists('default', $this->options[$scope])) {
            return $this->options[$scope]['default'];
        }

        return null;
    }

    /**
     * Get Object By Salesforce Id
     * @param string $scope
     * @param mixed  $id
     * @return mixed
     */
    public function getObjectBySalesforceId($scope, $id)
    {
        return $this->getManager()->getRepository('LevCRMBundle:' . ucfirst($scope))
                       ->findOneBy(array('salesforceId' => $id));
    }

    /**
     * Get Object By Salesforce Id
     * @param string $scope
     * @param mixed  $id
     * @return mixed
     */
    public function getObjectBySalesforceUserId($scope, $id)
    {
        return $this->getManager()->getRepository('LevCRMBundle:' . ucfirst($scope))
                       ->findOneBy(array('salesforceUserId' => $id));
    }


}
