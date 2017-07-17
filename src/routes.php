<?php
// Routes
$app->get('/', function ($request, $response, $args) {
	// Sample log message
	$this->logger->info("index '/' route");

	// Render index view
	return $this->renderer->render($response, 'index.phtml', $args);
});
// Group Urls
$app->group('/api', function () use ($app) {

	// Version group
	$app->group('/v1', function () use ($app) {
		$app->get('/students', 'getStudents');
		$app->get('/student/{id}', 'getStudent');
		$app->post('/create', 'addStudent');
		$app->put('/update/{id}', 'updateStudent');
		$app->delete('/delete/{id}', 'deleteStudent');
	});
});
