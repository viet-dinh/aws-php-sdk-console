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
        $metricId = 'bhiu7e5jwfdy3kzoyzx4we5csa',
        $period=300
    ) {
        $res = CloudWatch::getInstance()->getClient()->getMetricStatistics([
            'Namespace' => 'AWS/AppSync', // REQUIRED
            'MetricName' => 'Latency', // REQUIRED
            'Dimensions' => [
                [
                    'Name' => 'GraphQLAPIId', // REQUIRED
                    'Value' => $metricId, // REQUIRED
                ],
            ],
            'StartTime' => $startTime, // REQUIRED
            'EndTime' => $endTime, // REQUIRED
            'ExtendedStatistics' => ['p95'],
            'Period' => $period, // REQUIRED
            'Unit' => 'Milliseconds',
        ]);

        $data = [];
       // $timeZone = new DateTimeZone('Asia/Ho_Chi_Minh');
        
        foreach($res->get('Datapoints') as $dataPoint) {
            $datum = [];
            $datum['Time'] = $dataPoint['Timestamp']
                //->setTimezone($timeZone)
                ->__toString();
            $datum['p95'] = $dataPoint['ExtendedStatistics']['p95'];
            $data[$datum['Time']] = $datum;
        }
        
        return $data;
    }
}