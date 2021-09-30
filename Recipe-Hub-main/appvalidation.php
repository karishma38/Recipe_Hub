<?php
require_once "pdo.php";

if($_POST['action'] == "Login"){
    if ( isset($_POST['username']) && isset($_POST['password']) ) {
        $stmt = $pdo->prepare("SELECT * FROM users where username = :xyz");
        $stmt->execute(array(":xyz" => $_POST['username']));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ( $row === false ) {
        //   $_SESSION['error'] = 'username does not exists please SignUp';
        //   header( 'Location: login.php' ) ;
        //   return;
            echo json_encode(array('status' => "failed",'result' =>"Username does not exists"));
        }
        else{
          $username = $row['username'];
          $password = $row['pass'];
          if($password == $_POST['password']){
            // $_SESSION['username'] = $username;

            echo json_encode(array('status' => "OK",'result' =>"Login Successful", 'username' => $username));
            // return;
          }else{
            // $_SESSION['error'] = 'Incorrect password';
            // header( 'Location: login.php' ) ;
            // return;
            echo json_encode(array('status' => "failed",'result' =>"Incorrect password"));

          }
          
        }
            
    }
}
else if($_POST['action'] == "Signup"){
    if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])) {
        $stmt = $pdo->prepare("SELECT * FROM users where username = :xyz");
        $stmt->execute(array(":xyz" => $_POST['username']));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ( $row === false ) {
          $pic = "img/user.jpg";
          $sql = "INSERT INTO users (username, email, pass, pic) VALUES (:username, :email, :pass, :pic)";
          $stmt = $pdo->prepare($sql);
          $stmt->execute(array(
          ':username' => $_POST['username'],
          ':email' => $_POST['email'],
          ':pass' => $_POST['password'],
          ':pic' => $pic));
        //   $_SESSION['success'] = 'Account created';
        //   header( 'Location: login.php' ) ;
        //   return;
            echo json_encode(array('status' => "OK",'result' =>"Account created"));
        }
        else{
            echo json_encode(array('status' => "failed",'result' =>"Username already exists"));
        //   $_SESSION['error'] = 'username already exists';
        //   header( 'Location: signup1.php' ) ;
        //   return;
        }
        
      }
}