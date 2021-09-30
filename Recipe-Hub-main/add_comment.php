<?php
    require("pdo.php");
        echo "done";
    $stmt = $pdo->prepare("INSERT INTO `comments` (`dish_id`, `sender`, `comment`, `date`) VALUES (:dish, :sender, :comment, NOW())");
    $stmt->execute(array(":dish" => $_POST['id'],
                        ":sender" => $_POST['sender'],
                        ":comment" => $_POST['comment']));
    
?>