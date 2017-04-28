<?php

$patient = $_SESSION['patient'];

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

            $queryUpdate = "UPDATE tblPatient SET fldGoal = ? ";
                //    . ", fldLastUpdate = CURRENT_TIMESTAMP 
            $queryUpdate .= "WHERE pmkPatientID = '$patient'";
            $results = $thisDatabaseWriter->select($queryUpdate, $parameters, 1, 0, 2, 0, false, false);

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
    echo "<script type='text/javascript'>alert('Updated goal successfully')</script>";
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
              id="frmUpdate">
            <fieldset>

                <label for="fldGoal" class="required"> Goal Intensity
                    <input type="text" id="fldGoal" name="fldGoal"
                           value="<?php print $goal; ?>"
                           tabindex="140" maxlength="3" placeholder="Enter goal intensity"
                           <?php if ($goalERROR) print 'class="mistake"'; ?>
                           onfocus="this.select()" 
                           autofocus>
                </label>
                <input type="submit" id="btnUpdate" name="btnUpdate" value="Update" tabindex="900" class="button">
            </fieldset> <!-- ends buttons -->
        </form>

    </div>
    <?php
} // end body submit