<?php
  session_start();
  require_once "pdo.php";
	error_reporting(E_ERROR | E_WARNING | E_PARSE);

  $stmt = $pdo->prepare("SELECT * FROM `dishes` WHERE `id` = :id");
  $stmt->execute(array(":id" => $_GET['id']));
  $dish = $stmt->fetch(PDO::FETCH_ASSOC);

  $stmt = $pdo->prepare("SELECT * FROM `directions` WHERE `dish_id` = :id ORDER BY `directions`.`step` ASC ");
  $stmt->execute(array(":id" => $_GET['id']));
  $directions = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $stmt = $pdo->prepare("SELECT * FROM `ingredients` WHERE `dish_id` = :id ORDER BY `ingredients`.`rank` ASC ");
  $stmt->execute(array(":id" => $_GET['id']));
  $ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="img/recipehub.png" type="image/png">
  <title><?= $dish['name'] ?>-Recipe Hub</title>
 		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
     <link rel="stylesheet" href="recipe_styles.css">
	<style>
@import url('https://fonts.googleapis.com/css2?family=Signika:wght@600&display=swap');

.dishtitle{
  position: relative;
  text-align: center;
  background-size: cover;
  background-position: right;
  color: #fff;
  height: 400px;
  font-family: 'Signika', sans-serif;
}

#name{
  position: absolute;
  top: 50%;
  left: 50%;
  width: 95%;
  transform: translate(-50%, -50%);
  font-size: 2.5rem;
}
#type{
  width: 50px;
  height: 50px;
}
.cname{
  color: #fff;
  font-weight: 400;
  font-size: 30px;
  text-decoration: none;
  display: block;
}
.cname:hover{
    color: #528f2d;
}
.operation{
  height: 40px;
  width: 90px;
  border: none;
  border-radius: 5px;
  z-index: 2;
  font-size: 16px;
  outline: none;
}
.del{
  background-color: #d9534f;
}
.del:hover{
  background-color: #c9302c;
  transform: scale3d(1.1,1.1,1);
  -webkit-transform: scale3d(1.1,1.1,1);
}
.update{
  background-color: #59a9ff;
}
.update:hover{
  background-color: #007bff;
  transform: scale3d(1.1,1.1,1);
  -webkit-transform: scale3d(1.1,1.1,1);
}
.main{
  width: 70%;
  margin: 0px auto;
}

.dishinfo{
  display: flex;
  flex-wrap: nowrap;
  margin-top: 20px;
  justify-content: space-between;
}
.data{
  padding: 30px 0;
  font-size: 15px;
  line-height: 25px;
  color: #686868;
}
#dishdata{
  min-width: 330px;
  padding: 15px;
}

.info-recipe{
  border-bottom: 2px dotted #080808;
  padding: 15px 0;
  font-weight: 600;
  color: #3A3A3A;
}
.info-recipe:last-child{
  border-bottom: none;
}

.values{
  float: right;
letter-spacing: normal;
color: #3A3A3A;
font-weight: 400;
}
.caption{
  font-family: 'Signika', sans-serif;
  font-size: 40px;
  line-height: 40px;
margin-bottom: 25px;
color: #3A3A3A;
    
}
.media{
  width: 700px;
}

/* comment */
#comments-box{
  margin: 30px 0;
}
.comment{
  display: flex;
  flex-wrap: nowrap;
  margin-bottom: 10px;
}
.comment .user-img{
  width: 55px;
  height: 55px;
  border-radius: 50%;
  overflow: hidden;
  margin-right: 5px;
}
.sender{
  margin: 5px 0;
  color: #686868;
  font-size: 14px;
}
#comment{
  width: 100%;
  height: 50px;
  padding: 15px;
  margin: 8px 0;
  border: 2px solid #bfbfbf;
  border-radius: 10px;
  outline: none;
}
#submit_btn{
  background-color: black;
  color: white;
  padding: 12px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
  font-style: italic;
  font-size: 16px;
  outline: none;
}
#submit_btn:hover {
  background-color: green;
}

@media (max-width: 768px) and (min-width: 426px){
  .dishtitle{
    height: 300px;
  }
  .main{
  width: 90%;
  }
  h1{
    font-size: 1.5em;
  }
  .cname{
    font-size: 25px;
  }
  .media{
    width: 80%;
  }
}
@media (max-width: 426px){
  .dishtitle{
    height: 300px;
  }
  .dishinfo{
    display: block;
  }
  #dishdata {
    min-width: 300px;
    padding: 15px 0;
  }
  .media{
    width: 100%;
  }
  h1{
    font-size: 1.2em;
  }
  .cname{
    font-size: 25px;
  }
  #type{
  width: 35px;
  height: 35px;
  }
  .comment .user-img {
    width: 50px;
    height: 50px;
  }
  .sender{
    margin-bottom: 0;
  }
}
	</style>

</head>

<body>
<div id="sidenav">
<ul>
  <li><img src="img/recipehub.png" id="logoimg" height="70px" width="120px"></li>
  <li><a onclick="closeNav()" style="font-size: 1.5em;">&times;</a></li>
  <li><a href="index.php"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
  <li class="active"><a href="allrecipes.php"><i class='fa fa-cutlery' aria-hidden='true'></i> Recipes</a></li>
  <li><a href="about.php"><i class="fa fa-info-circle" aria-hidden="true"></i> About Us</a></li>
  
  <?php
      if(isset($_SESSION['username'])){
        echo "<li><a href='user.php'><i class='fa fa-cutlery' aria-hidden='true'></i> My recipes</a></li>";
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
  <li class="navlist"><a class='active' href="allrecipes.php"><i class='fa fa-cutlery' aria-hidden='true'></i> Recipes</a></li>
  <li class="navlist"><a href="about.php"><i class="fa fa-info-circle" aria-hidden="true"></i> About Us</a></li>
  <li class="navlist" id='dropbtn'>
  <?php
      if(isset($_SESSION['username'])){
        echo "<a><i class='fa fa-user' aria-hidden='true'></i> welcome ".$_SESSION['username']." <i class='fa fa-angle-double-down' aria-hidden='true'></i></a>";
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

<div class="dishtitle" style="background-image: url('<?=$dish['image']?>');">
  <div class="overlay" style="background-color:#00000075;height: 100%;">
    <div id="name">
      <h1><?= $dish['name'] ?>
      <span>
                          
        <?php
          if($dish['type'] == 'Vegetarian'){
            echo "<img src='img/veg.png'  id='type'>";
          }
          else if($dish['type'] == 'Egg'){
            echo "<img src='img/egg.png'  id='type'>";
          }
          else{
            echo "<img src='img/nonveg.png'  id='type'>";
          }
        ?>
      </span></h1>
      
      <a class="cname" href="chef.php?username=<?=$dish['uploded_by']?>"> By <?=$dish['uploded_by']?></a>
      <?php
        if($_SESSION['username'] == $dish['uploded_by']){
          ?>
          
          <a href="edit.php?id=<?=$dish['id']?>"><button class="operation update" ><i class="fa fa-pencil" aria-hidden="true"></i> Edit</button></a>
          <a href="delete.php?id=<?=$dish['id']?>" Onclick="return confirm('Are you sure you want to delete <?=$dish['name']?> recipe?');"><button class="operation del" ><i class="fa fa-trash" aria-hidden="true"></i> Delete</button></a>
              
          
          <?php
        }
      ?>
    </div>
  </div>
</div>

<div class="main">

  <div class="dishinfo">
    <div class="data">
      <h3 class="caption">Description</h3>
      <p><?= $dish['discription'] ?></p>
    </div>
    <div id="dishdata">
      <p class="info-recipe"><i class="fa fa-folder"></i> Category<span class="values"><?= $dish['category'] ?></span></p>
      <p class="info-recipe"><i class="fa fa-hourglass-half"></i> Preparation Time<span class="values"><?= $dish['prep'] ?></span></p>
      <p class="info-recipe"><i class="fa fa-hourglass-half"></i> Total Time<span class="values"><?= $dish['ready_in'] ?></span></p>
      <p class="info-recipe"><i class="fa fa-tachometer"></i> Difficulty<span class="values"><?= $dish['level'] ?></span></p>
      <p class="info-recipe"><i class="fa fa-cutlery"></i> Yields<span class="values"><?= $dish['serve'] ?> Servings</span></p>
    </div>
  </div>
      
  <div class="data">
    <h3 class="caption">Ingredients</h3>
    <ul style="list-style-type: none;">
        <?php
          $sr = 1;
          foreach($ingredients as $ingredient){
            echo "<li><span>".$sr."]</span> ".$ingredient['ingredients']." </li>";
            $sr++;
          }
        ?>
      </ul>
  </div>
          
              
  <div class="data">
    <h3 class="caption">Preparation</h3>
    <ul style="list-style-type: none;">
        <?php
          $sr = 1;
          foreach($directions as $direction){
            echo "<li><span>".$sr."]</span> ".$direction['directions']." </li>";
            $sr++;
          }
        ?>
    </ul>
  </div>

  <div class="data">
    <h3 class="caption">Nutrition Facts</h3>
    <p><?= $dish['nutritions'] ?></p>
  </div>

  <div class="data">
    <h3 class="caption">Media</h3>
    <img src="<?=$dish['image']?>" class="media" >
  </div> 
  <hr>
  <div id="comments-box">
  <h3 class="caption">Reviews</h3>
  <div id="display-comments">
    
  </div>
  </div>
  <div id="addcoments">
    <?php
      $set = "disabled";
      if($_SESSION['username']){
        $set = "";
      }
      else{
        echo "<p style='color:#686868;'>You need to login to add comment</p>";
      }
    ?>
    <form method="post" id="comment-form">
      <input type="hidden" name="id" value="<?=$_GET['id']?>">
      <input type="hidden" name="sender" value="<?=$_SESSION['username']?>">
      <input type="text" name="comment" id="comment" placeholder="Enter your review.." <?= $set ?> required>
      <input type="submit" value="submit" name="submit" id="submit_btn" <?= $set ?>>
    </form>
  </div>
</div>
<?php
    include("footer.php");
?>
</body>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
$(document).ready(function(){
 
 $('#comment-form').on('submit', function(event){
   console.log("submited");
  event.preventDefault();
  var form_data = $(this).serialize();
  $.ajax({
   url:"add_comment.php",
   method:"POST",
   data:form_data,
   success:function(data)
   {
     $('#comment-form')[0].reset();
     console.log(data);
     load_comment();
   }
  })
 });

 load_comment();

 function load_comment()
 {
  $.ajax({
   url:"display-comment.php?id=<?=$_GET['id']?>",
   method:"POST",
   success:function(data)
   {
      $('#display-comments').html(data);
   }
  })
 }

});
</script>
</html>