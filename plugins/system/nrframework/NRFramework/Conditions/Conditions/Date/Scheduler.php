<?php

/**
 * @author          Tassos.gr <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Conditions\Conditions\Date;

defined('_JEXEC') or die;

/**
 * DateTime Assignment Scheduling helper
 */
class Scheduler
{
  /**
   * Starting date
   *
   * @var object \DateTime
   */
  protected $start_date;

  /**
   * Ending date
   *
   * @var object \DateTime
   */
  protected $end_date;

  /**
   * Date to test against
   *
   * @var object \DateTime
   */
  protected $current_date;

  /**
   * @var string
   */
  protected $repetitionFrequency;

  /**
   * @var int 
   */
  protected $repetitionStep;

  /**
   * Used by 'weekly' repetition frequency
   *
   * @var array
   */
  protected $weekdays;

  /**
   * The interval between the starting and current date
   * http://php.net/manual/en/class.dateinterval.php
   * 
   * @var object \DateInterval
   */
  protected $interval;


  /**
   * Scheduler constructor
   *
   * @param array $options: Scheduling options
   *    start_date:
   *    end_date:
   *    current_date:
   *    repetitionFrequency: one of 'daily', 'weekly', 'monthly', 'yearly'
   *    repetitionStep: integer (1,2,3,...)
   *    weekdays: Array, used with 'weekly' repetitionFrequency, any of 'Monday', 'Tuesday', etc.
   *              Defaults to start_date's day name if empty
   */
  public function __construct($options)
  {
    $this->start_date          = $options['start_date'];
    $this->end_date            = array_key_exists('end_date', $options) ? $options['end_date'] : null;
    $this->current_date        = $options['current_date'];
    $this->repetitionFrequency = $options['repetitionFrequency'];
    $this->repetitionStep      = $options['repetitionStep'];
    $this->weekdays            = array_key_exists('weekdays', $options) ? 
                                  array_map('ucfirst', $options['weekdays']) :
                                  null;
    
    //create a DateInterval object from current and start dates
    $this->interval = $this->start_date->diff($this->current_date);
  }

  /**
   * @return bool
   */
  public function repeat()
  {
    //check if we are within the start/end date range (inclusive)
    if (!$this->checkDateRange())
    {
      return false;
    }

    $result = false;

    //
    switch ($this->repetitionFrequency)
    {
      case 'daily':
        $result = $this->repeatDaily();
        break;
      case 'weekly':
        $result = $this->repeatWeekly();
        break;
      case 'monthly':
        $result = $this->repeatMonthly();
        break;
      case 'yearly':
        $result = $this->repeatYearly();
        break;
    }

    return $result;
  }

  /**
   * Daily repetition check
   *
   * @return bool
   */
  protected function repeatDaily()
  {
    //get the number of days that have passed since start_date
    $num_days = $this->interval->days;

    if ($num_days % $this->repetitionStep !== 0) {
      return false;
    }

    return true;
  }

  /**
   * Weekly repetition check
   *
   * @return bool
   */
  protected function repeatWeekly()
  {
    //get current_date's day name
    $today_name = $this->current_date->format('l');

    // if $this->weekdays is empty use start_date's day name
    if (empty($this->weekdays))
    {
      $start_day_name = $this->start_date->format('l');
      if ($start_day_name !== $today_name)
      {
        return false;
      }
    }
    else
    {
      if (!in_array($today_name, $this->weekdays))
      {
        return false;
      }
    }
    
    //get the number of weeks that passed since start_date
    $num_weeks = floor($this->interval->days / 7);

    if ($num_weeks % $this->repetitionStep !== 0) 
    {
      return false;
    }

    return true;
  }

  /**
   * Monthly repetition check
   *
   * @return bool
   */
  protected function repeatMonthly()
  {
    //check if we are on the same day of the month
    $start_day    = $this->start_date->format('d');
    $current_day  = $this->current_date->format('d');

    if ($start_day !== $current_day)
    {
      return false;
    }

    //get the number of months that have passed since start_date
    $num_months = ($this->interval->y * 12) + $interval->m;

    if ($num_months % $this->repetitionStep !== 0) 
    {
      return false;
    }

    return true;
  }

   /**
   * Yearly repetition check
   *
   * @return bool
   */
  protected function repeatYearly()
  {
    //check if we are on the same month and day
    $start_day_month    = $this->start_date->format('d-m');
    $current_day_month  = $this->current_date->format('d-m');

    if ($start_day_month !== $current_day_month)
    {
      return false;
    }
    
    //get the number of years that have passed since start_date
    $num_years = $this->interval->y;

    if ($num_years % $this->repetitionStep !== 0) 
    {
      return false;
    }

    return true;
  }

  /**
   * Checks if the current date is between the start/end range (inclusive)
   *
   * @return bool
   */
  protected function checkDateRange()
  {
    if ($this->start_date > $this->current_date)
    {
      return false;
    }

    if (!empty($this->end_date) && $this->end_date < $this->current_date)
    {
      return false;
    }

    return true;
  }
}