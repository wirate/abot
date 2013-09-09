<?php

// :pnx!~pnx@P2PNET-A658E30D.a259.priv.bahnhof.se INVITE serveher :#testchanofdoom
// ERR_INVITEONLYCHAN when trying to join

/**
 * Relay_Adapter_Socket
 */
require_once 'Relay/Adapter/Socket.php';

require_once 'Relay/Irc/Message.php';

/**
 * Performs the task of a IRC client. mostly by dispatching task's
 * to other objects more suitable for the role.
 */
class Relay_Client
{
    /**
     * Socket object
     *
     * @var Relay_Network_Interface
     */
    protected $adapter = null;

    /**
     * Config variables
     *
     * @var array
     */
    protected $config = array(
        'ip' => '127.0.0.1',
        'port' => 6667,
        'password' => '',
        'use_ssl' => false,
        'nick' => 'Relay',
        'hostname' => '*',
        'servername' => '0',
        'ident' => 'Relay',
        'realname' => 'Relay',
        'channels' => '',
        'reconnect' => false,
        'automsg' => false
    );

    public function __construct(array $config = null)
    {
        $this->adapter = new Relay_Adapter_Socket;

        if ($config !== null) {
            $this->setConfig($config);
        }
    }

    public function setConfig(array $config)
    {
        foreach($config as $key => $val) {
            $key = strtolower($key);
            if (!array_key_exists($key, $this->config)) {
                throw new Exception("invalid key $key");
            }
            $this->config[$key] = $val;
        }
    }

    public function getChannels()
    {
        return $this->config['channels'];
    }

    public function connect()
    {
        $conf = $this->config;

        $this->adapter->connect($conf['ip'], $conf['port'], ($conf['use_ssl']) ? 'ssl' : 'tcp');

        if (strlen($conf['password'])) {
            $msg = new Relay_Irc_Message('PASS');
            $msg->setParam($conf['password']);
            $this->write($msg->getMessage());
        }

        // Begin forming NICK message
        $msg = new Relay_Irc_Message('NICK');
        $msg->setParam($conf['nick']);

        $this->write($msg->getMessage());

        // form USER message
        $msg->setCommand('USER');

        $msg->setParam($conf['hostname']);
        $msg->setParam($conf['servername']);
        $msg->setTrail($conf['realname']);

        $this->write($msg->getMessage());

        // TODO: connection is not done, need to check socket for handshake and autojoin channels.

        return $this;
    }

    public function write($data)
    {
        $this->adapter->write($data);
    }

    public function read()
    {
        try {
            $response = $this->adapter->read(512);
        } catch (Exception $e) {
            if ($this->config['reconnect']) {
                $this->connect();
                return;
            }
            throw $e;
        }

        // nothing to read, return.
        if (strlen($response) < 1) {
            return;
        }

        $response = Relay_Irc_Message::fromString($response);

        switch($response->getCommand()) {
        case 'PING':
            // respond to ping
            $this->write("PONG :{$response->getTrail()}\n");
            break;
        case 'INVITE' :
            if (in_array($response->getTrail(), $this->config['channels'])) {
                $msg = new Relay_Irc_Message('JOIN');
                $msg->setParam($response->getTrail());
                echo "INVITE: {$msg->getMessage()}";
                $this->write($msg->getMessage());
            }
            break;
        case 'RPL_ENDOFMOTD':
        case 'ERR_NOMOTD':
            $msg = new Relay_Irc_Message('JOIN');
            $msg->setParam(implode(',', (array) $this->config['channels']));

            echo $msg->getMessage();
            $this->write($msg->getMessage());

            if (is_array($this->config['automsg'])) {
                $msg = new Relay_Protocol_Message('PRIVMSG');

                foreach($this->config['automsg'] as $cmsg) {

                    $cut = strpos($cmsg, ' ');

                    if ($cut === false) {
                        continue;
                    }

                    $target = substr($cmsg, 0, $cut);
                    $message = substr($cmsg, $cut + 1);

                    if (strlen($message) < 1) {
                        continue;
                    }

                    $msg->resetParams();
                    $msg->setParam($target);
                    $msg->setTrail($message);
                    echo "AUTOMSG: $target -> $message\n";
                    $this->write($msg->getMessage());
                }
            }
        }

        return $response;
    }
}
