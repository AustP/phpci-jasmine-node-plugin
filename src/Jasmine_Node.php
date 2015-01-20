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
    if (!$this->executable) {
      $this->phpci->logFailure(Lang::get('could_not_find', 'jasmine-node'));
      return false;
    }
    if (!$this->directory) {
      $this->phpci->logFailure(Lang::get('invalid_command'));
      return false;
    }

    $this->phpci->logExecOutput(false);

    $cmd = escapeshellarg($this->executable) . " --noColor --verbose";

    $cmd .= " " . escapeshellarg($this->phpci->buildPath . $this->directory);
    $this->phpci->executeCommand($cmd);
    $output = $this->phpci->getLastOutput();

    $this->phpci->logExecOutput(true);

    if ($this->log)
      $this->phpci->log($output);

    $output = explode('Finished in ', $output);
    $specs = $output[0];
    $metadata = preg_split("~\r\n|\n|\r~", $output[1]);

    $seconds = 'Finished in ' . $metadata[0];
    $specData = $metadata[1];

    $matches = array();
    preg_match('~(\d+) test.*?(\d+) failure~', $specData, $matches);
    $specCount = $matches[1];
    $failureCount = $matches[2];

    $data = array(
      'metadata'=>array(
        'seconds'=>$seconds,
        'specData'=>$specData
      ),
      'expectations'=>array()
    );

    $specs = explode('Failures:', $specs);
    $specFailure = isset($specs[1])? 'Failures:' . $specs[1]: '';
    if ($specFailure) {
      $matches = array();
      preg_match_all("~\d+\) (.+)\s+Message:\s+(.+)\s+Stacktrace:\s+([\s\S]+?)(?= \d+\)|$)~", $specs[1], $matches);
      foreach ($matches[0] as $i=>$match) {
        $definition = $matches[1][$i];
        $expected = $matches[2][$i];
        $stacktrace = $matches[3][$i];

        $data['expectations'][] = array(
          'd'=>$definition,
          'e'=>$expected,
          's'=>$stacktrace
        );
      }
    }

    $this->build->storeMeta('jasmine-node-errors', $failureCount);
    $this->build->storeMeta('jasmine-node-data', $data);

    if ($specCount == 0) {
      $this->phpci->logFailure(Lang::get('no_tests_performed'));
      return false;
    } elseif ($failureCount > 0) {
      if (!$this->log)
        $this->phpci->logFailure($specFailure);

      return false;
    } else {
      return true;
    }
  }
}
