<?php
/**
* PHPCI - Continuous Integration for PHP
*
* @copyright    Copyright 2014, Block 8 Limited.
* @license      https://github.com/Block8/PHPCI/blob/master/LICENSE.md
* @link         https://www.phptesting.org/
*/

namespace PHPCI_Jasmine_Node_Plugin;

use PHPCI\Builder;
use PHPCI\Model\Build;
use PHPCI\Helper\Lang;
use PHPCI\Plugin\Util\TapParser;

/**
* Jasmine plugin, runs Jasmine tests within a project.
* @package PHPCI\Plugin
*/
class Jasmine_Node implements \PHPCI\Plugin
{
  private $executable;
  private $directory;
  private $log;

  /**
  * Set up the plugin, configure options, etc.
  * @param Builder $phpci
  * @param Build $build
  * @param array $options
  */
  public function __construct(Builder $phpci, Build $build, array $options = array())
  {
    $this->phpci = $phpci;
    $this->build = $build;

    if (isset($options['executable'])) {
      $this->executable = $options['executable'];
    }
    if (isset($options['directory'])) {
      $this->directory = $options['directory'];
    }
    if (isset($options['log'])) {
      $this->log = true;
    }
  }

  /**
  * Run the Jasmine plugin.
  * @return bool
  */
  public function execute()
  {
    if(!$this->executable) {
      $this->phpci->logFailure(Lang::get('could_not_find', 'Jasmine'));
      return false;
    }
    if(!$this->directory) {
      $this->phpci->logFailure(Lang::get('invalid_command'));
      return false;
    }

    $this->phpci->logExecOutput(false);

    $cmd = escapeshellarg($this->executable) . " -noColor --verbose";

    $cmd .= " " . escapeshellarg($this->phpci->buildPath . $this->directory);
    $this->phpci->executeCommand($cmd);
    $output = $this->phpci->getLastOutput();

    if($this->log)
      $this->phpci->log($output);

    $matches = array();
    preg_match('~(\d+) test.*?(\d+) failure~', $output, $matches);
    $specs = $matches[1];
    $failures = $matches[2];

    if ($specs == 0) {
      $this->phpci->logFailure(Lang::get('no_tests_performed'));
      return false;
    } elseif ($failures > 0) {
      if(!$this->log)
        $this->phpci->logFailure($output);

      return false;
    } else {
      return true;
    }
  }
}
