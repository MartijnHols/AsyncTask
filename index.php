<?php

require(__DIR__ . '/config.php');

/*
CREATE TABLE IF NOT EXISTS `asynctask` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `closure` text NOT NULL,
  `addedOn` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `completedOn` datetime DEFAULT NULL,
  `result` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `completedOn` (`completedOn`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4;
 */

$db = connect();
if ($db->errno) {
	throw new Exception('No database connection possible: ' . $db->error);
}

$deleteWhenFinished = false;

function async($callback) {
	global $db;

	$serializer = new SuperClosure\Serializer(null, SUPERCLOSURE_SERIALIZER_SECRET_SIGNING_KEY);

	$closure = $serializer->serialize($callback);

	if ($updater = $db->prepare("INSERT INTO asynctask
			(closure)
			VALUES(?)")) {
		$updater->bind_param('s', $closure);
		$updater->execute();
	} else {
		throw new Exception('Prepare failed: (' . $db->errno . ') ' . $db->error);
	}
}

$to = 'asynctasktest@mailinator.com';
$subject = 'AsyncTask test';
$content = 'Example content';
async(function () use($to, $subject, $content) {
	mail($to, $subject, $content);
});