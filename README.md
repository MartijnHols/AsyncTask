# AsyncTask
An easy way with simple configurability to run tasks asynchronously in PHP. Tasks can be any (anonymous) functions you want, making it so you can keep your code together. Example:

	$to = 'asynctasktest@mailinator.com';
	$subject = 'AsyncTask test';
	$content = 'Example content';
	async(function () {
		mail($to, $subject, $content);
	});
	// Render response to use

# How it works
Tasks are serialized and stored in a database table for later use. A cronjob script (called `thread.php`) will fetch these tasks and execute them as soon as possible.

# Current state of project
Experimenting; proof of concept.
