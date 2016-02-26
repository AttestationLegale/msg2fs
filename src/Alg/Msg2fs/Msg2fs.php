<?php

namespace Alg\Msg2fs;

class Msg2fs {

    /**
     * @var string
     */
    private static $spoolDir = '/var/spool/msg2fs/';

    /**
     * Save message to filesystem.
     *
     * @param array $msg
     * @param string $exchange
     * @param string $routingKey
     */
    public static function save(array $msg, $exchange, $routingKey) {
        $localId = self::formatLocalId($exchange, $routingKey);
        $outFile = self::$spoolDir . $localId;

        $msg = self::improveMsgHead($msg, $outFile);

        self::write($outFile, json_encode($msg));
    }

    /**
     * Return localId well formated.
     *
     * @param string $exchange
     * @param string $routingKey
     *
     * @return string
     */
    private static function formatLocalId($exchange, $routingKey) {
        return microtime(true) . "." . substr(md5(rand()), 0, 8) . "@" . $routingKey . "@" . $exchange . ".v1";
    }

    /**
     * Add system vars to message head.
     *
     * @param array $msg
     * @param string $outFile
     *
     * @return array
     */
    private static function improveMsgHead(array $msg, $outFile) {
        if (false == array_key_exists("head", $msg))
            $msg['head'] = array();

        $msg['head']['x-msg2fs-fid'] = $outFile;
        $msg['head']['x-msg2fs-hn'] = gethostname();
        $msg['head']['x-msg2fs-dt'] = (new \DateTime())->format(DATE_ISO8601);

        return $msg;
    }

    /**
     * Write message to filesystem
     *
     * @param string $outFile
     * @param string $msg
     *
     * @throws \Exception
     */
    private static function write($outFile, $msg) {
        if (false === file_put_contents($outFile, $msg));
            throw new \Exception(sprintf('File %s not writable', $outFile));
    }

}
