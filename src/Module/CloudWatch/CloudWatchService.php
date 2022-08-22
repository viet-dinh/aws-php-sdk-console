<?php

namespace Console\Module\CloudWatch;

use DateTime;
use DateTimeZone;

class CloudWatchService
{
    private const MAX_DATA_POINTS = 1440;
    
    /**
     * @return array
     */
    public function truncateDate(
        DateTime $startTime,
        DateTime $endTime, 
        $period=300
    ) {
        $result = [];
        $start = $startTime;
        while (true) {
            $end = new DateTime();
            $end->setTimestamp($start->getTimestamp() + 300*(self::MAX_DATA_POINTS - 1));
            if ($end->getTimestamp() > $endTime->getTimestamp()) {
                $result[] = [$start, $endTime];
                break;
            }

            $result[] = [$start, $end];
            $start = $end;
        }

        return $result;
    }

    public function getLatencyStatisticLog(
        DateTime $startTime,
        DateTime $endTime,
        string $nameSpace = 'AWS/AppSync',
        string $metricName = 'Latency',
        string $dimensionName = 'GraphQLAPIId',
        string $dimensionValue = 'bhiu7e5jwfdy3kzoyzx4we5csa',
        string $statistic = 'p95',
        int $period = 300,
        string $unit = 'Milliseconds'
    ) {
        $res = CloudWatch::getInstance()->getClient()->getMetricStatistics([
            'Namespace' => $nameSpace,
            'MetricName' => $metricName,
            'Dimensions' => [
                [
                    'Name' => $dimensionName,
                    'Value' => $dimensionValue,
                ],
            ],
            'StartTime' => $startTime,
            'EndTime' => $endTime,
            'ExtendedStatistics' => [$statistic],
            'Period' => $period,
            'Unit' => $unit,
        ]);

        $data = [];
       // $timeZone = new DateTimeZone('Asia/Ho_Chi_Minh');
        
        foreach($res->get('Datapoints') as $dataPoint) {
            $datum = [];
            $datum['time'] = $dataPoint['Timestamp']
                //->setTimezone($timeZone)
                ->__toString();
            $datum['p95'] = $dataPoint['ExtendedStatistics']['p95'];
            $data[$datum['time']] = $datum;
        }
        
        return $data;
    }
}