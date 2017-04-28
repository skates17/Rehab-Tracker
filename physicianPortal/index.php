<?php
include "top.php";
?>
<?php

//--------------------Table displays summary of patients OUT of weekly compliance----------------
print "<article><h2>Patients who are OUT of compliance: </h2>";

$tableName = tblPatient;

$checkCompQuery = "SELECT pmkPatientID, fldWeekCompliance, fldPhone, fldPatientEmail FROM tblPatient ";
$complianceColumn = $thisDatabaseReader->select($checkCompQuery, "", 0, 0, 0, 0, false, false);

if ($tableName != "") {
    print '<aside id="records">';
    print '<table id = "table">';
    print '<tr>';
    $columns = 0;
    print'<th><b>Patient ID:</b></th>';
    print'<th><b>Compliance:</b></th>';
    print '<th><b>Phone: </b></th>';
    print '<th><b>Patient Email: </b></th>';
//    foreach ($complianceColumn as $field) {
//        $columns++;
//    }
    $columns = 4;
    print '</tr>';
}
print '</article>';
////now print out each record
$notCompliant = "SELECT pmkPatientID, fldWeekCompliance, fldPhone, fldPatientEmail FROM " 
        . $tableName . " WHERE fldWeekCompliance < 1 and fnkCLIN = '" . $_SESSION['DocID'] . "'"
        . " ORDER BY fldWeekCompliance ";
$complianceResult = $thisDatabaseReader->select($notCompliant, "", 1, 2, 2, 1, false, false);
foreach ($complianceResult as $rec) {
    print '<tr>';
    for ($i = 0; $i < $columns; $i++) {
        if ($i == 1) { //$i=1 is fldWeekCompliance --> format so a % is displayed
            $percentageTop = $rec[$i] * 100;
            print '<td>'.$percentageTop . '% </td>';
        } elseif ($i ==3){//$i=3 is fldPatientEmail --> format so is a clickable link
                print '<td><a href="mailto:' . $rec[$i] . '">' . $rec[$i] . '</a></td>';
        } else{
            print '<td>' . $rec[$i] . '</td>';
        }
    }
    print '</tr>';
}
print '</table>';

// all done

print '</aside>';
?>

<!-- ------ TABLE LISTS ALL PATIENTS FOR LOGGED IN CLINICIAN------------------------ -->

<article><div id='box'>
<!--        <p>*This is a glance at the patients who are out of compliance. </p>
        <p>  More information can be found on the Patient Overview or Patient Sessions pages. </p></br>-->
        <h2>Summary of Active Patients for Clinician <?php print $_SESSION['DocID']; ?> </h2>


<?php
//Display all the records for a given table
if ($tblPatient != "") {


    print '<aside id="records">';
//    $query = 'SHOW COLUMNS FROM ' . $tblPatient;
//    
//    $info = $thisDatabaseReader->select($query, "", 0, 0, 0, 0, false, false);

    $columnOverview = "SHOW COLUMNS FROM tblPatient where "
            . "FIELD NOT IN ('fnkCLIN', 'fldActive', 'fldComplianceChecked', 'fldGoal',"
            . "'fldPatientEmail', 'fldPhone')";


    $overviewDisplay = $thisDatabaseReader->select($columnOverview, "", 1, 1, 12, 0, false, false);

    $span = count($overviewDisplay);
    print "<table id ='table'>";
// print out the column headings, note i always use a 3 letter prefix
// and camel case like pmkCustomerId and fldFirstName
    print '<tr>';
    $columns = 0;
    foreach ($overviewDisplay as $field) {
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
    $patientSummary = "SELECT pmkPatientID, "
           // . "fldPatientEmail, fldPhone, "
            . "fldDeviceSynced, fldStartDate, fldWeekCompliance"
            . " FROM $tblPatient WHERE fnkCLIN = '" . $_SESSION['DocID'] . "'";
    
    $displaySummary = $thisDatabaseReader->select($patientSummary, "", 1, 0, 2, 0, false, false);
    foreach ($displaySummary as $rec) {
        print '<tr>';
        for ($i = 0; $i < $columns; $i++) {
            print '<td>';
//            if ($i == 1) { //$i=1 is fldPatientEmail --> format so is a clickable link
//                print '<a href="mailto:' . $rec[$i] . '">' . $rec[$i] . '</a></td>';
//            } elseif
            if ($i == 3) { //$i=3 is fldWeekCompliance --> format so a % is displayed
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




print '</div></article>';

include "footer.php";
?>