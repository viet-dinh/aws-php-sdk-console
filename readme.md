# AWS PHP SDK / Console

This is use to run command to cli aws

## Installation

```bash
composer install
```

## Usage

Get session token and set up 'mfa'

```
aws sts get-session-token ...
nano ~/.aws/credentials
```

Run command

```
php bin/console export-latency-appsync-to-s3 --start-date=2022-08-01T00:00:00 --end-date=2022-08-15T23:59:59
```

Output:

```
Export latency logs from 2022-08-01T00:00:00 to 2022-08-15T23:59:59
Fetching data from 2022-08-01 00:00:00 to 2022-08-05 23:55:00
Fetching data from 2022-08-05 23:55:00 to 2022-08-10 23:50:00
Fetching data from 2022-08-10 23:50:00 to 2022-08-15 23:45:00
Fetching data from 2022-08-15 23:45:00 to 2022-08-15 23:59:59
Write data to S3
URL: https://appsync-latency-log.s3.ap-southeast-1.amazonaws.com/2022-08-01T00%3A00%3A00To2022-08-15T23%3A59%3A59.csv
```

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.
