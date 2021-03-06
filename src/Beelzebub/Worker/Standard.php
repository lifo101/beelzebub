<?php


/*
 * This file is part of Fortrabbit\Beelzebub.
 *
 * (c) Ulrich Kautz <uk@fortrabbit.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Beelzebub\Worker;

use Beelzebub\Daemon;
use Beelzebub\Worker;
use Spork\Fork;

/**
 * Base class for Worker
 *
 * @author Ulrich Kautz <uk@fortrabbit.com>
 */

class Standard implements Worker
{

    /**
     * @var Daemon
     */
    protected $daemon;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $interval;

    /**
     * @var int
     */
    protected $amount;

    /**
     * @var callable
     */
    protected $loop;

    /**
     * @var callable
     */
    protected $startup;

    /**
     * @var array
     */
    protected $pids;

    /**
     * {@inheritdoc}
     */
    public function __construct($name, $loop, $interval = self::DEFAULT_INTERVAL, $startup = null, $amount = self::DEFAULT_AMOUNT)
    {
        // for < 5.4, we cannot use type hints
        if (!is_callable($loop)) {
            throw new \BadMethodCallException("Loop needs to be callable");
        }
        if (!is_null($startup) && !is_callable($startup)) {
            throw new \BadMethodCallException("Startup needs to be callable");
        }
        $this->name     = $name;
        $this->loop     = $loop;
        $this->interval = $interval ? : self::DEFAULT_INTERVAL;
        $this->startup  = $startup;
        $this->amount   = $amount ? : self::DEFAULT_AMOUNT;
    }

    /**
     * {@inheritdoc}
     */
    public function run(array $args = array())
    {
        $callArgs = array($this, $args);
        call_user_func_array($this->loop, $callArgs);
    }

    /**
     * {@inheritdoc}
     */
    public function hasStartup()
    {
        return $this->startup ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function runStartup()
    {
        if ($this->startup) {
            return call_user_func_array($this->startup, array($this));
        } else {
            return null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDaemon(Daemon $daemon)
    {
        $this->daemon = $daemon;
    }

    /**
     * {@inheritdoc}
     */
    public function getDaemon()
    {
        return $this->daemon;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setInterval($interval)
    {
        $this->interval = $interval;
    }

    /**
     * {@inheritdoc}
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * {@inheritdoc}
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * {@inheritdoc}
     */
    public function getAmount()
    {
        return $this->amount;
    }

}
