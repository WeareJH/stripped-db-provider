# Jh Stripped Db Provider

This module can be configured to automatically send stripped databases periodically into an Amazon S3 Bucket in the cloud.

## Installation

Add the following repositories to the `repositories` section of your `composer.json`

```
{"type": "vcs", "url": "git@github.com:WeareJH/stripped-db-provider.git"},
```

Then install like any regular composer package
```bash
composer require wearejh/stripped-db-provider
```

## Configuration

There are two areas of configuration. The first is project specific config, which is set in **config.php** and gets commited to the repository. The second is environment specific configuration which is added to the **env.php** file directly on the server.

### Project Specific Configuration

All databases are stripped by default but you can define project specific tables whose data you wish to not be included in the dump by adding the following to your **config.php** file:

```
'system' => [
        'default' => [
            'stripped_db_provider' => [
                'dump' => [
                    'project_ignore_tables' => ['example_table_a', 'example_table_b']
                ]
            ]
        ]
    ]
```

Where `project_ignore_tables` is a list of project specfic tables.

### Environment Specific Configuration

Once Installed and deployed to production, you will want to configure it so that it automatically sends stripped DBs to the S3 Bucket.
Edit the **env.php** file manually and set the following config (values below are examples):
```
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
                    'secret_access_key' => 'example-bucket-secret-access-key',
                    'using_encrypted_values_for_s3_config' => false
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

The Amazon S3 Bucket Details should be in LastPass. Ask your fellow developers if you can't find it or need help setting up.

After you have edited the env.php file, run the following command immediately :

```
bin/magento app:config:import
``` 

## Run upload manually

You can also manually trigger the stripped database upload from the command line by running the following command : 

```
bin/magento wearejh:stripped-db-provider:upload-to-remote 
```

To do a full DB dump

```
bin/magento wearejh:stripped-db-provider:upload-to-remote --full
```

## Import a remote dump locally

You can use the module to import a dump from S3 directly to your local. It will back up your local admin accounts and 
reimport them

```
bin/magento wearejh:stripped-db-provider:import-from-remote PROJECT NAME 
```

To skip admin accounts backup

```
bin/magento wearejh:stripped-db-provider:import-from-remote PROJECT NAME --no-admin-backup=1
```

## Issues / Feature Request

Please open github issues for any issues you encounter or feature requests you want to see. PRs are of course welcomed.

## Troubleshooting

If any project has 0.3.3 version installed of this module, getting a dependency error related to module: ifsnop/mysqldump-php ^2.12 while upgrading module to latest version then remove the below line from composer.json file of project root folder:
        {
           "type": "vcs",
           "url": "git@github.com:maciejslawik/mysqldump-php.git"
         }
