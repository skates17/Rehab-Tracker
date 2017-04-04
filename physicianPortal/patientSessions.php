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
$querypatient = "SELECT * FROM " . $tblPatient . 
        " WHERE pmkPatientID = '$patient' "
    //    . "GROUP BY pmkPatientID "
        . "ORDER BY pmkPatientID ";
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

print '<h2>Update Intensity</h2>';

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1 Initialize variables
$debug = false;
$update = true;

// SECTION: 1a.
//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//


//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1c form variables
//
// Initialize variables one for each form element
// in the order they appear on the form
$goal = $resultsInfo[0]['fldGoal'];
//
//query for movie pick initialization 
if ($debug) {
    print '<p> initialize variables</p>';
} else {
    $goal = "";
}

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1d form error flags
//
// Initialize Error Flags one for each form element we validate
// in the order they appear in section 1c.
$goalERROR = false;

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1e misc variables
//
// create array to hold error messages filled (if any) in 2d displayed in 3c.
$errorMsg = array();
$data = array();
$dataEntered = false;

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2 Process for when the form is submitted

// Step Three: code can be in initialize variables or where step four needs to be
// $patient is an associative array

if (isset($_POST["btnUpdate"])) {

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2a Security
//
    /*    if (!securityCheck(true)) {
      $msg = "<p>Sorry you cannot access this page. ";
      $msg.= "Security breach detected and reported</p>";
      die($msg);
      }
     */
//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2b Sanitize (clean) data
// remove any potential JavaScript or html code from users input on the
// form. Note it is best to follow the same order as declared in section 1c.
    $goal = htmlentities($_POST["fldGoal"], ENT_QUOTES, "UTF-8");
    $parameters[] = $goal;

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2c Validation
//
    if ($goal == "") {
        $errorMsg[] = "Please set a patient Goal";
        $goalERROR = true;
    }

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2d Process Form - Passed Validation
//
// Process for when the form passes validation (the errorMsg array is empty)
//

    if (!$errorMsg) {
        if ($debug) {
            print '<p> 2d';
            print "<p>Form is valid</p>";
        }

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2e Save Data
        if ($debug) {
            print '<p> 2e';
        }

        $dataEntered = false;
        try {

            if ($debug) {
                print '<p> before begin transaction</p>';
            }
            $thisDatabaseWriter->db->beginTransaction();
            if ($debug) {
                print '<p> begin transaction</p>';
            }

            $queryUpdate = "UPDATE tblPatient SET fldGoal = ?, "
                    . "fldLastUpdate = CURRENT_TIMESTAMP WHERE pmkPatientID = $patient";
            $results = $thisDatabaseWriter->select($queryUpdate, $parameters, 1, 0, 0, 0, false, false);

            // all sql statements are done so lets commit to our changes

            $dataEntered = $thisDatabaseWriter->db->commit();

            if ($debug)
                print "<p>transaction complete ";
        } catch (PDOExecption $e) {
            $thisDatabase->db->rollback();
            if ($debug)
                print "Error!: " . $e->getMessage() . "</br>";
            $errorMsg[] = "There was a problem with accpeting your data please contact us directly.";
        }
    } // end form is valid
} // ends if form was submitted.
//#############################################################################
//
// SECTION 3 Display Form
//
//####################################
// SECTION 3a.
// If its the first time coming to the form or there are errors we are going
// to display the form.
if (isset($_POST["btnUpdate"]) AND empty($errorMsg)) { // closing of if marked with: end body submit
    print "<h1>Success!</h1>";
    print "<p>You have successfully updated $patient 's goal to:  '$goal'. ";
} else {
//####################################
//
// SECTION 3b Error Messages
//
// display any error messages before we print out the form
    if ($errorMsg) {
        print '<div id="errors">';
        print '<h1>Your form has the following mistakes</h1>';

        print "<ol>\n";
        foreach ($errorMsg as $err) {
            print "<li>" . $err . "</li>\n";
        }
        print "</ol>\n";
        print '</div>';
    }
//####################################
//
// SECTION 3c html Form
//
    /* Display the HTML form. note that the action is to this same page. $phpSelf
      is defined in top.php
      NOTE the line:
      value="<?php print $email; ?>
      this makes the form sticky by displaying either the initial default value (line 35)
      or the value they typed in (line 84)
      NOTE this line:
      <?php if($emailERROR) print 'class="mistake"'; ?>
      this prints out a css class so that we can highlight the background etc. to
      make it stand out that a mistake happened here.
     */
    ?>
    <div>
        <p>Patient:  <?php print $patient; ?> 
        <form action="<?php print $phpSelf; ?>"
              method="post"
              id="frmRegister">
            <fieldset>

                <label for="fldGoal" class="required"> Goal Intensity
                    <input type="text" id="fldGoal" name="fldGoal"
                           value="<?php print $goal; ?>"
                           tabindex="140" maxlength="3" placeholder="Enter goal intensity"
                           <?php if ($goalERROR) print 'class="mistake"'; ?>
                           onfocus="this.select()" 
                           autofocus>

                    <input type="submit" id="btnUpdate" name="btnUpdate" value="Update" tabindex="900" class="button">
                    </fieldset> <!-- ends buttons -->
                    </form>

                    </div>
                    <?php
                } // end body submit
//include 'updateIntensity.php';
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
                $query = "SELECT * FROM " . $tblSession . " WHERE pmkPatientID = '$patient' "
                       // . " GROUP BY fldSessNum "
                        . "ORDER BY fldDate DESC ";
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

                