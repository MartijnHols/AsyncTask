<?php

require(__DIR__ . '/config.php');
/**
 * @global $queue
 */

$to = 'asynctasktest@mailinator.com';
$subject = 'AsyncTask test';
$content = 'Example content';
$queue(function () use($to, $subject, $content) {
	mail($to, $subject, $content);
});
