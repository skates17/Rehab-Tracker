<?php
/*
 * The purpose of this page is to show granular information for an individual patient
 * this displays:
 * 1) an option to select another patient/go back
 * 2) the ability to update a patient's goal intensity based on progress
 * 3) if the patient is in/out of compliance
 */

include 'top.php';
//initialize the patient variable from selectPatient.php to use in patientSessions.php
$patient = $_SESSION['patient'];
?>

<!--1) an option to select another patient/go back-->
<input type="submit" value="Select another patient" class="button" onclick="goBack()">
<br>

<script>
    function goBack() {
        window.history.back();
    }
</script>

<?php

// 
//2) the ability to update a patient's goal intensity based on progress
include 'updateIntensity.php';


print '<br>';

//--------------------------3 DISPLAY PATIENT INFORMATION ---------------------
print"<h2>Patient " . $patient . " 's Information </h2>";

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

//-------------------------DISPLAY PATIENT INFORMATION--------------------------------
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
$querypatient = "SELECT * FROM " . $tblPatient .
        " WHERE pmkPatientID = '$patient' "
        //    . "GROUP BY pmkPatientID "
        . "ORDER BY pmkPatientID ";
$infopatient = $thisDatabaseReader->select($querypatient, "", 1, 1, 2, 0, false, false);
foreach ($infopatient as $rec) {
    print '<tr>';
    for ($i = 0; $i < $columns; $i++) {
        if ($i == 3) { //$i=3 is fldPatientEmail --> format so is a clickable link
            print '<td><a href="mailto:' . $rec[$i] . '">' . $rec[$i] . '</a></td>';
        } else {
            print '<td>' . $rec[$i] . '</td>';
        }
    }
    print '</tr>';
}
print '</table><br>';



//-------------------------DISPLAY ALL PATIENT SESSION HISTORY -------------------------
// Display all the records for a given table
print'<h2>Session History </h2>';
if ($tblSession != "") {
    print '<aside id="records">';
//                    $query2 = 'SHOW COLUMNS FROM ' . $tblSession;
//                    $info = $thisDatabaseReader->select($query2, "", 0, 0, 0, 0, false, false);
//                    $span = count($info);
//                    
//--------------------- hard coded table headers b/c used a join with another table-------------                
    ?>  

    <table> 
        <tr><b>
            <th>PatientID</th>
            <th>Sess Num </th> 
            <th> Session Compliance </th>
            <th>Intensity 1</th>
            <th>Intensity 2</th>
            <th>Goal </th>
            <th>Note </th>
        </b> </tr>
    <?php
}
////now print out each record
$query = "SELECT tblPatient.pmkPatientID, fldSessNum, fldSessionCompliance, fldIntensity1, fldIntensity2, fldGoal, fldNote "
        . "FROM " . "$tblSession JOIN tblPatient ON tblPatient.pmkPatientID=tblSession.pmkPatientID " .
        " WHERE tblPatient.pmkPatientID = '$patient'"
        . " ORDER BY fldSessNum DESC ";

$info3 = $thisDatabaseReader->select($query, "", 1, 1, 2, 0, false, false);
$columns = 7; //hard coded this value b/c hard coded headers where columns was calculated
foreach ($info3 as $rec) {
    print '<tr>';
    for ($i = 0; $i < $columns; $i++) {
        if ($i == 2) { ///$i=2 is the 3rd table row = session compliance
            $percentage = $rec[$i] * 100;
            print '<td> ' . $percentage . '% </td>';
        } else {
            print '<td>' . $rec[$i] . '</td>';
        }
    }
    print '</tr>';
}
print '</table>';
// all done

print '</aside>';

print '</article><br>';

include 'footer.php';

