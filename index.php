<!DOCTYPE html>

<?php
  if(session_status() == PHP_SESSION_ACTIVE) session_start();
  $message = null;

  $getConn = function(){
    $host = "localhost";  
    $username = "root";  
    $password = "password";  
    $database = "bdd";

    try {
      return new PDO("mysql:host=$host; dbname=$database", $username, $password);
    } catch (\Throwable $th) {
      return null;
    }
    
  };

  $getUser = function($login, $conn){
    $query = "SELECT * FROM users WHERE username = :username";  
    $stmt = $conn->prepare($query);  
    $stmt->bindParam("username", $login);
    $stmt->execute();

    $count = $stmt->rowCount();  
    if($count > 0) {
      return $stmt->fetchAll()[0];
    }

    return null;
  };

  $checkLogin = function($login, $password, $getUser){
    if(!$login) return "Missing login";
    if(!$password) return "Missing password";

    $user = $getUser($login);

    if(!$user || !password_verify($password, $user['password'])) return "Invalid credentials";
    
    $_SESSION["name"] = $user['username'];
    return null;
  };

  if (isset($_POST["login"])){
    $message = $checkLogin($_POST["login"] ?? null, $_POST["password"] ?? null, fn($v)=>$getUser($v,$getConn()));
  }


  if(isset($_POST['disconnect'])){
    unset($_SESSION['name']);
  }
?>


<html>
  <head>
      <title>LOGIN</title>
  </head>
  <body>

    <?php if (!is_null($message)) { ?>

      <p><?= $message ?></p>

    <?php } ?>

    <?php if (isset($_SESSION['name'])) { ?>

      <p>Bonjour monsieur <?= $_SESSION['name'] ?></p>
      <form action="index.php" method="post">
        <input type="hidden" name="disconnect" />
        <button type="submit">Disconnect</button>
      </form>

    <?php } ?>

    <?php if (!isset($_SESSION['name'])) { ?>
      
        <form action="index.php" method="post">
          <h2>LOGIN</h2>

          <label>User Name</label>
          <input type="text" name="login" placeholder="User Name" /><br/>

          <label>Password</label>
          <input type="password" name="password" placeholder="Password" /><br/> 

          <button type="submit">Login</button>
        </form>

    <?php } ?>
  </body>
</html>