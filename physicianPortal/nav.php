<!-- ######################     Main Navigation   ########################## -->
<nav color = "white">
<ul><b>
        <?php
        // This sets the current page to not be a link. Repeat this if block for
        //  each menu item 
        if ($path_parts['filename'] == "index") {
            print '<li class="activePage">Home</li>';
        } else {
            print '<li class = "nav"><a href="index.php">Home</a></li>';
        }

        if ($path_parts['filename'] == "viewCompliance") {
            print '<li class="activePage">All Patients</li>';
        } else {
            print '<li class = "nav"><a href="viewCompliance.php">All Patients</a></li>';
        }

        if ($path_parts['filename'] == "selectPatient" or $path_parts['filename']== "patientSessions") {
            print '<li class="activePage">Patient Sessions</li>';
        } else {
            print '<li class = "nav"><a href="selectPatient.php">Patient Sessions</a></li>';
        }
        
//          if ($path_parts['filename']== "patientSessions") {
//            print '<li class="activePage">Patient Sessions</li>';
//        } 
                
        if ($_SESSION['fldAdmin'] == 1) {
            if ($path_parts['filename'] == "manageUsers") {
                print '<li class="activePage">Add Clinician</li>';
            } else {
                print '<li class = "nav"><a href="manageUsers.php">Add Clinician</a></li>';
            }
            
            if ($path_parts['filename'] == "form") {
                print '<li class="activePage">Add Patient</li>';
            } else {
                print '<li class = "nav"><a href="form.php">Add Patient</a></li>';
            }
        }

        print "<li>CLIN: " . $_SESSION['DocID'] . " </li>";
        ?>
        <li><button class="button" onclick="window.location.href = 'logout.php'">Logout</button></li>
    </b></ul>
</nav>
<!-- #################### Ends Main Navigation    ########################## -->