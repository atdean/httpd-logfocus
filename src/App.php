<?php
/**
 * This file is a part of atdean/httpd-logslice.
 *
 * (c) 2018 Austin Dean
 *
 * For the full copyright and license information, please view
 * the license that was distributed with the source code.
 */

namespace ATDean\HttpdLogSlice;

use Symfony\Component\Console\Application as BaseConsoleApp;
use Symfony\Component\Console\Input\InputInterface;

/**
 * @author Austin Dean <amberdean89@gmail.com>
 */
class App extends BaseConsoleApp
{
    private $baseDir;

    public function __construct()
    {
        parent::__construct('httpd-logslice');

        $this->setBaseDirectory();

        $this->add(new Commands\LogfileFilterCommand($this->baseDir));
    }

    /**
     * Trace back to the entry point of the application to get the original
     * working directory, in order to keep relative filepaths consistent.
     */
    private function setBaseDirectory()
    {
        $trace = debug_backtrace();
        $this->baseDir = $trace[count($trace) - 1]['file'];


    }
}
