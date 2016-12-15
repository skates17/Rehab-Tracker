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

print'<h2>Patient Overview</h2>';
print 'Welcome to Patient Overview where you see each patient, their <b>Last Updated</b> date (when they last synched),'
        . '<b>Start Date</b> (when they started the program), <b>Last Checked</b> (when WE last checked their compliance),'
        . 'and finally, <b>Week Compliance</b> (are they doing their job).';
// Begin output
print "<article><div id='box'>";
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
    $query = 'SELECT * FROM ' . $tblPatient;
    $info2 = $thisDatabaseReader->select($query, "", 0, 0, 0, 0, false, false);
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
print '</div>';
print '</article>';
print '<p><i>If the “Last Checked” date is empty because the patient is new, then  '
. '“Last Checked” is updated with their “Start Date” that their doctor entered at the '
        . 'beginning of their account creation. This “Last Checked” '
        . 'field is how the compliance check PHP file knows when to check that user for '
        . 'weekly compliance. When this date is seven days prior to the current date, '
        . 'that user is checked for compliance and “Last Checked” is then updated with the current date. '
        . 'They will then be checked seven days from that new date.</i></p>';

//--------------------TBLSESSION INFORMATION--------------------------------

// Begin output

print' <h2>Patient Sessions</h2>';
print "<article><div id= 'box'>";

// Display all the records for a given table
if ($tblSession != "") {
    print '<aside id="records">';
    $query = 'SHOW COLUMNS FROM ' . $tblSession;
    $info = $thisDatabaseReader->select($query, "", 0, 0, 0, 0, false, false);
    $span = count($info);
//print out the table name and how many records there are
    print "<table id='table'>";
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
    $query = "SELECT * FROM ".  $tblSession;
    $info3 = $thisDatabaseReader->select($query, "", 0, 0, 0, 0, false, false);
    foreach ($info3 as $rec) {
        print '<tr>';
        for ($i = 0; $i < $columns; $i++) {
            print '<td>' . $rec[$i] . '</td>';
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