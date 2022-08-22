<?php

namespace Console\Command;

use Console\Module\CloudWatch\CloudWatch;
use Console\Module\S3\S3;
use DateTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ExportLatencyAppsyncToS3 extends Command
{
    protected function configure(): void
    {
        $this->setName('export-latency-appsync-to-s3')
            ->setDescription('Get p95% SRT AppSync latency metric and export to S3')
            ->setHelp('Run this command to execute your custom tasks in the execute function.')
            ->addOption(
                'start-date', 
                'sd',
                InputOption::VALUE_REQUIRED, 
                'Start date?',
            )->addOption(
                'end-date', 
                'ed',
                InputOption::VALUE_REQUIRED, 
                'End date?'
            )->addOption(
                'namespace', 
                null,
                InputOption::VALUE_OPTIONAL, 
                'name space cloudwatch',
                null
            );//todo add full optional option
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $startDateOption = $input->getOption('start-date');
        $endDateOption = $input->getOption('end-date');
        $nameSpace = $input->getOption('namespace') ?? 'AWS/AppSync';

        $output->writeln(sprintf('Export latency logs from %s to %s', $startDateOption, $endDateOption));

        $startDate = new DateTime($input->getOption('start-date'));
        $endDate = new DateTime($input->getOption('end-date'));

        $bulks = CloudWatch::getInstance()->getService()->truncateDate($startDate, $endDate);
        $data = [];

        foreach($bulks as $period) {
            $output->writeln(sprintf('Fetching data from %s to %s', $period[0]->format('Y-m-d H:i:s'), $period[1]->format('Y-m-d H:i:s')));
            
            $data = array_merge(
                $data, 
                CloudWatch::getInstance()->getService()->getLatencyStatisticLog($period[0], $period[1], $nameSpace)
            );

            if (empty($data)) {
                $output->writeln('Empty');
            }
        }

        ksort($data);

        $output->writeln('Write data to S3');

        $url = S3::getInstance()->getService()->writeDataToS3(
            $startDateOption . 'To' . $endDateOption . '.csv',
            $data
        );

        $output->writeln('URL: ' . $url);

        return 1;
    }
}