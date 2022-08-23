<?php

namespace Console\Module\S3;
use Aws\Exception\AwsException;

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

            $publicPolicy = [
                "Version" => "2012-10-17",
                "Statement" => [
                    [
                        "Sid" => "PublicRead",
                        "Effect" => "Allow",
                        "Principal" => "*",
                        "Action" => [
                            "s3:GetObject",
                            "s3:GetObjectVersion"
                        ],
                        "Resource" => sprintf("arn:aws:s3:::%s/*", self::BUCKET)
                    ]
                ]
            ];

            $this->putPolicy(json_encode($publicPolicy));
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

    private function putPolicy(string $policy) {
        $client = S3::getInstance()->getClient();
        try {
            $resp = $client->putBucketPolicy([
                'Bucket' => self::BUCKET,
                'Policy' => $policy,
            ]);
        } catch (AwsException $e) {
            echo $e->getMessage();
            echo "\n";
        }
    }
}