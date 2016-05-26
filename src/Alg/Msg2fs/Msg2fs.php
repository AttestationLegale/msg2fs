<?php

namespace Alg\Msg2fs;

class Msg2fs
{

    /**
     * @var string
     */
    private $spoolDir;

    /**
     * @var string
     */
    private $exchange;

    /**
     * @var string
     */
    private $routingKey;

    /**
     * @var string
     */
    private $outFile;

    /**
     * @var array
     */
    public $msg;

    /**
     * @var string
     */
    private $dateFormat;



    /**
     * Msg2fs constructor.
     *
     * @return self
     */
    public function __construct()
    {
        if (!$this->spoolDir = getenv('SPOOLDIR')) {
            $this->spoolDir = '/var/spool/msg2fs/';
        }
        $this->dateFormat = 'DATE_ISO8601';
        $this->msg = [];
        $this->outFile = null;
        $this->exchange = null;
        $this->routingKey = null;


        return $this;
    }
    /**
     * @return string
     */
    public function getDateFormat()
    {
        return $this->dateFormat;
    }

    /**
     * @param string $dateFormat
     * @return self
     */
    public function setDateFormat($dateFormat)
    {
        $this->dateFormat = $dateFormat;
        return $this;
    }


    /**
     * Save message to filesystem.
     *
     * @param array $msg
     * @param string $exchange
     * @param string $routingKey
     */
    public function save(array $msg, $exchange, $routingKey)
    {
        $this->msg = $msg;
        $this->exchange = $exchange;
        $this->routingKey = $routingKey;

        $localId = $this->formatLocalId();
        $this->outFile = $this->spoolDir . $localId;

        $this->improveMsgHead();

        $this->flush();
    }

    /**
     * Return localId well formated.
     *
     * @return string
     */
    private function formatLocalId()
    {
        return microtime(true) . "." . substr(md5(rand()), 0, 8) . "@" . $this->routingKey . "@" . $this->exchange . ".v1";
    }

    /**
     * Add system vars to message head.
     */
    public function improveMsgHead()
    {
        if (false == array_key_exists("head", $this->msg)) {
            $this->msg['head'] = array();
        }

        $this->msg['head']['x-msg2fs-fid'] = $this->outFile;
        $this->msg['head']['x-msg2fs-hn'] = gethostname();
        $this->msg['head']['x-msg2fs-dt'] = $this->getDate();
    }

    public function getDate(\DateTime $date = null)
    {
        if ($date === null) {
            $date = new \DateTime();
        }
        return $date->format($this->dateFormat);
    }


    /**
     * Write message to filesystem
     */
    private function flush()
    {
        $file = new \SplFileObject($this->outFile, 'w');
        $file->fwrite(json_encode($this->msg));
    }

}
