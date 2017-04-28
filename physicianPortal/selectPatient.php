<?php


//Step One: Make connection with database (in top.php)
include "top.php";

print "<h2>Select Patient</h2>";
print '<p>Select a patient to view their session history.</p>';


//initialize value
$patient = "";
$debug = true;



// Step Two: code can be in initialize variables or where step four needs to be
$query = "SELECT pmkPatientID ";
$query .= "FROM tblPatient ";
$query .= "ORDER BY  pmkPatientID";


// Step Three: code can be in initialize variables or where step four needs to be
// $patient is an associative array
$patientList = $thisDatabaseReader->select($query, "", 0, 1, 0, 0, false, false);

if (isset($_POST['btnSubmit'])) {
    foreach (($_POST['Patient']) as $patient) {
        $patient = $patient;
//print '$patient';
    }
    $_SESSION['patient']=$patient;
    echo "<script>window.open('patientSessions.php','_self')</script>";
}

// Step Four: prepare output two methods, only do one of them
/* html looks like this if we were to do this manually (shortened to three 
  buildings

 */
//print "<div id = 'container'> ";


// or you can print it out
print "<fieldset><form method='POST'>";
print '<label for="lstPatient">Patient ';
print '<select id="lstPatient" name = "Patient[]"';
print '        name="lstPatient" ';
print '        tabindex="300" >';


foreach ($patientList as $row) {
    print '<option ';
    if ($patientList == $row["pmkPatientID"])
        print " value='selected' ";
    print 'value="' . $row["pmkPatientID"] . '">' . $row["pmkPatientID"];
    print '</option>';
}
print '</select></label>';
//submit form
//print '<fieldset class="buttons">;
print '             <input type="submit" id="btnSubmit" name="btnSubmit" value="View" tabindex="900" class="button">';
print '          </fieldset>';
print '</form>';

