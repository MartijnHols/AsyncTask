<?php

require(__DIR__ . '/config.php');

$to = 'asynctasktest@mailinator.com';
$subject = 'AsyncTask test';
$content = 'Example content';
AsyncTask::queue(function () use($to, $subject, $content) {
	mail($to, $subject, $content);

	$storeAsyncTask = AsyncTask::$storeAsyncTask;
	$storeAsyncTask('test');
});
