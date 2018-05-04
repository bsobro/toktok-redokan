<?php

namespace ContentEgg\application\components;

/**
 * Scheduler class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2016 keywordrush.com
 */
abstract class Scheduler implements iScheduler {

    public static function initAction()
    {
        \add_action(static::getCronTag(), array(get_called_class(), 'run'));
    }

    public static function addScheduleEvent($recurrence = 'hourly', $timestamp = null)
    {
        if (!$timestamp)
            $timestamp = time();

        if (!\wp_next_scheduled(static::getCronTag()))
            \wp_schedule_event($timestamp, $recurrence, static::getCronTag());
    }

    public static function clearScheduleEvent()
    {
        if (\wp_next_scheduled(static::getCronTag()))
            \wp_clear_scheduled_hook(static::getCronTag());
    }

}
