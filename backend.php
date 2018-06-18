<?php
/**
 * Created by PhpStorm.
 * User: Silas
 * Date: 05.06.2018
 * Time: 16:17
 */

/**
 * ================================
 *  DATABASE CONNECTION
 * ================================
 */

function connectToDb(){
  $serverName = "127.0.0.1";
  $userName = "root";
  $password = "";
  $dbName = "modul183_lb3";
  $port = "3306";

  $conn =  new mysqli($serverName, $userName, $password, $dbName, $port ) or die("unable to connect to host"); ;
  if (mysqli_connect_errno()) {
    printf("Connection Error: %s\n", mysqli_connect_error());
    exit();
  }
  else if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  mysqli_set_charset ( $conn, "utf8" );
  return $conn;
}


/**
 * ================================
 *  REGISTER
 * ================================
 */

if(isset($_POST['register'])) {
  $user = $_POST['userRegister'];
  $password = $_POST['passwordRegister'];

  newUser($user, $password);
  fileLog("User hat sich registriert: ", $user);

  login($user, $password);
  fileLog("Session wurde gesetzt: ", $user);

  header($user + ' ' + $password);
}

function newUser($name, $password){
  mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
  $conn = connectToDb();
  $stmt = $conn->prepare( "INSERT INTO user(user, password)VALUES (?,?);");
  $hashedPassword = hashPassword($password);
  $stmt->bind_param('ss', $name, $hashedPassword);
  $stmt->execute();
  $stmt->close();
  $conn->close();
}

function hashPassword($oldPassword) {
  $pepper = "ThisIsARealySecureLongPassword123456789";
  $newPassword = $oldPassword . $pepper;
  return password_hash($newPassword, PASSWORD_BCRYPT, ["cost" => 12]);
}


/**
 * ================================
 *  LOGIN
 * ================================
 */

if(isset($_POST['login'])) {
  $user = $_POST['userLogin'];
  $password = $_POST['passwordLogin'];
  
  $attempts = countAttemptsForUser($user);


  if(login($user, $password)){
    echo "Login was successful!";
    fileLog("User hat sich eingeloggt: ", $user);
    $_SESSION['userid'] = $user;
    fileLog("Session wurde gesetzt: ", $user);
  }elseif(is_null($attempts) || $attempts <=5){
    echo "Login not successful, try again.";
    $attempts += 1;
    addAttempt($user, $attempts);
    fileLog("Failed login: ", $user);
  }else
  {
    echo "Your account got deactivated. Please ask you admin for help. ";
    fileLog("User account deactivated: ", $user);
  }

  
}

function login($user, $password){

  $pepper = "ThisIsARealySecureLongPassword123456789";

  if(password_verify($password . $pepper, findPasswordByUser($user))){
    return true;
  }else{
    echo "Password is incorrect";
    return false;
  }
}


function findPasswordByUser($user){
  $conn = connectToDb();
  $stmt = $conn->prepare("SELECT password FROM user WHERE user = ?;");
  $stmt->bind_param( 's', $user);
  $stmt->execute();
  $stmt->bind_result($password);
  $stmt->fetch();
  $stmt->close();
  $conn->close();
  return $password;
}

function countAttemptsForUser($user){
  $conn = connectToDb();
  $stmt = $conn->prepare("SELECT attempt FROM user WHERE user = ?;");
  $stmt->bind_param( 's', $user);
  $stmt->execute();
  $stmt->bind_result($attempt);
  $stmt->fetch();
  $stmt->close();
  $conn->close();
  return $attempt;
}

function addAttempt($user, $attempt){
  $conn = connectToDb();
  $stmt = $conn->prepare( "UPDATE user SET attempt = ? WHERE user = ?");
  $stmt->bind_param('ss', $attempt, $user);
  $stmt->execute();
  $stmt->close();
  $conn->close();
}


/**
 * ================================
 *  LOGGING
 * ================================
 */

function fileLog($info, $text) {
  $log = "[" . $date = date('Y-m-d h:i:s a') . "] - " . $info . $text."\r\n";
  $myFile = fopen("logs/log.txt", "a");
  fwrite($myFile, $log);
  fclose($myFile);
}
