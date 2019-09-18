# Jh Stripped Db Provider

This module can be configured to automatically send stripped databases periodically into an Amazon S3 Bucket in the cloud.

## Installation

Install like any regular composer package
```bash
composer config repositories.stripped-db-provider  vcs git@github.com:WeareJH/stripped-db-provider.git
composer require wearejh/stripped-db-provider
composer update wearejh/stripped-db-provider
```

## Configuration

Once Installed and deployed to production, you will want to configure it so that it automatically sends stripped DBs to the S3 Bucket.
Edit the env.php file manually and set the following config (values below are examples):
```json
'system' => [
        'default' => [
            'stripped_db_provider' => [
                'general' => [
                    'enabled' => '1',
                    'project_name' => 'example-project',
                    'cron_expr' => '0 4 * * 0'
                ],
                'storage' => [
                    'bucket_name' => 'example-bucket-name',
                    'region' => 'example-bucket-region',
                    'access_key_id' => 'example-bucket-access-key',
                    'secret_access_key' => 'example-bucket-secret-access-key'
                ],
                'dump' => [
                    'project_ignore_tables' => ['example_table_a', 'example_table_b']
                ]
            ]
        ]
    ]
```

Values are described in the following table:

| config                | value                                                               |
|-----------------------|---------------------------------------------------------------------|
| enabled               | One of {0,1}                                                        |
| project-name          | String. Unique name for the project (used to name the db dump)      |
| cron_expr             | A valid cron expression. Eg. "0 0/5 * * *"                          |
| bucket_name           | String. AWS S3 Bucket name                                          |
| region                | String. AWS S3 Bucket region                                        |
| access_key_id         | String. AWS S3 Bucket access key id                                 |
| secret_access_key     | String. AWS S3 Bucket secret access key                             |
| project_ignore_tables | Array of Strings. Project specific tables that should be stripped.  |

The Amazon S3 Bucket Details should be in LastPass. Ask your fellow developers if you can't find it or need help setting up.

After you have edited the env.php file, run the following command immediately :

`bin/magento app:config:import` 

## Manual Run

You can also manually trigger the stripped database upload from the command line by running the following command : 

```
bin/magento wearejh:db:backup-stripped-db 
```

## Issues / Feature Request

Please open github issues for any issues you encounter or feature requests you want to see. PRs are of course welcomed.