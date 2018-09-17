<?php

namespace Helper;

use Codeception\Exception\ModuleException;
use Codeception\Lib\Interfaces\DependsOnModule;
use Codeception\Module\Cli;
use Codeception\TestInterface;

/**
 * Class CleanSphinxSearch
 * @package Helper
 */
class CleanSphinxSearch extends \Codeception\Module implements DependsOnModule
{
    /**
     * @var Cli $cli
     */
    private $cli;

    /**
     * @param Cli $cli
     */
    public function _inject(\Codeception\Module\Cli $cli)
    {
        $this->cli = $cli;
    }

    /**
     * @return array
     */
    public function _depends()
    {
        return ['Codeception\Module\Cli' => 'Cli is a mandatory dependency of CleanSphinxSearch'];
    }

    /**
     * Cleans rt indexes
     *
     * @throws ModuleException
     */
    public function cleanSphinxSearch()
    {
        if (!isset($this->config['host']) || !isset($this->config['port'])) {
            throw new ModuleException(__CLASS__, 'requires the options "host" and "port"');
        }
        $host = $this->config['host'];
        $port = $this->config['port'];

        $this->cli->runShellCommand("mysql -h {$host} -P {$port} -e 'DELETE FROM rt_files WHERE id!=1;'");
    }
}
