<?php
    require("pdo.php");

    $stmt = $pdo->prepare("SELECT *,DATE_FORMAT(date, '%d-%m-%Y') AS d FROM `comments` WHERE `dish_id` = :id ORDER BY `date`");
    $stmt->execute(array(":id" => $_GET['id']));
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);


    if($comments){
        foreach($comments as $comment){
            
            $stmt = $pdo->prepare("SELECT * FROM users where username = :xyz");
            $stmt->execute(array(":xyz" => $comment['sender']));
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            ?>
            <div class="comment">
            <div class="user-img">
                <img src="<?=$user['pic']?>" alt="user" width="100%" height="100%">
            </div>
            <div>
                <p class="sender">From <span style="color:blue;"><?=$comment['sender']?></span> at <?=$comment['d']?></p>
                <p class="comment-content"><?=$comment['comment']?></p>
            </div>
            </div>
            <?php
        }
    }
    else{
        echo "<center style='color:#686868;'>no comments yet</center>";
    }
?>