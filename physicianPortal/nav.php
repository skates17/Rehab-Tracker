<!-- ######################     Main Navigation   ########################## -->
<nav>
    <ul>
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

                if ($path_parts['filename'] == "managePatients") {
            print '<li class="activePage">Manage Patients</li>';
        } else {
            print '<li class = "nav"><a href="managePatients.php">Manage Patients</a></li>';
        }
        
        if ($path_parts['filename'] == "managePhysicians") {
            print '<li class="activePage">Manage Physicians</li>';
        } else {
            print '<li class = "nav"><a href="managePhysicians.php">Manage Physicians</a></li>';
        }
        ?>
        <li><button class="button" onclick="window.location.href='logout.php'">Logout</button></li>
    </ul>
</nav>
<!-- #################### Ends Main Navigation    ########################## -->