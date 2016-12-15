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
print '<p>In this portal, you are able to view: '
        . "<ul style='list-style-type:disc'><li><a href='viewCompliance.php'>Patients Overview </a>"
        . "to see each patient's information as well as all patients and their recent sessions.</li>"
        . "<li><a href='patientSessions.php'>Patient Sessions </a>"
        . "which shows detailed session information for a specified patient to track their progress.</li>"
        . "<li><a href='managePatients.php'>Manage Patients </a>"
        . "and "
        . "<a href='addPhysician.php'>Manage Physicians </a>"
        . "to add or deactivate them.</li></ul></p>"
        . "<br>";

print'<h2>Patient Overview</h2>';

$tableName = tblPatient;

print"<p>Here is a quick glance at the patients who are out of compliance. "
        . "More information can be found on the Patient Overview or Patient Sessions pages. "
        . "The following patients are OUT of weekly compliance: ";

$checkCompQuery = "SELECT pmkPatientID FROM tblPatient WHERE fldWeekCompliance = 0";
$compliance = $thisDatabaseReader->select($checkCompQuery, "", 1, 0, 0, 0, false, false);

if ($tableName != "") {
    print '<aside id="records">';
    print '<table id = "table">';
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

print '</article>';

include "footer.php";
?>