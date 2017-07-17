<?php
if (PHP_SAPI == 'cli-server') {
	// To help the built-in PHP dev server, check if the request was actually for
	// something which should probably be served as a static file
	$url = parse_url($_SERVER['REQUEST_URI']);
	$file = __DIR__ . $url['path'];
	if (is_file($file)) {
		return false;
	}
}

require __DIR__ . '/../vendor/autoload.php';

session_start();

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);
$corsOptions = array(
	"origin" => "*",
	"exposeHeaders" => array("Content-Type", "X-Requested-With", "X-authentication", "X-client"),
	"allowMethods" => array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'),
);
$cors = new \CorsSlim\CorsSlim($corsOptions);

$app->add($cors);
// Set up dependencies
require __DIR__ . '/../src/dependencies.php';

// Register middleware
require __DIR__ . '/../src/middleware.php';

// Register routes
require __DIR__ . '/../src/routes.php';

// Run app
$app->run();

function getStudents() {
	$sql = "SELECT * FROM students";
	try {
		$stmt = getConn()->query($sql);
		$wines = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;

		return json_encode($wines);
	} catch (PDOException $e) {
		echo '{"error":{"text":' . $e->getMessage() . '}}';
	}

}

function getStudent($request) {
	$id = 0;
	$id = $request->getAttribute('id');
	if (empty($id)) {
		echo '{"error":{"text":"Id is empty"}}';
	}
	try {
		$db = getConn();
		$sth = $db->prepare("SELECT * FROM students WHERE id=$id");
		$sth->bindParam("id", $args['id']);
		$sth->execute();
		$todos = $sth->fetchObject();
		return json_encode($todos);
	} catch (PDOException $e) {
		echo '{"error":{"text":' . $e->getMessage() . '}}';
	}
}
function addStudent($request) {
	$stu = json_decode($request->getBody());

	$sql = "INSERT INTO students (f_name, l_name, age,class) VALUES (:f_name, :l_name,:age,:class)";
	try {
		$db = getConn();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("f_name", $stu->f_name);
		$stmt->bindParam("l_name", $stu->l_name);
		$stmt->bindParam("age", $stu->age);
		$stmt->bindParam("class", $stu->class);
		$stmt->execute();
		$stu->id = $db->lastInsertId();
		$db = null;
		echo json_encode($stu);
	} catch (PDOException $e) {
		echo '{"error":{"text":' . $e->getMessage() . '}}';
	}
}

function updateStudent($request) {
	$stu = json_decode($request->getBody());
	$id = $request->getAttribute('id');
	$sql = "UPDATE students SET f_name=:f_name,l_name=:l_name, age=:age, class=:class WHERE id=:id";
	try {
		$db = getConn();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("f_name", $stu->f_name);
		$stmt->bindParam("l_name", $stu->l_name);
		$stmt->bindParam("age", $stu->age);
		$stmt->bindParam("class", $stu->class);
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$db = null;
		echo json_encode($stu);
	} catch (PDOException $e) {
		echo '{"error":{"text":' . $e->getMessage() . '}}';
	}
}

function deleteStudent($request) {
	$id = $request->getAttribute('id');
	$sql = "DELETE FROM students WHERE id=:id";
	try {
		$db = getConn();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("id", $id);
		//$stmt->execute();
		$db = null;
		echo '{"error":{"text":"successfully! deleted Records"}}';
	} catch (PDOException $e) {
		echo '{"error":{"text":' . $e->getMessage() . '}}';
	}
}

function getConn() {
	$dbhost = "localhost";
	$dbuser = "root";
	$dbpass = "";
	$dbname = "student";
	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $dbh;
}