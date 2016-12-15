       
<!DOCTYPE html>
<html lang="en">

    <!-- **********************     Body section      ********************** -->

    <?php
    session_start();
    if (!isset($_SESSION['DocID'])) {
        header("Location: login.php");
    }
    include "head.php";
    $tblPatient = 'tblPatient';
    $tblSession = 'tblSession';
    ?>
    <body>
        
        
        <div id ="container">
            <?php
            include "nav.php";
            ?>

            <!-- %%%%%%%%%%%%%%%%%%%%%%     Page header   %%%%%%%%%%%%%%%%%%%%%%%%%%%%%% -->





            <!-- %%%%%%%%%%%%%%%%%%%%% Ends Page header   %%%%%%%%%%%%%%%%%%%%%%%%%%%%%% -->