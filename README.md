# PHP Restful API Framework SLIM

Slim is a PHP micro framework that helps you quickly write simple and powerful web applications and APIs

<h1>How to configure Slim framework on XAMPP/WAMP</h1>
<p>You need to download Slim framework and unzip this,Now we will paste into <code>XAMPP/htdocs</code> or <code>wamp/www</code> folder.</p>
<p>I am assuming you have composer install, if not please <code>install composer</code> on your system.Please open XAMPP/htdocs/slim folder into cmd line and run below command</p>

<p><code>composer update</code></p>

<h1>How to connect MySQL with Slim</h1>
<p>I am using MYSQL database for this slim framework.I will create <code>student</code> in mysql using phpMyAdmin UI or MySQL server.We will run below SQL query into <code>student</code> database,</p>

~~~php
CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `f_name` varchar(255) NOT NULL,
  `l_name` varchar(255) NOT NULL,
  `age` int(11) NOT NULL,
  `class` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
~~~

<p>We will create db connection method in <code>index.php</code> file,You can create separate db connection file and include into <code>index.php</code> as well, Open <code>slim/public/index.php</code> file and added below code in the end of file.</p>

~~~php
function getConn() {
    $dbhost="loclahost";
    $dbuser="root";
    $dbpass="";
    $dbname="student";
    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;
}
~~~

<h1>HTTP Rest Service/Operation</h1>
<p>I will create following rest end points in this slim demo,We will create GET,POST,PUT and DELETE type request.</p>
<table class="table"><thead class="thead-inverse"><tr><th>#</th><th>Route</th><th>Method</th><th>Type</th><th>Full route</th><th>Description</th></tr></thead><tbody><tr><th scope="row">1</th><td>/students</td><td>GET</td><td>JSON</td><td> <a href="http://localhost/slim/public/api/v1/students/" class="link-red" target="_blank">http://localhost/slim/public/api/v1/students/</a></td><td>Get all students data</td></tr><tr><th scope="row">2</th><td>/employee/{id}</td><td>GET</td><td>JSON</td><td> <a href="javascript:void(0)" class="link-red" target="_blank">http://localhost/slim/public/api/v1/student/1</a></td><td>Get a single student data</td></tr><tr><th scope="row">3</th><td>/create</td><td>POST</td><td>JSON</td><td> <a href="javascript:void(0)" class="link-red" target="_blank">http://localhost/slim/public/api/v1/create</a></td><td>Create new record in database</td></tr><tr><th scope="row">4</th><td>/update/{id}</td><td>PUT</td><td>JSON</td><td> <a href="javascript:void(0)" class="link-red" target="_blank">http://localhost/slim/public/api/v1/update/1</a></td><td>Update an student record</td></tr><tr><th scope="row">5</th><td>/delete/{id}</td><td>DELETE</td><td>JSON</td><td> <a href="javascript:void(0)" class="link-red" target="_blank">http://localhost/slim/public/api/v1/delete/1</a></td><td>Delete an student record</td></tr></tbody></table>

<h1>Create HTTP routes</h1>
<p>We are planning to create above rest call into this example so we will add below rest end points into <code>slim/src/routes.php</code> file,</p>

~~~php
$app->group('/api', function () use ($app) {
	$app->group('/v1', function () use ($app) {
		$app->get('/students', 'getStudents');
		$app->get('/student/{id}', 'getStudent');
		$app->post('/create', 'addStudent');
		$app->put('/update/{id}', 'updateStudent');
		$app->delete('/delete/{id}', 'deleteStudent');
	});
});
~~~

<h1>Get all records from students Database</h1>
<p>We will create new method <code>getStuents()</code> into <code>slim/public/index.php</code> file and return JSON results.This method will get all students details from <code>students</code> table.</p>

~~~php
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
~~~

<p>now test rest call http://localhost/slim/api/v1/students using browser , it will show students list json results data.</p>

<h1>Add New Student record using MySQL and Slim Restful API Framework</h1>
<p>We will create new HTTP Post method <code>addStudent()</code> into <code>slim/public/index.php</code> file. This methos will create new record into MySQL database.You need to send data into json format.</p>

~~~php
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
~~~

<h1>Update Student record using MySQL and Slim Restful API Framework</h1>
<p>We will create new HTTP PUT method <code>updateStudent()</code> into <code>slim/public/index.php</code> file. This methos will update record into MySQL database.You need to send data student data with ID into json format.</p>

~~~php
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
~~~

<h1>Delete Student record using MySQL and Slim Restful API Framework</h1>
<p>We will create new HTTP DELETE method <code>deleteStudent()</code> into <code>slim/public/index.php</code> file. This methos will delete student record into MySQL database.You need to send data student data with ID into json format.</p>

~~~php
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
~~~

<h1>How to Fix cross-domain request in Slim</h1>
<p>sometimes, we are using cross domain rest call , so client will get error on console about cross domain request etc etc..? We can fix that issue easily in slim framework by corsslim plugin.</p>

<p>First we will include <code>"palanik/corsslim": "dev-slim3"</code> into composer file and run again <code>'composer update'</code> command.We will write some CORS configuration code into <code>public\index.php</code> file.</p>

~~~php
$corsOptions = array(
    "origin" => "*",
    "exposeHeaders" => array("Content-Type", "X-Requested-With", "X-authentication", "X-client"),
    "allowMethods" => array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS')
);
$cors = new \CorsSlim\CorsSlim($corsOptions);
~~~

<h1>Conclusion</h1>
<p>This Slim API Framework demo has basic crud table operation. You can extend this Slim example as per your need and enjoy.</p>

