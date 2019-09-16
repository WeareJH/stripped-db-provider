<?php

declare(strict_types=1);

namespace Jh\StrippedDbProvider\Model\Config\Backend;

use Magento\Cron\Model\Config\Backend\Product\Alert;
use Magento\Cron\Model\Config\Source\Frequency as CronFrequency;

class Frequency extends Alert
{
    /**
     * Cron string path
     */
    const CRON_STRING_PATH_STRIPPED_DB_UPDATE = 'crontab/default/jobs/jh_stripped_db_backup/schedule/cron_expr';

    /**
     * Cron model path
     */
    const CRON_MODEL_PATH_STRIPPED_DB_UPDATE = 'crontab/default/jobs/jh_stripped_db_backup/run/model';

    /**
     * {@inheritdoc}
     *
     * @return $this
     * @throws \Exception
     */
    public function afterSave()
    {

        $time = $this->getData('groups/cron/fields/time/value');
        $frequency = $this->getData('groups/cron/fields/frequency/value');

        $cronExprArray = [
            intval($time[1]), //Minute
            intval($time[0]), //Hour
            $frequency == CronFrequency::CRON_MONTHLY ? '1' : '*', //Day of the Month
            '*', //Month of the Year
            $frequency == CronFrequency::CRON_WEEKLY ? '1' : '*', //Day of the Week
        ];

        $cronExprString = join(' ', $cronExprArray);

        try {
            $this->_configValueFactory->create()->load(
                self::CRON_STRING_PATH_STRIPPED_DB_UPDATE,
                'path'
            )->setValue(
                $cronExprString
            )->setPath(
                self::CRON_STRING_PATH_STRIPPED_DB_UPDATE
            )->save();
            $this->_configValueFactory->create()->load(
                self::CRON_MODEL_PATH_STRIPPED_DB_UPDATE,
                'path'
            )->setValue(
                $this->_runModelPath
            )->setPath(
                self::CRON_MODEL_PATH_STRIPPED_DB_UPDATE
            )->save();
        } catch (\Exception $e) {
            throw new \Exception(__('We can\'t save the cron expression.'));
        }

        return parent::afterSave();
    }
}