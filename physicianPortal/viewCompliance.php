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
    if ($_SESSION['fldAdmin'] != 1) {
        $query .= " WHERE fnkCLIN = '" . $_SESSION['DocID'] . "'";
        $info2 = $thisDatabaseReader->select($query, "", 1, 0, 2, 0, false, false);
    } else {
        $info2 = $thisDatabaseReader->select($query, "", 0, 0, 0, 0, false, false);
    }

    foreach ($info2 as $rec) {
        print '<tr>';
        for ($i = 0; $i < $columns; $i++) {
            print '<td>';
            if ($i == 3) { //$i=3 is fldPatientEmail --> format so is a clickable link
                print '<a href="mailto:' . $rec[$i] . '">' . $rec[$i] . '</a></td>';
            } elseif ($i == 8) { //$i=8 is fldWeekCompliance --> format so a % is displayed
                $percentage = $rec[$i] * 100;
                print $percentage . '% </td>';
            } else {
                print htmlentities($rec[$i], ENT_QUOTES) . '</td>';
            }
        }
        print '</tr>';
    }
// all done
    print '</table>';
    print '</aside>';
}
print '</div>';
print '</article>';

////--------------------TBLSESSION INFORMATION--------------------------------
//
//// Begin output
//
//print' <h2>Patient Sessions</h2>';
//print "<article><div id= 'box'>";
//
//// Display all the records for a given table
//if ($tblSession != "") {
//    print '<aside id="records">';
//    $query = 'SHOW COLUMNS FROM ' . $tblSession;
//    $info = $thisDatabaseReader->select($query, "", 0, 0, 0, 0, false, false);
//    $span = count($info);
////print out the table name and how many records there are
//    print "<table id='table'>";
//// print out the column headings, note i always use a 3 letter prefix
//// and camel case like pmkCustomerId and fldFirstName
//    print '<tr>';
//    $columns = 0;
//    foreach ($info as $field) {
//        print '<td><b>';
//        $camelCase = preg_split('/(?=[A-Z])/', substr($field[0], 3));
//        foreach ($camelCase as $one) {
//            print $one . " ";
//        }
//        print '</b></td>';
//        $columns++;
//    }
//    print '</tr>';
////now print out each record
//    $query = "SELECT * FROM ".  $tblSession;
//    $info3 = $thisDatabaseReader->select($query, "", 0, 0, 0, 0, false, false);
//    foreach ($info3 as $rec) {
//        print '<tr>';
//        for ($i = 0; $i < $columns; $i++) {
//            print '<td>' . $rec[$i] . '</td>';
//        }
//        print '</tr>';
//    }
//// all done
//    print '</table>';
//    print '</aside>';
//}
print '</div></article>';

include "footer.php";
?>