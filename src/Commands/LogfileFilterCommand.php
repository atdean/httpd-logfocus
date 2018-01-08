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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Austin Dean <amberdean89@gmail.com>
 */
class LogfileFilterCommand extends Command
{
    private $baseDir;
    private $rawLogfileData = [];

    public function __construct($baseDir)
    {
        parent::__construct();

        $this->baseDir = $baseDir;
    }

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
        try {
            $this->loadFiles($input->getArgument('filepaths'));

            foreach($this->rawLogfileData as $data) {
                $output->writeln(key($data));
            }
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
            exit(1);
        }

        // ("[^"]+")|\S+
    }

    /**
     * @param $filepaths An array of file paths relative to working dir to load
     *
     * @throws Exception When no files are specified, or if a file cannot be loaded
     */
    private function loadFiles($filepaths)
    {
        if (count($filepaths) > 0) {
            foreach ($filepaths as $fp) {
                /* If the given path starts with and /, we know it is absolute.
                 * If it does not, it's relative and must be appended to __DIR__. */
                if (substr($fp, 0, 1) !== '/') {
                    $fp = __DIR__ . '/' . $fp;
                }

                try {
                    /* file_get_contents produces a warning rather than an Exception
                     * if it can't locate the file. @ supresses the console output,
                     * and we use falsey checking to detect and handle failure. */
                    $content = @file_get_contents($fp);

                    if ($content !== false) {
                        array_push($this->rawLogfileData, [$fp => $content]);
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
    }
}
