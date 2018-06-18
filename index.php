<?php
/**
 * Created by PhpStorm.
 * User: Silas
 * Date: 05.06.2018
 * Time: 16:17
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Projekt Applikationssicherheit - M183</title>
  <link rel="stylesheet" href="./styling/normalize.css">
  <link rel="stylesheet" href="./styling/skeleton.css">
</head>
  <body>
    <h1>Projekt Applikationssicherheit - M183</h1>
    <hr>
    <h3>Login</h3>
    <form method="post" action="backend.php" name="login"><br>
      <label for="userLogin">User</label><input id="userLogin" type="text" name="userLogin"><br>
      <label for="passwordLogin">Password</label><input id="passwordLogin" type="password" name="passwordLogin">
      <input type="submit" name="login" value="Send">
    </form><br>
    <h3>Register</h3>
    <form method="post" action="backend.php" name="register"><br>
      <label for="userRegister">User</label><input id="userRegister" type="text" name="userRegister"><br>
      <label for="passwordRegister">Password</label><input id="passwordRegister" type="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{9,}"" 
      required title="9 or more characters, at least one number, and one uppercase and lowercase letter " name="passwordRegister">
      <input type="submit" name="register" value="Send">
    </form>
    <script src="validation.js"></script>
  </body>
</html>