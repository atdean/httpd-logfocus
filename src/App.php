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
    public function __construct()
    {
        parent::__construct('httpd-logslice');

        // TODO :: Add checking for permissions errors on getcwd()

        $this->add(new Commands\LogfileFilterCommand());
    }
}
