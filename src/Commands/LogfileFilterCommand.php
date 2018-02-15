<?php
/**
 * This file is a part of atdean/httpd-logslice.
 *
 * (c) 2018 Austin Dean
 *
 * For the full copyright and license information, please view
 * the license that was distributed with the source code.
 */

namespace ATDean\HttpdLogSlice\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Carbon\Carbon;

/**
 * @author Austin Dean <amberdean89@gmail.com>
 */
class LogfileFilterCommand extends Command
{
    private $patterns = [
        'newlines' => '/\n|\r/',
        'matchColumns' => '/\"[^\"]+\"|\S+/',
        'ipAddress' => '/(?:(?P<request_ip>[0-9]{3}\.)\S+)/',
        'request' => '/(?:(?P<type>[A-Z]+) (?P<url>.+?)\s)/',
        'insideBrackets' => '/\[(.+)\]/',
        'timestamp' => '/(?:\[(?P<day>[0-9][0-9])\/(?P<month>[a-zA-Z]{3})\/(?P<year>[0-9]{4})\:(?P<hours>[0-9]{2})\:(?P<minutes>[0-9]{2})\:(?P<seconds>[0-9]{2})+\])/'
    ];

    private $filters = [];

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
             )->addOption(
                 'start-date',
                 'd',
                 InputOption::VALUE_REQUIRED,
                 'Starting date of logs to analyze. If no end date is provided, '
                    . 'will select entries from start date up to the most current.',
                 null
             )->addOption(
                 'end-date',
                 'D',
                 InputOption::VALUE_REQUIRED,
                 'Ending date of logs to analyze. If no start date is provided, '
                    . 'will select entries from the earliest up to the end date.',
                 null
             );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // TODO :: Decouple parsing logic from the console command.

        try {
            $loadedFiles = $this->loadFiles($input->getArgument('filepaths'));

            // TODO :: Query input for filter params and generate filter objects.

            // TODO :: Store parsed data in an internal array and route to desired output.
            $this->parseFiles($loadedFiles, $this->createFilters($input));
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
            exit(1);
        }
    }

    /**
     * @param array $filepaths An array of file paths relative to working dir to load
     * @throws Exception When no files are specified, or if a file cannot be loaded
     * @return array
     */
    private function loadFiles(array $filepaths) : array
    {
        $filesArray = [];

        if (count($filepaths) > 0) {
            foreach ($filepaths as $fp) {
                /* If the given path starts with and /, we know it is absolute.
                 * If it does not, it's relative and must be appended to working dir. */
                if (substr($fp, 0, 1) !== '/') {
                    $fp = getcwd() . '/' . $fp;
                }

                try {
                    /* file_get_contents produces a warning rather than an Exception
                     * if it can't locate the file. @ supresses the console output,
                     * and we use falsey checking to detect and handle failure. */
                    $content = @file_get_contents($fp);

                    if ($content !== false) {
                        array_push(
                            $filesArray,
                            ['filepath' => $fp, 'raw_text' => $content]
                        );
                    } else {
                        throw new \Exception();
                    }
                } catch (\Exception $e) {
                    // $childMsg = (empty($e->getMessage)) ? ': ' . $e->getMessage() : '';
                    throw new \Exception(
                        'Error: Unable to open file at ' . $fp
                    );
                }
            }
        } else {
            throw new \Exception('Error: No logfiles were specified.');
        }

        return $filesArray;
    }

    private function parseFiles(array $loadedFiles, array $filters)
    {
        foreach ($loadedFiles as $file) {
            // Split raw text glob into lines, removing empty lines
            $splitLines = preg_split($this->patterns['newlines'],
                $file['raw_text'], -1, PREG_SPLIT_NO_EMPTY);

            foreach ($splitLines as $line) {
                $logEntry = $this->analyzeEntry($line);
                var_dump($logEntry);
            }
        }
    }

    private function analyzeEntry($line)
    {
        // TODO :: Replace all of this with a consolidated capture regex to validate format.

        // Break into columns
        preg_match_all($this->patterns['matchColumns'],
            $line, $columns, PREG_PATTERN_ORDER);

        $entry = [];

        $entry['request_ip'] = (!empty($columns[0][0])) ? $columns[0][0] : null;
        $entry['remote_user'] = (!strcmp($columns[0][2], '-')) ? $columns[0][2] : null;

        // Parse timestamp Component
        preg_match($this->patterns['insideBrackets'], $columns[0][3], $timestamp);

        try {
            if (!empty($timestamp)) {
                $entry['timestamp'] = Carbon::createFromFormat('d/M/Y:H:i:s', $timestamp[1]);
            } else {
                throw new \Exception('Invalid or empty timestamp provided.');
            }
        } catch (\Exception $e) {
            throw new \Exception("Could not create timestamp from format:\n" . $e->getMessage());
        }

        // Parse request component
        preg_match($this->patterns['request'],
            $columns[0][4], $reqInfo);

        if (!empty($reqInfo)) {
            $entry['request_type'] = $reqInfo['type'];
            $entry['request_url'] = $reqInfo['url'];
        } else {
            $entry['request_type'] = null;
            $entry['request_url'] = null;
        }

        return $entry;
    }
}
