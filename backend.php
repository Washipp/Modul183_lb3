<?php
/**
 * Created by PhpStorm.
 * User: Silas
 * Date: 05.06.2018
 * Time: 16:17
 */

/**
 * ================================
 *  LOGIN
 * ================================
 */

if(isset($_POST['login'])) {
  $user = $_POST['userLogin'];
  $password = $_POST['passwordLogin'];

  fileLog("User hat sich eingeloggt: ", $user);

  login($user, $password);

  fileLog("Session wurde gesetzt: ", $user);
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

  header('Location: http://localhost/modul183_lb3/index.php?success=true');
}

function newUser($name, $password){
  $stmt = connectToDb()->prepare( "INSERT INTO user(user, password)VALUES (?,?);");
  $stmt->bind_param('ss', $name, hashPassword($password));
  if($stmt->execute()){
    $stmt->close();
    return true;
  }else{
    $stmt->close();
    return false;
  }
}

function login($user, $password){
  if(password_verify($password, findPasswordByUser($user))){
    $_SESSION['userid'] = $user;
    echo "Login was successful!";
  }else{
    echo "Password is incorrect";
  }
}


function findPasswordByUser($user){
  $stmt = connectToDb()->prepare("SELECT password FROM user WHERE user = ?;");
  $stmt->bind_param( 's', $user);
  $stmt->execute();
  $stmt->bind_result($password);
  $stmt->fetch();
  $stmt->close();
  return $password;
}

function hashPassword($oldPassword) {
  return password_hash($oldPassword, PASSWORD_BCRYPT, ["cost" => 12]);
}

function connectToDb(){
  $serverName = "localhost";
  $userName = "root";
  $password = "root";
  $dbName = "modul183_lb3";
  $port = "8889";

  $conn =  new mysqli($serverName, $userName, $password, $dbName, $port );
  if (mysqli_connect_errno()) {
    printf("Connection Error: %s\n", mysqli_connect_error());
    exit();
  }

  mysqli_set_charset ( $conn, "utf8" );
  return $conn;
}

function fileLog($info, $text) {
  $log = "[" . $date = date('Y-m-d h:i:s a') . "] - " . $info . $text."\r\n";
  $myFile = fopen("logs/log.txt", "a");
  fwrite($myFile, $log);
  fclose($myFile);
}