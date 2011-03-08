#!/usr/bin/php
<?php

// get queue from preaxbot
$queue = msg_get_queue(ftok('./init.php', 'r'));

unset($argv[0]);
$cmd = implode(" ", $argv);

echo "SENDING !$cmd\n";

msg_send($queue, 1, "!$cmd", false, false);
