<?php
    session_start();
    function debug_to_console($data) {
        $output = $data;
        if (is_array($output))
            $output = implode(',', $output);

        echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
    }
    $selectedTeams = [];
    /*             Shufffles Draft Order            */
    $teams = ['arsenal', 'astonvilla', 'bournemouth', 'brighton', 'brentford', 'chelsea', 'crystalpalace', 'everton', 'fulham', 'ipswichtown', 'leicester', 'liverpool', 'mancity', 'manunited', 'newcastle', 'nottingham', 'southampton', 'tottenham', 'westham', 'wolves'];
    shuffle($teams);
    $i = 0;
    debug_to_console($teams);
    
    if (isset($_POST['teams'])) {
        $beams = $_POST['teams'];
        foreach ($beams as $team) {
            $selectedTeams[$i] = $team;
            $i++;
        }
        $_SESSION['teams'] = $teams;
       $_SESSION['selected'] = $selectedTeams;
    }
    if (isset($_GET['move']) && $_GET['move'] == 'true') {
       header('location: draft.php');
       exit();
   }
?>
<!DOCTYPE html>
<html>
<head>
    <link rel = "stylesheet" href = "style.css">
    <title>Selected Teams</title>
</head>
<body>
    <div class = "column">
    <div class = "titleBox">
    <?php echo "<h1>Selected Teams:</h1>"; ?>
    </div>
    
        <div class = "column" id ="teamsSelected">
        <ul id = "list">
        <?php
        foreach ($selectedTeams as $team) {
            echo "<li>" . htmlspecialchars($team) . "</li>";
        }
        debug_to_console($selectedTeams);
        echo "</ul>";

    ?>
    <div class = "row">
    <a href = "teamSelection.html"> Go Back </a>
    <a href = "begin.php?move=true"> Draft </a>
    
    </div>
    </div>
</div>
</body>
</html>