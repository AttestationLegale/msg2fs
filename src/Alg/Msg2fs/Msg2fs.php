<?php

namespace Alg\Msg2fs;

class Msg2fs {

    /**
     * @var string
     */
    private $spoolDir = '/var/spool/msg2fs/';

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
     * Msg2fs constructor.
     *
     * @return self
     */
    public function __construct() {
        if(!$this->spoolDir = getenv('SPOOLDIR'))
            $this->spoolDir = '/var/spool/msg2fs/';

        return $this;
    }

    /**
     * Save message to filesystem.
     *
     * @param array $msg
     * @param string $exchange
     * @param string $routingKey
     */
    public function save(array $msg, $exchange, $routingKey) {
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
    private function formatLocalId() {
        return microtime(true) . "." . substr(md5(rand()), 0, 8) . "@" . $this->routingKey . "@" . $this->exchange . ".v1";
    }

    /**
     * Add system vars to message head.
     */
    private function improveMsgHead() {
        if (false == array_key_exists("head", $this->msg))
            $this->msg['head'] = array();

        $this->msg['head']['x-msg2fs-fid'] = $this->outFile;
        $this->msg['head']['x-msg2fs-hn'] = gethostname();
        $this->msg['head']['x-msg2fs-dt'] = (new \DateTime())->format(DATE_ISO8601);
    }

    /**
     * Write message to filesystem
     */
    private function flush() {
        $file = new \SplFileObject($this->outFile, 'w');
        $file->fwrite(json_encode($this->msg));
    }

}
