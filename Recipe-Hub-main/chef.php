<?php
  session_start();
  require_once "pdo.php";
  if($_GET['username'] == "Editors Choice"){
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    return;
  }

  $stmt = $pdo->prepare("SELECT * FROM users where username = :xyz");
  $stmt->execute(array(":xyz" => $_GET['username']));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  
?>
<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="img/recipehub.png" type="image/png">
  	<title>Recipe Hub</title>
 		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
     <link rel="stylesheet" href="recipe_styles.css">
<style>
.user{
    width: 80%;
    margin: 50px auto;
}
#userpic{
    width: 200px;
    border: 2px solid #686868;
    border-radius: 5px;
    margin-left: 10px;
    overflow: hidden;
}

</style>
</head>
<body>
<div id="sidenav">
<ul>
  <li><img src="img/recipehub.png" id="logoimg" height="70px" width="120px"></li>
  <li><a onclick="closeNav()" style="font-size: 1.5em;">&times;</a></li>
  <li><a href="index.php"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
  <li><a href="allrecipes.php"><i class='fa fa-cutlery' aria-hidden='true'></i> Recipes</a></li>
  <li><a href="about.php"><i class="fa fa-info-circle" aria-hidden="true"></i> About Us</a></li>
  
  <?php
      if(isset($_SESSION['username'])){
        echo "<li class='active'><a href='user.php'><i class='fa fa-cutlery' aria-hidden='true'></i> My recipes</a></li>";
        echo "<li><a href='logout.php'><i class='fa fa-sign-out' aria-hidden='true'></i> Log Out</a></li>";

      }else{
        echo "<li><a href='landing.php'><i class='fa fa-sign-in' aria-hidden='true'></i> Login</a></li>";
      }
  ?>
  
</ul>
</div>
<script>
  function openNav() {
  document.getElementById("sidenav").style.width = "100%";
}

function closeNav() {
  document.getElementById("sidenav").style.width = "0";
}
</script>
<div class="navbar">
  <div class="logo">
    <img src="img/recipehub.png" id="logoimg" height="70px" width="120px">
  </div>
  <button id="menu" onclick="openNav()"><i class="fa fa-bars" aria-hidden="true"></i></button>
<ul id="navlist">
  <li class="navlist"><a href="index.php"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
  <li class="navlist"><a href="allrecipes.php"><i class='fa fa-cutlery' aria-hidden='true'></i> Recipes</a></li>
  <li class="navlist"><a href="about.php"><i class="fa fa-info-circle" aria-hidden="true"></i> About Us</a></li>
  <li class="navlist" id='dropbtn'>
  <?php
      if(isset($_SESSION['username'])){
        echo "<a class='active'><i class='fa fa-user' aria-hidden='true'></i> welcome ".$_SESSION['username']." <i class='fa fa-angle-double-down' aria-hidden='true'></i></a>";
        echo "<div class='dropdown-content'>";
        echo "<a href='user.php'><i class='fa fa-cutlery' aria-hidden='true'></i> My recipes</a>";
        echo "<a href='logout.php'><i class='fa fa-sign-out' aria-hidden='true'></i> Log Out</a>";
        echo "</div>";
        
      }else{
        echo "<a href='landing.php'><i class='fa fa-sign-in' aria-hidden='true'></i> Login</a>";
      }
  ?>
  </li>
</ul>
</div>
<div class="user">
    <h1>Chef <?=$row['username']?></h1>
    
    <div id="userpic">
        <img src="<?=$row['pic']?>" alt="user pic" width="100%" height="100%">
    </div>
</div>

<div class="recipes">
    <center><h2><?=$row['username']?>'s recipes</h2></center>
    <div class="list">
    <?php
        $stmt = $pdo->prepare("SELECT * FROM `dishes` WHERE `uploded_by`=:xyz");
        $stmt->execute(array(":xyz" => $_GET['username']));
        $dishes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ( count($dishes) == 0) {
          echo ("<p style='margin:10px;'>".$row['username']." didn't added any recipes yet</p>");
        }
        else{
          foreach( $dishes as $dish )
          {
            ?>
            <div class="recipe_card">
                <div class="dish_img">
                    <a href="recipe.php?id=<?=$dish['id']?>">
                        <img class="dishimg" src="<?=$dish['image']?>" >
                    </a>
                </div>
                <div class="dish_info">
                  <div class="dishname" style="margin: 10px 0;position: relative;">
                        <span class="type">
                          
                          <?php
                            if($dish['type'] == 'Vegetarian'){
                              echo "<img src='img/veg.png' style='width: 100%;height: 100%'>";
                            }
                            else if($dish['type'] == 'Egg'){
                              echo "<img src='img/egg.png' style='width: 100%;height: 100%'>";
                            }
                            else{
                              echo "<img src='img/nonveg.png' style='width: 100%;height: 100%'>";
                            }
                          ?>
                        </span>
                        <h4 style="width: 85%;"><a href="recipe.php?id=<?=$dish['id']?>" class="dish_name"><?=$dish['name']?></a></h4>
                    <a class="chefname" href="#"><img src="img/chefhat.png" alt="" width="20" height="20"> By <?=$dish['uploded_by']?></a>
                    </div>
                    <div class="dish_metadata">
                        <div class="meta" style="background-color: #ffc42b;display: inline-block;padding: 5px 10px;border-radius: 20px;"><?=$dish['ready_in']?></div>
                        <div class="meta" style="background-color: #ffc42b;display: inline-block;padding: 5px 10px;border-radius: 20px;"><?=$dish['serve']?> serve</div>
                    </div>
                </div>
            </div>
                            
            <?php
          }
        }
    
    ?>
    
    
</div>
</div>
<?php
    include("footer.php");
?>
</body>
<script>
    user = <?=$_GET['username']?>;
    console.log(user);
</script>
</html>