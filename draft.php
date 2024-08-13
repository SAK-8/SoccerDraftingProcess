<?php 
session_start();
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

$teams = $_SESSION['teams'];
$selected = $_SESSION['selected'];
debug_to_console($teams);
debug_to_console($selected);


if (!isset($_SESSION['player'])) {
    $_SESSION['player'] = 0;
}

    $teamNumber = $_SESSION['player'];

/*                Player or Computer Selection    */

if(!isset($_SESSION['user-selected'])){
    $_SESSION['user-selected'] = false;
}
$_SESSION['user-selected'] = false;

foreach($selected as $beamTeam){
    if($teams[$teamNumber] == $beamTeam){
        $_SESSION['user-selected'] = true;
    }  
}
$playerId = 0;
$truth = $_SESSION['user-selected'];
$query = "SELECT * FROM player";
$result = $db->query($query);
if(!$truth){
    if ($result->num_rows > 0) {
        $playersWithMaxTradeValue = [];
        $maxTradeValue = -INF; // Initialize to the smallest possible value
    
        // Iterate through all players to find the maximum trade value
        while ($row = $result->fetch_assoc()) {
            $tradeValue = (float)$row['tradevalue']; // Ensure tradevalue is treated as float
            if ($tradeValue > $maxTradeValue) {
                $maxTradeValue = $tradeValue;
                $playersWithMaxTradeValue = [$row]; // Start new list with current player
            } elseif ($tradeValue == $maxTradeValue) {
                $playersWithMaxTradeValue[] = $row; // Add player to list of maximum trade value players
            }
            // Randomly select a player with the maximum trade value
        if (!empty($playersWithMaxTradeValue)) {
            $randomIndex = array_rand($playersWithMaxTradeValue);
            $selectedPlayer = $playersWithMaxTradeValue[$randomIndex];
            $playerId = $selectedPlayer['id'];
        }
    }
        
    } else {
        echo "THE DRAFT IS OVER";
    }
}

/*                Connect to Table                  */
$sql = "SELECT * FROM player";

/*                Sort                              */

if (!isset($_SESSION['toggle'])) {
    $_SESSION['toggle'] = "DESC";
}

$toggle = $_SESSION['toggle'];

if (isset($_GET['orderby'])) {
    $order = $_GET['orderby'];

    if ($toggle == "DESC") {
        $_SESSION['toggle'] = "ASC";
    } elseif ($toggle == "ASC") {
        $_SESSION['toggle'] = "DESC";
    }

    $toggle = $_SESSION['toggle']; // Update $toggle with the new session value
    $sql = "SELECT * FROM player ORDER BY $order $toggle";
    debug_to_console($_SESSION['toggle']);
}

$bam = $db->query($sql);



/*                Change player displayed           */

$name = "Name"; $position = "Position"; $overall = 0; $height = 0; $weight = 0; $age = 0; $salary = 0; $tradeValue = "0 stars"; $morale = "Content";
if (isset($_GET['display'])) {
    $play = htmlspecialchars($_GET['display'], ENT_QUOTES, 'UTF-8');
    $selected = "SELECT * FROM display WHERE name = ?";
    $workSelect = $db->prepare($selected);
    $workSelect->bind_param("s", $play);
    $workSelect->execute();
    $result = $workSelect->get_result();
    $player = $result->fetch_assoc();    
    if($player){
        $name = $player['name'];
        $position = $player['position'];
        $overall = $player['overall'];
        $height = $player['height'];
        $weight = $player['weight'];
        $age = $player['age'];
        $salary = $player['salary'];
        $tradeValue = $player['tradevalue'];
        $morale = $player['morale'];
    }   
};

?>

<DOCTYPE! html>
<html>
    <head>
        <link rel = "stylesheet" href="style.css">
        <title>Draft</title>
    </head>
    <body>
        <div class = "titleBox">
            <h1> SUPER LEAGUE FANTASY DRAFT</h1>
        </div>
        <!--  This area is for when the user is not selecting -->
        <?php if(!$truth){?>
        <div id = "compselectbar">
            <img class = "teamLogo" src = "teamm/<?php echo $teams[$teamNumber];?>.png">
            <h1> <?php echo $teams[$teamNumber]; ?> are selecting </h1>
        </div>
        <div id = "computerleave">
            <h1> And with pick #<?php echo $teamNumber+1; ?></h1>
            <a href = "selected.php?id=<?php echo $playerId;?>"> NEXT </a>
        </div>
                <!--  This area is for when the user is selecting -->

        <?php }else{?>
        <div id = "displayPlayer" class = "row">
            <div class = "column" id = "ovrColumn">
                <h1 class = "overallAtt" id = "ovrTitle"> OVR</h1>
                <h1 class = "overallAtt"> <?php echo $overall; ?></h1>
            </div>
            <img id = "displayImage" src = "img/<?php echo $name ?>.png">
            <div class = "column">
                <h1> <?php echo $name; ?></h1>
                <h1> <?php echo $position; ?></h1>
                <div id = "grid">
                    <div class = "row"> <h1 class = "attribute"> Height:</h1> <h1 class = "value"><?php echo $height; ?></h1></div>
                    <div class = "row"> <h1 class = "attribute"> Weight:</h1> <h1 class = "value"><?php echo $weight; ?></h1></div>
                    <div class = "row"> <h1 class = "attribute"> Age:</h1> <h1 class = "value"><?php echo $age; ?></h1></div>
                    <div class = "row"> <h1 class = "attribute"> Salary:</h1> <h1 class = "value"><?php echo $salary; ?></h1></div>
                    <div class = "row"> <h1 class = "attribute"> Trade Value:</h1> <h1 class = "value"><?php echo $tradeValue; ?></h1></div>
                    <div class = "row"> <h1 class = "attribute"> Morale:</h1> <h1 class = "value"><?php echo $morale; ?></h1></div>
                </div>
            </div>
            <img class = "teamLogo" src = "teamm/<?php echo $teams[$teamNumber];?>.png">
            <a id = "toggle"> Toggle Draftboard <br> and Player Pool </a>
        </div>
                <!--  This is the draftpool -->
        <table id = "draftpool">
            <tr id = "tableHeading">
                <th><a href = "draft.php?orderby=name" class = "names">Name </a></th>
                <th><a href = "draft.php?orderby=position" class = "names">POS </a></th>
                <th><a href = "draft.php?orderby=age" class = "names">AGE </a></th>
                <th><a href = "draft.php?orderby=overall" class = "names">OVR </a></th>
                <th><a href = "draft.php?orderby=finishing" class = "names">FIN </a></th>
                <th><a href = "draft.php?orderby=longshot" class = "names">LSHOT </a></th>                
                <th><a href = "draft.php?orderby=shotpower" class = "names">SHTPWR </a></th>
                <th><a href = "draft.php?orderby=shortpass" class = "names">SHRTPSS </a></th>
                <th><a href = "draft.php?orderby=longpass" class = "names">LNGPSS </a></th>
                <th><a href = "draft.php?orderby=speed" class = "names">PACE </a></th>
                <th><a href = "draft.php?orderby=ballcontrol" class = "names">BLCNTRL </a></th>
                <th><a href = "draft.php?orderby=dribbling" class = "names">DRBL </a></th>
                <th><a href = "draft.php?orderby=penalties" class = "names">PEN </a></th>
                <th><a href = "draft.php?orderby=freekickaccuracy" class = "names">FKA </a></th>
                <th><a href = "draft.php?orderby=defensiveawareness" class = "names">DEFA </a></th>
                <th><a href = "draft.php?orderby=standtackle" class = "names">STNDTKL </a></th>
                <th><a href = "draft.php?orderby=slidetackle" class = "names">SLDTKL </a></th>
                <th><a href = "draft.php?orderby=stamina" class = "names">STMN </a></th>
                <th><a href = "draft.php?orderby=strength" class = "names">STRN </a></th>
                <th><a href = "draft.php?orderby=tradevalue" class = "names">TRDVLU </a></th>
                <th><a class = "names"> DRAFT </a></th>
            </tr>
            <tr>
                <?php while($row = $bam->fetch_assoc()){?>
                <td id = "playerNameBoard"><a id = "bamws" href = "draft.php?display=<?php echo $row['name'] ?>"><?php echo $row['name'];?> </a></td>
                <td> <?php echo $row['position'];?></td>
                <td> <?php echo $row['age'];?></td>
                <td> <?php echo $row['overall'];?></td>
                <td> <?php echo $row['finishing'];?></td>
                <td> <?php echo $row['longshot'];?></td>
                <td> <?php echo $row['shotpower'];?></td>
                <td> <?php echo $row['shortpass'];?></td>
                <td> <?php echo $row['longpass'];?></td>
                <td> <?php echo $row['speed'];?></td>
                <td> <?php echo $row['ballcontrol'];?></td>
                <td> <?php echo $row['dribbling'];?></td>
                <td> <?php echo $row['penalties'];?></td>
                <td> <?php echo $row['freekickaccuracy'];?></td>
                <td> <?php echo $row['defensiveawareness'];?></td>
                <td> <?php echo $row['standtackle'];?></td>
                <td> <?php echo $row['slidetackle'];?></td>
                <td> <?php echo $row['stamina'];?></td>
                <td> <?php echo $row['strength'];?></td>
                <td> <?php echo $row['tradevalue'];?></td>
                <?php $number = $row['id']; ?> 
                <td> <a id = "draftButton" href = "selected.php?id=<?php echo $number ?>"> DRAFT</a></td>
            </tr>
            <?php };
            };?>
            </table>
            

                    <!--  This is the draftboard -->
            <table id = "draftboard">
            <tr>    
                <th>PICK # </th>
                <th>TEAM </th>
                <th>NAME </th>
                <th>OVERALL </th>
                <th>POSITION </th>
                <th>AGE </th>
            </tr>
            <?php
            $stat = "SELECT * FROM draftboard";
            $board = $db->query($stat);
           
            while($sel = $board->fetch_assoc()){
                ?>
            <tr>    
                <td><?php echo $sel['id']; ?></td>
                <td><?php echo $sel['team']; ?> </td>
                <td><?php echo $sel['name']; ?> </td>
                <td><?php echo $sel['overall']; ?> </td>
                <td><?php echo $sel['position']; ?> </td>
                <td><?php echo $sel['age']; ?> </td>
            </tr>
            <?php };?>
            </table>

            <table class = "rosters">
            <?php
                $sorted = $teams;
                asort($sorted);
                $i = 0; $three = 0;
                while ($i < 20) { ?>
                    <tr><?php
                        $statement = "SELECT * FROM $sorted[$i]";
                        $reboot = $db->query($statement);
                        ?>
                        <td><img src="teamm/<?php echo $sorted[$i]; ?>.png"></td>
                        <?php while ($sel = $reboot->fetch_assoc()) { ?>
                            <td><?php echo $sel['position']; ?>:<?php echo $sel['name']; ?> </td>
                        </tr>
                        <?php } // <-- Correct closing of the inner while loop
                        $i++;
                        $three++;
                    } // <-- Correct closing of the outer while loop
                ?>
            </table>
            <script>
            var checker = false;
            document.getElementById('toggle').addEventListener('click', function() {
                if (checker === false){
                    document.getElementById('draftboard').style.display ="block";
                    document.getElementById('draftpool').style.display ="none";
                    checker = true;
                }else{
                    document.getElementById('draftboard').style.display ="none";
                    document.getElementById('draftpool').style.display ="block";
                    checker = false;
                }
            });    

            </script>

            <script>
            document.getElementById('draftButton').addEventListener('click', function() {
                <?php  $_SESSION['value'] = false;?>
            });    
            var truth = <?php echo $truth ? 'true' : 'false'; ?>;
            if(truth){
                document.getElementById('draftpool').style.display ="none";
                document.getElementById('displayplayer').style.display ="none";
                document.getElementById('draftboard').style.display ="none";
                document.getElementById('compselectbar').style.display ="block";
                document.getElementById('computerleave').style.display ="block";
                console.log("true");
            }else{
                document.getElementById('draftpool').style.display ="block";
                document.getElementById('displayplayer').style.display ="block";
                document.getElementById('draftboard').style.display ="block";
                document.getElementById('compselectbar').style.display ="none";
                document.getElementById('computerleave').style.display ="none";
                console.log("false");
            }
            </script>
            
    </body>
</html>