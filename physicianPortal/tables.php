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
session_start();
if (!isset($_SESSION['DocID'])) {
 header("Location: login.php");
}
$tableName = "";
if (isset($_GET['getRecordsFor'])) {
    $tableName = $_GET['getRecordsFor'];
}
// Begin output
print '<article>';
print '<h2>Database: ' . DATABASE_NAME . '</h2>';
// print out a list of all the tables and their description
// make each table name a link to display the record
print '<section id="tables2" class="float_left">';
print '<table>';
$query = 'SHOW TABLES';
$results = $thisDatabaseReader->select($query, "", 0, 0, 0, 0, false, false);
// loop through all the tables in the database, display fields and properties
foreach ($results as $row) {
    // table name link
    print '<tr class="odd">';
    print '<th colspan="6">';
    print '<a href="?getRecordsFor=' . htmlentities($row[0], ENT_QUOTES) . "#" . htmlentities($row[0], ENT_QUOTES) . '">';
    print htmlentities($row[0], ENT_QUOTES) . '</a>';
    print '</th>';
    print '</tr>';
    //get the fields and any information about them
    $query = 'SHOW COLUMNS FROM ' . $row[0];
    $results2 = $thisDatabaseReader->select($query, "", 0, 0, 0, 0, false, false);
    foreach ($results2 as $row2) {
        print '<tr>';
        print '<td>' . $row2['Field'] . '</td>';
        print '<td>' . $row2['Type'] . '</td>';
        print '<td>' . $row2['Null'] . '</td>';
        print '<td>' . $row2['Key'] . '</td>';
      //  print '<td>' . $row2['Extra'] . '</td>';
        print '</tr>';
    }
}
print '</table>';
print '</section>';
// Display all the records for a given table
if ($tableName != "") {
    print '<aside id="records">';
    $query = 'SHOW COLUMNS FROM ' . $tableName;
    $info = $thisDatabaseReader->select($query,  "", 0, 0, 0, 0, false, false);
    $span = count($info);
    //print out the table name and how many records there are
    print '<table>';
    $query = 'SELECT * FROM ' . $tableName;
    $a = $thisDatabaseReader->select($query,  "", 0, 0, 0, 0, false, false);
    print '<tr>';
    print '<th colspan=' . $span . '>' . $query;
    print '</th>';
    print '</tr>';
    print '<tr>';
    print '<th colspan=' . $span . '>' . $tableName;
    print ' ' . count($a) . ' records';
    print '</th>';
    print '</tr>';
    // print out the column headings, note i always use a 3 letter prefix
    // and camel case like pmkCustomerId and fldFirstName
    print '<tr>';
    $columns = 0;
    foreach ($info as $field) {
        print '<td>';
        $camelCase = preg_split('/(?=[A-Z])/', substr($field[0], 3));
        foreach ($camelCase as $one) {
            print $one . " ";
        }
        print '</td>';
        $columns++;
    }
    print '</tr>';
    //now print out each record
    $query = 'SELECT * FROM ' . $tableName;
    $info2 = $thisDatabaseReader->select($query, "", 0, 0, 0, 0, false, false);
    $highlight = 0; // used to highlight alternate rows
    foreach ($info2 as $rec) {
        $highlight++;
        if ($highlight % 2 != 0) {
            $style = ' odd ';
        } else {
            $style = ' even ';
        }
        print '<tr class="' . $style . '">';
        for ($i = 0; $i < $columns; $i++) {
            print '<td>' . htmlentities($rec[$i], ENT_QUOTES) . '</td>';
        }
        print '</tr>';
    }
    // all done
    print '</table>';
    print '</aside>';
}
print '</article>';
include "footer.php";
?>