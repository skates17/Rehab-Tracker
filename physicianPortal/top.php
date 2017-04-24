       
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

            <?php
            include "nav.php";
            ?>
    
        <div id ="container">
    
        
    <body>
        
        
        
            

            <!-- %%%%%%%%%%%%%%%%%%%%%%     Page header   %%%%%%%%%%%%%%%%%%%%%%%%%%%%%% -->





            <!-- %%%%%%%%%%%%%%%%%%%%% Ends Page header   %%%%%%%%%%%%%%%%%%%%%%%%%%%%%% -->