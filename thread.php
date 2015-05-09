<?php

//TODO: Move to private directory

require(__DIR__ . '/config.php');

function logException(Exception $e) {
	//NYI
	throw $e;
}

//TODO: Start a new process that executes each task seperately? Might work better if a task has a sleep(x), or even if it's long running without that
function executeIteration() {
	try {
		// Reconnect every time to prevent crashes when connection messes up
		$db = connect();
		if ($db->errno) {
			// Doesn't matter, try again later!
			logException(new Exception('Connection failed: (' . $db->errno . ') ' . $db->error));
			return false;
		}
		$serializer = new SuperClosure\Serializer(null, SUPERCLOSURE_SERIALIZER_SECRET_SIGNING_KEY);

		$query = $db->query('SELECT id, closure FROM asynctask WHERE completedOn IS NULL');
		while ($row = $query->fetch_object()) {
			try {
				$closure = $serializer->unserialize($row->closure);
				$result = $closure();
			} catch (Exception $e) {
				$result = serialize($e);
			}
			if ($updater = $db->prepare("UPDATE asynctask SET completedOn=NOW(), result=? WHERE id=?")) {
				$updater->bind_param('si', $result, $row->id);
				$updater->execute();
			} else {
				logException(new Exception('Prepare failed: (' . $db->errno . ') ' . $db->error));
			}
		}
		$query->close();
		$db->close();
	} catch (Exception $e) {
		logException($e);
		return false;
	}
	return true;
}

$i = 0;
while ($i++ < 5) {
	executeIteration();

	sleep(1);
}
