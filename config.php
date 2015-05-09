<?php

//TODO: Move to private directory

// "jeremeamia/superclosure": "~2.0"
require(__DIR__ . '/vendor/autoload.php');

function connect() {
	return mysqli_connect('127.0.0.1', 'root', '', 'async_test');
}

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

class AsyncTask {
	const DEFAULT_PRIORITY = 0;

	private static $superClosureSerializerSecretSigningKey = '9e6f3d6c-c9fc-4c4f-a646-08d5d3c97824';

	/**
	 * @var callable A function that stores the task somewhere so it can be executed later.
	 */
	public $storeAsyncTask;

	/**
	 * @param string $closure
	 *
	 * @return mixed
	 */
	protected function storeTask($closure) {
		$store = $this->storeAsyncTask;
		return $store($closure);
	}

	/**
	 * @param callable $callable
	 * @param int $priority NotImplemented
	 *
	 * @throws Exception
	 */
	public function queue($callable, $priority = AsyncTask::DEFAULT_PRIORITY) {
		$serializer = new SuperClosure\Serializer(null, static::$superClosureSerializerSecretSigningKey);

		$closure = $serializer->serialize($callable);

		if (!static::storeTask($closure)) {
			// Failure could mean loss of data. We can't have that.
			throw new Exception('Failed to add the task to the queue. Storage failed.');
		}
	}

	public function __invoke($callable, $priority = AsyncTask::DEFAULT_PRIORITY) {
		$this->queue($callable, $priority);
	}
}

// Voor een lazy load systeem zoals dat van Yii
$queue = new AsyncTask();
$queue->storeAsyncTask = function ($closure) use ($db) {
	if ($updater = $db->prepare("INSERT INTO asynctask (closure) VALUES(?)")) {
		$updater->bind_param('s', $closure);
		$updater->execute();
		return true;
	} else {
		throw new Exception('Prepare failed: (' . $db->errno . ') ' . $db->error);
	}
};
