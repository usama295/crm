<?php

 namespace App\Lev\CRMBundle\Traits;
 use Symfony\Component\Console\Helper\ProgressBar as SFProgressBar;

 trait ProgressBar {

     /**
      * @var \Symfony\Component\Console\Output\Output
      */
     protected $output;

     /**
      * @var \Symfony\Component\Console\Helper\ProgressBar
      */
     protected $bar;

     /**
      * @inheritdoc
      */
     public function progressStart($scope, $units = null)
     {
       if (null !== $this->output) {
         $this->output->writeln(PHP_EOL . PHP_EOL . 'Processing ' . strtoupper($scope));
         $this->bar = new SFProgressBar($this->output, $units);
         $this->bar->setFormat('verbose');
       }
     }

     /**
      * @inheritdoc
      */
     public function progressAdvance()
     {
       if (null !== $this->output && null !== $this->bar) {
         $this->bar->advance();
       }
     }

     /**
      * @inheritdoc
      */
     public function progressFinish()
     {
       if (null !== $this->output && null !== $this->bar) {
         $this->bar->finish();
         $this->bar = null;
       }
     }
 }
