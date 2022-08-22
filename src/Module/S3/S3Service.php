<?php

namespace Console\Module\S3;

class S3Service
{
    public const BUCKET = 'appsync-latency-log';

    public function writeDataToS3(
        string $fileName,
        array $data
    ) {
        $client = S3::getInstance()->getClient();
        if (!$client->doesBucketExist(self::BUCKET)) {
            $client->createBucket(array('Bucket' => self::BUCKET));
            $client->waitUntil('BucketExists', array('Bucket' => self::BUCKET));
        }

        $path = ROOT_DIRECTORY . '/latency.csv';
        $this->toCSV($path, $data);

        $result = $client->putObject(array(
            'Bucket'     => self::BUCKET,
            'Key'        => $fileName,
            'SourceFile' => $path,
        ));

        return $result['ObjectURL'];
    }

    private function toCSV(string $path, array $data) {
        $fp = fopen($path, 'w');
        foreach ($data as $fields) {
            fputcsv($fp, $fields);
        }

        fclose($fp);
    }
}