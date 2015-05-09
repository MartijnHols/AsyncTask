# AsyncTask
An easy way with simple configurability to run tasks asynchronously in PHP. Tasks can be any (anonymous) functions you want, making it so you can keep your code together. Example:

	$to = 'asynctasktest@mailinator.com';
	$subject = 'AsyncTask test';
	$content = 'Example content';
	async(function () use($to, $subject, $content) {
		// This could take a while, especially if it has attachments
		mail($to, $subject, $content);
	});
	// But we don't have to wait! It will happen very soon in a background queue.
	// Render response to user

# How it works
Tasks are serialized and stored in a database table for later use. A cronjob script (called `thread.php`) will fetch these tasks and execute them as soon as possible.

# Current state of project
Experimenting; proof of concept.
