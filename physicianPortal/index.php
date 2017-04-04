<?php

//##############################################################################
//
// This page lists your tables and fields within your database. if you click on
// a database name it will show you all the records for that table. 
// 
// 
// This file is only for class purposes and should never be publicly live
//##############################################################################
include "top.php";

print '<h1> Welcome! </h1>';

print "<h2>The following patients are OUT of weekly compliance*: </h2>";

$tableName = tblPatient;



$checkCompQuery = "SELECT pmkPatientID FROM tblPatient WHERE fldWeekCompliance = 0";
$compliance = $thisDatabaseReader->select($checkCompQuery, "", 1, 0, 0, 0, false, false);



if ($tableName != "") {
    print '<aside id="records">';
    print '<table id = "table" align= "center">';
    print '<tr>';
    $columns = 0;
    print'<th><b>Patient ID:</b></th>';
    foreach ($compliance as $field) {
        $columns++;
    }
    print '</tr>';
}

////now print out each record
$query = "SELECT pmkPatientID FROM " . $tableName . " WHERE fldWeekCompliance = 0 ORDER BY fldLastUpdate ";
$info3 = $thisDatabaseReader->select($query, "", 1, 1, 0, 0, false, false);
foreach ($info3 as $rec) {
    print '<tr>';
    for ($i = 0; $i < $columns; $i++) {
        print '<td>' . $rec[$i] . '</td>';
    }
    print '</tr>';
}
// all done

print '</aside>';

//-------- BEGIN LISTING PATIENTS FOR DOCTOR WHO IS LOGGED IN--------------------------//

print "<article><div id='box'>";
print"<p>*This is a glance at the patients who are out of compliance. "
        . "More information can be found on the Patient Overview or Patient Sessions pages. </p></br> ";
print "<h2>All Active Patients</h2>";


// Display all the records for a given table
if ($tblPatient != "") {
    print '<aside id="records">';
    $query = 'SHOW COLUMNS FROM ' . $tblPatient;
    $info = $thisDatabaseReader->select($query, "", 0, 0, 0, 0, false, false);
    $span = count($info);
    print "<table id ='table'>";
// print out the column headings, note i always use a 3 letter prefix
// and camel case like pmkCustomerId and fldFirstName
    print '<tr>';
    $columns = 0;
    foreach ($info as $field) {
        print '<td><b>';
        $camelCase = preg_split('/(?=[A-Z])/', substr($field[0], 3));
        foreach ($camelCase as $one) {
            print $one . " ";
        }
        print '</b></td>';
        $columns++;
    }
    print '</tr>';
//now print out each record
    $query = "SELECT * FROM $tblPatient WHERE fnkDocID = '" . $_SESSION['DocID']. "'";
    
    $info2 = $thisDatabaseReader->select($query, "", 1, 0, 2, 0, false, false);
    foreach ($info2 as $rec) {
        print '<tr>';
        for ($i = 0; $i < $columns; $i++) {
            print '<td>' . htmlentities($rec[$i], ENT_QUOTES) . '</td>';
        }
        print '</tr>';
    }
// all done
    print '</table>';
    print '</aside>';
}




print '</div></article>';

include "footer.php";
?>