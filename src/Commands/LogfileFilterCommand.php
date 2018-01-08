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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Austin Dean <amberdean89@gmail.com>
 */
class LogfileFilterCommand extends Command
{
    protected function configure()
    {
        $this->setName('logfile:filter')
             ->setDescription('Parses the specified logfile.')
             ->setHelp('Parses the specified logfile and returns an analysis '
                 . 'and/or subset of the data as specified by the given arguments.')
             ->addArgument(
                 'filepaths',
                 InputArgument::IS_ARRAY | InputArgument::REQUIRED,
                 'Which logfiles should be processed?'
             );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filepaths = $input->getArgument('filepaths');

        if (count($filepaths) > 0) {
            foreach ($filepaths as $fn) {
                $output->writeln('File: ' . $fn);
            }
        } else {
            throw new \Exception('Error: No logfiles were specified.');
        }

        $output->writeln('It works');
    }
}
