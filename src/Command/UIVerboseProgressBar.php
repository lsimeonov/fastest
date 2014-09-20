<?php

namespace Liuggio\Fastest\Command;

use Liuggio\Fastest\Process\Processes;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class UIVerboseProgressBar implements ProgressBarInterface
{
    private $messageInTheQueue;
    private $lastIndex;
    private $output;

    public function __construct($messageInTheQueue, OutputInterface $output)
    {
        $this->messageInTheQueue = $messageInTheQueue;
        $this->output = $output;
        $this->lastIndex = 0;
    }

    public function render($queue, Processes $processes)
    {
        $now = $queue->count();
        $errorCount = $processes->countErrors();

        $log = $processes->getReport();
        $count = count($log);
        $tests = array_slice($log, $this->lastIndex, $count, 1);

        foreach ($tests as $report) {

            $flag = "<info>✔</info>";
            $err = '';
            if (!$report->isSuccessful()) {
                $flag = "<error>✘</error>";
                $err = $report->getErrorBuffer();
            }

            $remaining = sprintf('%d/%d', $now, $this->messageInTheQueue);

            $this->output->writeln($remaining."\t".$flag."\t".$report->getSuite().$err);

        }
        $this->lastIndex = $count;

        return $errorCount;
    }

    public function finish($queue, Processes $processes)
    {
        $this->render($queue, $processes);
    }
}