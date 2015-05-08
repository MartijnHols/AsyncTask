<?php

require(__DIR__ . '/config.php');

/*
CREATE TABLE IF NOT EXISTS `asynctask` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`closure` text NOT NULL,
`addedOn` datetime NOT NULL,
`completedOn` datetime DEFAULT NULL,
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
			(closure, addedOn)
			VALUES(?, NOW())")) {
		$updater->bind_param('s', $closure);
		$updater->execute();
	} else {
		throw new Exception('Prepare failed: (' . $db->errno . ') ' . $db->error);
	}
}
/*string 'C:32:"SuperClosure\SerializableClosure":134:{a:5:{s:4:"code";s:40:"function () {
    return 'pindakaas';
};";s:7:"context";a:0:{}s:7:"binding";N;s:5:"scope";N;s:8:"isStatic";b:0;}}' (length=180)*/
async(function () {
	throw new Exception('Oops! Something broke.');
});