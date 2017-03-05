<?php

/* List box from database
 * Four step process

  Create your database object using the appropriate database username
  Define your query. In this example we open the file that contains the query.
  Execute the query
  Prepare output and loop through array

 */
//initialize value
$patient = "";
$debug = true;

//Step One: Make connection with database (in top.php)
include "top.php";

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
}

// Step Four: prepare output two methods, only do one of them
/* html looks like this if we were to do this manually (shortened to three 
  buildings

 */
//print "<div id = 'container'> ";
print "<h2>View Patient Information</h2>";
print '<p>Select a patient to view their session history.</p>';
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

//TBLSESSION INFORMATION
//--------------------------DISPLAY IF PATIENT IS IN/OUT OF COMPLIANCE---------------------
if (isset($_POST['btnSubmit'])) {
    print"<p>Patient $patient is ";
    $checkCompQuery = "SELECT fldWkCompliance FROM tblPatient WHERE pmkPatientID = $patient";
    $compliance = $thisDatabaseReader->select($checkCompQuery, "", 1, 0, 2, 0, false, false);
    if ($compliance == 0) {
        print'<b>NOT</b> ';
    } 
    print"in <b>weekly</b> compliance.";


// Begin output
    print '<article>';
    if ($debug) {
        print"after patient sessions article";
    }
}

//-------------------------DISPLAY ALL PATIENT INFORMATION--------------------------------
if ($tblPatient != "") {
    print '<aside id="records">';
    $queryPatient = 'SHOW COLUMNS FROM ' . $tblPatient;
    $infoP = $thisDatabaseReader->select($queryPatient, "", 0, 0, 0, 0, false, false);
    $span = count($infoP);
    print '<table>';
// print out the column headings, note i always use a 3 letter prefix
// and camel case like pmkCustomerId and fldFirstName
    print '<tr>';
    $columns = 0;
    foreach ($infoP as $field) {
        print '<th><b>';
        $camelCase = preg_split('/(?=[A-Z])/', substr($field[0], 3));
        foreach ($camelCase as $one) {
            print $one . " ";
        }
        print '</b></th>';
        $columns++;
    }
    print '</tr>';
}

////now print out each record
$querypatient = "SELECT * FROM " . $tblPatient . " WHERE pmkPatientID = '$patient' ORDER BY pmkPatientID ";
$infopatient = $thisDatabaseReader->select($querypatient, "", 1, 1, 2, 0, false, false);
foreach ($infopatient as $rec) {
    print '<tr>';
    for ($i = 0; $i < $columns; $i++) {
        print '<td>' . $rec[$i] . '</td>';
    }
    print '</tr>';
}
print '</table><br>';
// all done

//---------------------------UPDATE INTENSITY GOAL ------------------------------------------
include 'updateIntensity.php';
print '<br>';


//-------------------------DISPLAY ALL PATIENT SESSION HISTORY -------------------------
// Display all the records for a given table
print'<h2>Session History </h2>';
if ($tblSession != "") {
    print '<aside id="records">';
    $query2 = 'SHOW COLUMNS FROM ' . $tblSession;
    $info = $thisDatabaseReader->select($query2, "", 0, 0, 0, 0, false, false);
    $span = count($info);
    print '<table>';
// print out the column headings, note i always use a 3 letter prefix
    print '<tr>';
    $columns = 0;
    foreach ($info as $field) {
        print '<th><b>';
        $camelCase = preg_split('/(?=[A-Z])/', substr($field[0], 3));
        foreach ($camelCase as $one) {
            print $one . " ";
        }
        print '</b></th>';
        $columns++;
    }
    print '</tr>';
}

////now print out each record
$query = "SELECT * FROM " . $tblSession . " WHERE pmkPatientID = '$patient' ORDER BY pmkPatientID ";
$info3 = $thisDatabaseReader->select($query, "", 1, 1, 2, 0, false, false);
foreach ($info3 as $rec) {
    print '<tr>';
    for ($i = 0; $i < $columns; $i++) {
        print '<td>' . $rec[$i] . '</td>';
    }
    print '</tr>';
}
// all done

print '</aside>';

print '</article><br>';

include 'footer.php';

