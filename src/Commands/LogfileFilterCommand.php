<?php
/**
 * This file is a part of atdean/httpd-logslice.
 *
 * (c) 2018 Austin Dean
 *
 * For the full copyright and license information, please view
 * the license that is located at the bottom of this file.
 */

namespace ATDean\HttpdLogSlice\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LogfileFilterCommand extends Command
{
    protected function configure()
    {
        $this->setName('logfile:filter')
             ->setDescription('Parses the specified logfile and returns an analysis and/or subset of the data as specified by the given arguments.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('It works');
    }
}
