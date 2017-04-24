<!-- ######################     Main Navigation   ########################## -->
<nav>
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
            print '<li class="activePage">Patient Overview</li>';
        } else {
            print '<li class = "nav"><a href="viewCompliance.php">Patient Overview</a></li>';
        }

        if ($path_parts['filename'] == "patientSessions") {
            print '<li class="activePage">Patient Sessions</li>';
        } else {
            print '<li class = "nav"><a href="patientSessions.php">Patient Sessions</a></li>';
        }
        if ($_SESSION['fldAdmin'] == 1) {
            if ($path_parts['filename'] == "manageUsers") {
                print '<li class="activePage">Manage Users</li>';
            } else {
                print '<li class = "nav"><a href="manageUsers.php">Manage Users</a></li>';
            }
        }

        print "<li>CLIN: " . $_SESSION['DocID'] . " </li>";
        ?>
        <li><button class="button" onclick="window.location.href = 'logout.php'">Logout</button></li>
    </b></ul>
</nav>
<!-- #################### Ends Main Navigation    ########################## -->