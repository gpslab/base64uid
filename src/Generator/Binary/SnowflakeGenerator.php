<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Base64UID\Generator\Binary;

use GpsLab\Component\Base64UID\Exception\ArgumentRangeException;
use GpsLab\Component\Base64UID\Exception\ArgumentTypeException;
use GpsLab\Component\Base64UID\Exception\ZeroArgumentException;

class SnowflakeGenerator implements BinaryGenerator
{
    /**
     * TODO use private const after drop PHP < 7.1.
     *
     * @var int
     */
    private static $DATA_CENTER_LENGTH = 5; // data center value 0-31

    /**
     * TODO use private const after drop PHP < 7.1.
     *
     * @var int
     */
    private static $MACHINE_LENGTH = 7; // machine value 0-127

    /**
     * TODO use private const after drop PHP < 7.1.
     *
     * @var int
     */
    private static $SEQUENCE_LENGTH = 6; // sequence value 0-63

    /**
     * @var int
     */
    private $data_center;

    /**
     * @var int
     */
    private $machine;

    /**
     * @var int
     */
    private $time_offset;

    /**
     * @var int
     */
    private $last_time = 0;

    /**
     * @var int
     */
    private $sequence = 0;

    /**
     * Snowflake.
     *
     * The time offset allows to move the starting point of time in microseconds,
     * which reduces the size of the stored time:
     *  0             = 1970-01-01 00:00:00 (UTC)
     *  1577833200000 = 2020-01-01 00:00:00 (UTC)
     *
     * @param int $data_center
     * @param int $machine
     * @param int $time_offset
     */
    public function __construct($data_center, $machine, $time_offset = 0)
    {
        if (!is_int($data_center)) {
            throw new ArgumentTypeException(sprintf('Data center should be integer, got "%s" instead.', gettype($data_center)));
        }

        if (!is_int($machine)) {
            throw new ArgumentTypeException(sprintf('Machine should be integer, got "%s" instead.', gettype($data_center)));
        }

        if (!is_int($time_offset)) {
            throw new ArgumentTypeException(sprintf('Time offset should be integer, got "%s" instead.', gettype($data_center)));
        }

        if ($data_center < 0) {
            throw new ZeroArgumentException(sprintf('Data center should be grate then "0", got "%d" instead.', $data_center));
        }

        if ($machine < 0) {
            throw new ZeroArgumentException(sprintf('Machine should be grate then "0", got "%d" instead.', $machine));
        }

        if ($time_offset < 0) {
            throw new ZeroArgumentException(sprintf('Time offset should be grate then "0", got "%d" instead.', $time_offset));
        }

        $max_data_center = (int) bindec(str_repeat('1', self::$DATA_CENTER_LENGTH));

        if ($data_center > $max_data_center) {
            throw new ArgumentRangeException(sprintf('Data center number should be grate then or equal to "%d", got "%d" instead.', $max_data_center, $data_center));
        }

        $max_machine = (int) bindec(str_repeat('1', self::$MACHINE_LENGTH));

        if ($machine > $max_machine) {
            throw new ArgumentRangeException(sprintf('Data center number should be grate then or equal to "%d", got "%d" instead.', $max_machine, $machine));
        }

        $now = (int) floor(microtime(true) * 1000);

        if ($time_offset > $now) {
            throw new ArgumentRangeException(sprintf('Time offset should be grate then or equal to current time "%d", got "%d" instead.', $now, $time_offset));
        }

        $this->data_center = $data_center;
        $this->machine = $machine;
        $this->time_offset = $time_offset;
    }

    /**
     * @return int
     */
    public function generate()
    {
        $time = ((int) floor(microtime(true) * 1000) - $this->time_offset);

        if ($this->last_time === $time) {
            ++$this->sequence;
        } else {
            $this->last_time = $time;
        }

        $uid = 1 << 64 - 1;
        $uid |= $time << self::$DATA_CENTER_LENGTH + self::$MACHINE_LENGTH + self::$SEQUENCE_LENGTH;
        $uid |= $this->data_center << self::$MACHINE_LENGTH + self::$SEQUENCE_LENGTH;
        $uid |= $this->machine << self::$SEQUENCE_LENGTH;
        $uid |= $this->sequence;

        return $uid;
    }
}
