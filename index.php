<?php

define('SUPERCLOSURE_SERIALIZER_SECRET_SIGNING_KEY', '9e6f3d6c-c9fc-4c4f-a646-08d5d3c97824');

// "jeremeamia/superclosure": "~2.0"
require(__DIR__ . '/vendor/autoload.php');

use SuperClosure\Serializer;

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

mysql_connect('127.0.0.1', 'root', '');
mysql_select_db('async_test');

$deleteWhenFinished = false;

function async($callback) {
	$serializer = new Serializer(null, SUPERCLOSURE_SERIALIZER_SECRET_SIGNING_KEY);

	$closure = $serializer->serialize($callback);

	mysql_query("INSERT INTO asynctask
		(closure, addedOn)
		VALUES('" . mysql_real_escape_string($closure) . "', NOW())");
}
/*string 'C:32:"SuperClosure\SerializableClosure":134:{a:5:{s:4:"code";s:40:"function () {
    return 'pindakaas';
};";s:7:"context";a:0:{}s:7:"binding";N;s:5:"scope";N;s:8:"isStatic";b:0;}}' (length=180)*/
async(function () {
	return 'pindakaas';
});