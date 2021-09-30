<?php
  session_start();
   require_once "pdo.php";

    if(!isset($_SESSION['username'])){
        header( 'Location: landing.php' ) ;
        return;
    }
    

    if (isset($_POST['submit'])){

     	if($_POST['prep']=='other_time'){
     		$prep=$_POST['text_prep'];
     	}else{
     		$prep=$_POST['prep'];
     	}

		  if($_POST['ready']=='other_time'){
     		$ready=$_POST['text_ready'];
     	}else{
     		$ready=$_POST['ready'];
     	}

     	if($_POST['serving']=='other_time'){
     		$serve=$_POST['text_serving'];
     	}else{
     		$serve=$_POST['serving'];
      }
      $target = $_POST['address'];

      if($_FILES['img']['tmp_name']){
        if (move_uploaded_file($_FILES['img']['tmp_name'], $target)) {
            $msg = true;
        }else{
            $msg = false;
        }
      }
			$query = "UPDATE `dishes` SET `name` = :name, `uploded_by` = :uploded_by, `prep`=:prep, `type` = :type, `ready_in`= :ready_in, `category` = :category, `serve` = :serve, `level` = :level, `discription` = :discription, `nutritions` = :nutritions WHERE `id`= :id";


        $stmt = $pdo->prepare($query);
        $stmt->execute(array(":name" => $_POST['recipe_title'], 
        					":uploded_by" => $_SESSION['username'],
        					":prep" => $prep,
        					":type" => $_POST['type'],
        					":ready_in" => $ready,
        					":category" => $_POST['category'],
        					":serve" => $serve,
        					":level" => $_POST['level'],
        					":discription" => $_POST['description'],
                  ":nutritions" => $_POST['nutrition'],
                  ":id" => $_GET['id'])
                        );

        $stmt = $pdo->prepare('DELETE FROM ingredients WHERE dish_id=:dish_id');
        $stmt->execute(array( ":dish_id" => $_GET['id'] ));
        
        $ingredient_rank=1;
        for ($i=1; $i<=100 ; $i++) 
    	{ 
        	if (!isset($_POST['ingredient'.$i])) continue;
        		
                # code...
            $stmt = $pdo->prepare("INSERT INTO `ingredients`(`dish_id`, `rank`, `ingredients`) VALUES(:dish_id, :rank, :ingredients)");
            $stmt->execute(array(":dish_id" => $_GET['id'],
                                ":rank" => $ingredient_rank,
                                ":ingredients" => $_POST['ingredient'.$i]));
            $ingredient_rank++;
        }

        $stmt = $pdo->prepare('DELETE FROM directions WHERE dish_id=:dish_id');
        $stmt->execute(array( ":dish_id" => $_GET['id'] ));

		$direction_rank=1;
		for ($i=1; $i <100 ; $i++)
   		{
			if (!isset($_POST['step'.$i])) continue; 

			$stmt =$pdo->prepare("INSERT INTO `directions`(`dish_id`, `step`, `directions`) VALUES(:dish_id, :step, :directions)");
			$stmt->execute(array(":dish_id" => $_GET['id'],
								":step" => $direction_rank,
								"directions" => $_POST['step'.$i]));
			
		    $direction_rank++;
	    }
		      	
        header( 'Location: recipe.php?id='.$_GET['id'] ) ;
        return;

    }

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
  <title>Recipe Hub</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="recipe_styles.css">
  <script src="https://code.jquery.com/jquery-3.2.1.js"></script>
<style>
	
	.div1
	{
background: url(img/bg3size.jpg);
background-repeat: no-repeat;
background-size: auto 100%;
height: 300px;
background-color: black;
color: white;
font-size: 70px;
font-style: italic;
font-family: Arial;

	}

	.text{
		text-align: right;
    padding-left: 20px;
    padding-right: 220px;
    line-height: 300px;
  }
  .main{
    margin: 2rem auto;
    max-width: 750px;
    padding: 0px 20px;
  }
.field{
  margin: 2rem auto;
  width: 100%;
}
label{
  color: rgba(21, 21, 21, .7);
  font-weight: 600;
}
.grid{
  width: 100%;
  display: inline-flex;
  flex-wrap: wrap;
}
.grid .field{
  width: 50%;
  padding: 0px 20px;
}

	input[type=text], textarea {
  width: 100%;
  height: 50px;
  padding: 15px;
  margin: 8px 0;
  border: 2px solid #bfbfbf;
  border-radius: 10px;
  outline: none;
}

.btn, .delete{
  height: 50px;
  font-weight: 600;
  background-color: #528f2d;
  border: none;
  border-radius: 25px;
  padding: 0 20px;
  color: #fff;
  outline: none;
}
.delete{
  position: absolute;
  width: 50px;
  padding: 0;
  top: 50%;
  margin-left: 20px;
  transform: translateY(-25px);
  line-height: 50px;
  font-size: 30px;
}
.btn:hover, .delete:hover{
  background-color: #006400;
}

input[type=file]
{
	width: 100%; 
  border: 2px solid #bfbfbf;
  outline: none;
}

select {
  width: 100%;
  height: 50px;
  padding: 10px;
  margin: 8px 0;
  border: 2px solid #bfbfbf;
  border-radius: 10px;
  outline: none;
}

#submit_btn {
  background-color: black;
  color: white;
  padding: 12px 20px;
  border: 2px solid #000;
  border-radius: 4px;
  cursor: pointer;
  float: center;
  font-weight: bold;
  font-style: italic;
  font-size: 16px;
  outline: none;
}

#submit_btn:hover {
  background-color: green;
  border: 2px solid green;
}
.media{
  width: 700px;
}

@media (max-width: 426px){
  .grid .field, .media{
    width: 100%;
  }
  .delete{
    margin-left: 5px;
    line-height: 40px;
    width: 40px;
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

<div class="div1">
  <div class="text">
    <strong>RECIPE</strong>
  </div>
</div>



<div class="main">
 <form method="post" enctype="multipart/form-data" id="edit_recipe">
 	<div class="field">
  <label for="recipe_title"><strong>Recipe Title:</strong></label><br>
  <input type="text" id="recipe_title" name="recipe_title" placeholder="Your Recipe Title" required value="<?= ($dish['name'])? htmlentities($dish['name']): '' ?>">
</div>

<div class="field">
	<label for="description"><strong>Description:</strong></label><br>
	<textarea id="description" name="description" placeholder="Your Recipe Description" style="height:140px" required ><?= ($dish['discription'])? htmlentities($dish['discription']): '' ?></textarea>
</div>

<div class="field">
  <label for="ingredient1"><strong>Recipe Ingredients:</strong></label><br>
  <textarea id="ingredient1" name="ingredient1" placeholder="Enter Your Recipe Ingredients" style="height:50px; width: 90%;" required><?= htmlentities($ingredients[0]['ingredients']) ?></textarea>
  
  <div id="add_ingredients">
  <?php
        $ingrd_count = 1;
        for($i=1; $i < count($ingredients); $i++){
            $ingrd_count++;
            echo '<div id="ingrd'.$ingrd_count.'"  class="count_ingrd" style="position: relative;">
            <textarea id="ingredient'.$ingrd_count.'" name="ingredient'.$ingrd_count.'" placeholder="Enter Your Recipe Ingredients" style="height:50px; width: 90%;" required>'.htmlentities($ingredients[$i]["ingredients"]).'</textarea>
            <button class="delete" onclick="$(\'#ingrd'.$ingrd_count.'\').remove();console.log(ingrd_count);return false;">X</button>
            </div>';
        }
    ?>
  </div>
  <input type="button" id="add_ingredient" class="btn" value="Add ingredients">
</div>

<div class="field">
  <label for="step_text1"><strong>Recipe Instructions:</strong></label><br>
  <textarea id="step_text1" name="step1" placeholder="Enter Your Recipe Instructions" style="height:100px; width: 90%;" required><?= htmlentities($directions[0]['directions']) ?></textarea>

  <div id="add_steps">
  <?php
        $step_count = 1;
        for($i=1; $i < count($directions); $i++){
            $step_count++;
            echo '<div id="step'.$step_count.'"  class="count_steps" style="position: relative;">
            <textarea id="step_text'.$step_count.'" name="step'.$step_count.'" placeholder="Enter Your Recipe Instructions" style="height:100px; width: 90%;" required>'.htmlentities($directions[$i]["directions"]).'</textarea>
            <button class="delete" onclick="$(\'#step'.$step_count.'\').remove();return false;">X</button>
            </div>';
        }
    ?>
  </div>
  <input type="button" id="add_step" class="btn" value="Add next step">
</div>

<div class="grid">
<div class="field">
	<label for="level"><strong>Recipe Difficulty:</strong></label><br>
<select id="level" name="level" required>
        <option value="" disabled style="display: none;"> Select level</option>
        <option value="Advance" <?= ($dish['level'] == 'Advance')? 'selected': '' ?>>Advance</option>
        <option value="Moderate" <?= ($dish['level'] == 'Moderate')? 'selected': '' ?>>Moderate</option>
        <option value="Easy" <?= ($dish['level'] == 'Easy')? 'selected': '' ?>>Easy</option>
      </select>
</div>

<div class="field">
	<label for="category"><strong>Recipe Category:</strong></label><br>
<select id="category" name="category" required>
        <option value="" disabled style="display: none;"> Select Category</option>
        <option value="Beverages" <?= ($dish['category'] == 'Beverages')? 'selected': '' ?>>Beverages</option>
        <option value="Healthy" <?= ($dish['category'] == 'Healthy')? 'selected': '' ?>>Healthy</option>
        <option value="Main-Course" <?= ($dish['category'] == 'Main-Course')? 'selected': '' ?>>Main-Course</option>
        <option value="Dessert" <?= ($dish['category'] == 'Dessert')? 'selected': '' ?>>Dessert</option>
  </select>
</div>

<div class="field">
	 <label for="serving"><strong>Servings:</strong></label><br>
  <select id="serving" name="serving" required>
        <option value="" style="display: none;"> Select Serving</option>
        <option value="1" <?= ( $dish['serve'] == '1' )? 'selected': '' ?>>1 serving</option>
        <option value="2" <?= ( $dish['serve'] == '2' )? 'selected': '' ?>>2 serving</option>
        <option value="3" <?= ( $dish['serve'] == '3' )? 'selected': '' ?>>3 serving</option>
        <option value="4" <?= ( $dish['serve'] == '4' )? 'selected': '' ?>>4 serving</option>
        <option value="5" <?= ( $dish['serve'] == '5' )? 'selected': '' ?>>5 serving</option>
        <option value="6" <?= ( $dish['serve'] == '6' )? 'selected': '' ?>>6 serving</option>
        <option value="7" <?= ( $dish['serve'] == '7' )? 'selected': '' ?>>7 serving</option>
        <option value="8" <?= ( $dish['serve'] == '8' )? 'selected': '' ?>>8 serving</option>
        <option value="9" <?= ( $dish['serve'] == '9' )? 'selected': '' ?>>9 serving</option>
        <option value="other_time" <?= (1 <= $dish['serve'] && $dish['serve'] <= 9)? '': 'selected'?>>Other Number</option>
  </select><br>
  <input type="text" id="text_serving" name="text_serving" placeholder="Number of Servings" value="<?= $dish['serve'] ?>"
  <?= (1 <= $dish['serve'] && $dish['serve'] <= 9)? 'style="display: none;"': ''?>>
 </div>

<div class="field">
	 <label for="prep"><strong>Preparation Time:</strong></label><br>
  <select id="prep" name="prep" required>
        <option value="" style="display: none;"> Select Time</option>
        <option value="5 minutes" <?= ( $dish['prep'] == '5 minutes' )? 'selected': '' ?>>5 minutes</option>
        <option value="15 minutes" <?= ( $dish['prep'] == '15 minutes' )? 'selected': '' ?>>15 minutes</option>
        <option value="30 minutes" <?= ( $dish['prep'] == '30 minutes' )? 'selected': '' ?>>30 minutes</option>
        <option value="45 minutes" <?= ( $dish['prep'] == '45 minutes' )? 'selected': '' ?>>45 minutes</option>
        <option value="1 hour" <?= ( $dish['prep'] == '1 hour' )? 'selected': '' ?>>1 hour</option>
        <option value="1:30 hours" <?= ( $dish['prep'] == '1:30 hours' )? 'selected': '' ?>>1:30 hours</option>
        <option value="2 hours" <?= ( $dish['prep'] == '2 hours' )? 'selected': '' ?>>2 hours</option>
        <option value="other_time" <?= ($dish['prep'] == '5 minutes' || $dish['prep'] == '15 minutes' || $dish['prep'] == '30 minutes' || $dish['prep'] == '45 minutes' || $dish['prep'] == '1 hour' || $dish['prep'] == '1:30 hours' || $dish['prep'] == '2 hours')? '': 'selected'?>>Other Preparation Time</option>
  </select><br>
  <input type="text" id="text_prep" name="text_prep" placeholder="Other Preparation Time" value="<?= $dish['prep'] ?>"
  <?= ($dish['prep'] == '5 minutes' || $dish['prep'] == '15 minutes' || $dish['prep'] == '30 minutes' || $dish['prep'] == '45 minutes' || $dish['prep'] == '1 hour' || $dish['prep'] == '1:30 hours' || $dish['prep'] == '2 hours')? 'style="display: none;"': ''?>>
 </div>

 <div class="field">
	 <label for="ready"><strong>Ready In(Total Time Required):</strong></label><br>
  <select id="ready" name="ready" required>
        <option value="" style="display: none;"> Select Time</option>
        <option value="5 minutes" <?= ( $dish['ready_in'] == '5 minutes' )? 'selected': '' ?>>5 minutes</option>
        <option value="15 minutes" <?= ( $dish['ready_in'] == '15 minutes' )? 'selected': '' ?>>15 minutes</option>
        <option value="30 minutes" <?= ( $dish['ready_in'] == '30 minutes' )? 'selected': '' ?>>30 minutes</option>
        <option value="45 minutes" <?= ( $dish['ready_in'] == '45 minutes' )? 'selected': '' ?>>45 minutes</option>
        <option value="1 hour" <?= ( $dish['ready_in'] == '1 hour' )? 'selected': '' ?>>1 hour</option>
        <option value="1:30 hours" <?= ( $dish['ready_in'] == '1:30 hours' )? 'selected': '' ?>>1:30 hours</option>
        <option value="2 hours" <?= ( $dish['ready_in'] == '2 hours' )? 'selected': '' ?>>2 hours</option>
        <option value="other_time" <?= ($dish['ready_in'] == '5 minutes' || $dish['ready_in'] == '15 minutes' || $dish['ready_in'] == '30 minutes' || $dish['ready_in'] == '45 minutes' || $dish['ready_in'] == '1 hour' || $dish['ready_in'] == '1:30 hours' || $dish['ready_in'] == '2 hours')? '': 'selected'?> >Other Time</option>
  </select><br>
  <input type="text" id="text_ready" name="text_ready" placeholder="Other Time" value="<?= $dish['ready_in'] ?>"
  <?= ($dish['ready_in'] == '5 minutes' || $dish['ready_in'] == '15 minutes' || $dish['ready_in'] == '30 minutes' || $dish['ready_in'] == '45 minutes' || $dish['ready_in'] == '1 hour' || $dish['ready_in'] == '1:30 hours' || $dish['ready_in'] == '2 hours')? 'style="display: none;"': ''?>>
 </div>

 <div class="field">
	 <label for="type"><strong>Cooking Time:</strong></label><br>
  <select id="type" name="type" required>
      <option value="" style="display: none;"> Select Time</option>
      <option value="Vegetarian" <?= ( $dish['type'] == 'Vegetarian' )? 'selected': '' ?>>Vegetarian</option>
      <option value="Egg" <?= ( $dish['type'] == 'Egg' )? 'selected': '' ?>>Contain Egg</option>
      <option value="Non-Vegetarian" <?= ( $dish['type'] == 'Non-Vegetarian' )? 'selected': '' ?>>Non-Vegetarian</option>
  </select>
</div>
</div>

<div class="field">
	<label for="nutrition"><strong>Nutritional Facts:</strong></label><br>
	<textarea id="nutrition" name="nutrition" placeholder="Enter Nutrition Facts Of Your Recipe" style="height:140px" required><?= ($dish['nutritions'])? htmlentities($dish['nutritions']): '' ?></textarea>
</div>

<div class="field">
 <label for="img"><strong>Recipe image:</strong></label><br>
 <input type="hidden" name="address" value="<?=$dish['image']?>">
 <img src="<?=$dish['image']?>" class="media" id="preview"><br>
  <input type="file" id="img" name="img" accept="image/*">
  </div>

  </form>
  <div class="field">
  	
    <input type="submit" value="Submit" id="submit_btn" name="submit" form="edit_recipe">
    <a href="<?=$_SERVER['HTTP_REFERER']?>"><button id="submit_btn" style="background-color: #fff;color:#000;">Back</button></a>
  </div>


</div>



</form>


</div>
<?php
    include("footer.php");
?>
</body>
<script>

  ingrd_count = <?= $ingrd_count ?>;
  step_count = <?= $step_count ?>;
  $('#add_ingredient').click(function(event){
    event.preventDefault();

    var ingrd= $('.count_ingrd');
    console.log(ingrd.length+1);
    if ( ingrd.length+1 >= 30 ) {
        alert("Maximum of 30 entries exceeded");
        return;
    }
    ingrd_count++;
    console.log("Adding position "+ingrd_count);
    $('#add_ingredients').append(
      '<div id="ingrd'+ingrd_count+'"  class="count_ingrd" style="position: relative;">\
      <textarea id="ingredient'+ingrd_count+'" name="ingredient'+ingrd_count+'" placeholder="Enter Your Recipe Ingredients" style="height:50px; width: 90%;" required></textarea>\
      <button class="delete" onclick="$(\'#ingrd'+ingrd_count+'\').remove();console.log(ingrd_count);return false;">X</button>\
      </div>');
        
  });

  $('#add_step').click(function(event){
    event.preventDefault();

    var steps= $('.count_steps');
    console.log(steps.length+1);
    if ( steps.length+1 >= 30 ) {
        alert("Maximum of 30 entries exceeded");
        return;
    }
    step_count++;
    console.log("Adding step "+step_count);
    $('#add_steps').append(
      '<div id="step'+step_count+'"  class="count_steps" style="position: relative;">\
      <textarea id="step'+step_count+'" name="step'+step_count+'" placeholder="Enter Your Recipe Instructions" style="height:100px; width: 90%;" required></textarea>\
      <button class="delete" onclick="$(\'#step'+step_count+'\').remove();return false;">X</button>\
      </div>');
        
  });




  var serve = document.querySelector('#serving');
  var prep = document.querySelector('#prep');
  var ready = document.querySelector('#ready');

  $('#serving').change(function(){
    if(serve.value === 'other_time'){
      document.querySelector('#text_serving').style.display = 'block';
      document.querySelector('#text_serving').required = true;
      document.querySelector('#text_serving').focus();
    }
    else{
      document.querySelector('#text_serving').style.display = 'none';
      document.querySelector('#text_serving').required = false;
    }
  });

  $('#prep').change(function(){
    if(prep.value === 'other_time'){
      document.querySelector('#text_prep').style.display = 'block';
      document.querySelector('#text_prep').required = true;
      document.querySelector('#text_prep').focus();
    }
    else{
      document.querySelector('#text_prep').style.display = 'none';
      document.querySelector('#text_prep').required = false;
    }
  });


  $('#ready').change(function(){
    if(ready.value === 'other_time'){
      document.querySelector('#text_ready').style.display = 'block';
      document.querySelector('#text_ready').required = true;
      document.querySelector('#text_ready').focus();
    }
    else{
      document.querySelector('#text_ready').style.display = 'none';
      document.querySelector('#text_ready').required = false;
    }
  });

const img = document.querySelector("#preview");
const select = document.querySelector("#img");
	select.addEventListener("change",function(event){
		var reader = new FileReader();
		reader.onload = function(){
			if(reader.readyState == 2){
				img.src = reader.result;
			}
		}
		reader.readAsDataURL(event.target.files[0]);

	});


if ( window.history.replaceState ) {
	window.history.replaceState( null, null, window.location.href );
}
</script>
</html>