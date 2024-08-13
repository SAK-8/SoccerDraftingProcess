<?php
session_start();
/*                DEbug to console for well debugging   */
$teams = $_SESSION['teams'];
$name = "shan";

function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}

/*                Database COnnection             */
$servername = "localhost";
$username = "root"; 
$password = "";
$dbname = "players";

$db = new mysqli($servername, $username, $password, $dbname);

if (!isset($_SESSION['player'])) {
    $_SESSION['player'] = 0;  // Initialize player to 0
}
if (!isset($_SESSION['ascend'])) {
    $_SESSION['ascend'] = true;
}
$checker = $_SESSION['ascend'];
debug_to_console($checker);

if(isset($_GET['id'])){

    /*            Obtains draft Order              */
    debug_to_console($teams);

    /*            Sets team value            */
    
    if ($_SESSION['player'] == 20) {  
        $_SESSION['ascend'] = false;
        $_SESSION['player']--;
    } elseif ($_SESSION['player'] == -1) {
        $_SESSION['ascend'] = true;
        $_SESSION['player']++;

    }
    /*            Moves the player                 */
    $id = intval($_GET['id']);
    $number = $_SESSION['player'];
    $selected = "SELECT * FROM player WHERE id = ?";
    $workSelect = $db->prepare($selected);
    $workSelect->bind_param("i", $id);
    $workSelect->execute();
    $result = $workSelect->get_result();
    $player = $result->fetch_assoc();

    if($player){
        $putPlayer = "INSERT INTO $teams[$number]
                    (id, name, position, age, overall, finishing, longshot, shotpower, shortpass, longpass, speed, ballcontrol, dribbling, penalties, freekickaccuracy, defensiveawareness, standtackle, slidetackle, stamina, strength, tradevalue) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $workInsert = $db->prepare($putPlayer);
        $workInsert->bind_param("issiiiiiiiiiiiiiiiiis", $player['id'], $player['name'], $player['position'], $player['age'], $player['overall'], $player['finishing'], $player['longshot'], $player['shotpower'], $player['shortpass'], $player['longpass'], $player['speed'], $player['ballcontrol'], $player['dribbling'], $player['penalties'], $player['freekickaccuracy'], $player['defensiveawareness'], $player['standtackle'], $player['slidetackle'], $player['stamina'], $player['strength'], $player['tradevalue']);
        $name = $player['name'];
        $workInsert->execute();

        $putBoard = "INSERT INTO draftboard
            (id, team, name, position, age, overall) VALUES (?, ?, ?, ?, ?, ?)";
        $workBoardInsert = $db->prepare($putBoard);
        $workBoardInsert->bind_param("isssii", $number, $teams[$number], $player['name'], $player['position'], $player['age'], $player['overall']);
        $workBoardInsert->execute();

        $deletePlayer = "DELETE FROM player WHERE id = ?";
        $workDelete = $db->prepare($deletePlayer);
        $workDelete->bind_param("i", $id);
        $workDelete->execute();
        $workSelect->close();
    $workInsert->close();
    $workDelete->close();
    
?>
<!--      General Html         -->
<DOCTYPE! html>
<html>
    <head>
        <link rel = "stylesheet" href = "style.css">
        <style>
            body{
                background-image:url("bg/<?php echo $teams[$number]; ?>.png");
            }
        </style>
        <title> Selection </title>
    </head>
    <body>
        <div class = "titleBox">
            <img src = "player/<?php echo $name;?>">
            <h1> <?php echo $teams[$number]; ?> have selected <?php echo $name; ?> </h1>
            <a id = "i" href = "draft.php?teamNum=true"> Return </a>
        </div>
    </body>

    <?php 
    /*                THIS INCREMENTS THE TEAM VALUE       */
    if (!isset($_SESSION['value']) || $_SESSION['value'] === false) {

            if ($_SESSION['ascend']) {
                $_SESSION['player']++;
            } else {
                $_SESSION['player']--;
            }



        $player = $_SESSION['player'];
        echo "You viewed this page " . $player . " times.";
    } else {
        echo "You refreshed the page";
    }
    }
    
} else{
    echo "haha guess not!";
}?>
    <script>
    counter = 0;
    document.getElementById('i').addEventListener('click', function() {
    <?php $_SESSION['value'] = true; ?>
    });
    </script>
</html>