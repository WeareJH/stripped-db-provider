# Jh Stripped Db Provider

This module can be configured to automatically send stripped databases periodically into an Amazon S3 Bucket in the cloud.

## Installation


Add the repository to composer.json:
```
composer config repositories.stripped-db-provider  vcs git@github.com:WeareJH/stripped-db-provider.git
```
Require package:
```
composer require wearejh/stripped-db-provider
```
Install package:
```
composer update wearejh/stripped-db-provider
```

## Configuration

Once Installed and deployed to production, you will want to configure it so that it automatically sends stripped DBs to the S3 Bucket. The configuration can be found in the admin at _Stores > Configuration > JH MODULES > Stripped DB Backups_

The Amazon S3 Bucket Details should be in LastPass. Ask your fellow developers if you can't find it or need help setting up.

## Manual Run

You can also manually trigger the stripped database upload from the command line by running the following command : 

```
bin/magento wearejh:db:backup-stripped-db 
```

## Issues / Feature Request

Please open github issues for any issues you encounter or feature requests you want to see. PRs are of course welcomed.