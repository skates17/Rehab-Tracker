<?php
/* the purpose of this page is to display a form to allow a user and allow us
 * to add a new user or update an existing user 
 * 
 * Written By: Meaghan Winter

 */

include "top.php";
?>

<?php
//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1 Initialize variables
$debug = false;
$update = false;

// SECTION: 1a.
//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1b Security
//
// define security variable to be used in SECTION 2a.
$yourURL = $domain . $phpSelf;

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1c form variables
//
// Initialize variables one for each form element
// in the order they appear on the form


$DocID = $resultsInfo [0]['fnkCLIN'];
$pmkPatientID = $resultsInfo[0]["pmkPatientID"];
$patientEmail = $resultsInfo[0]["fldPatientEmail"];
$phone = $resultsInfo[0]['fldPhone'];
$active = $resultsInfo[0]['fldActive'];
$lastUpdate = $resultsInfo[0]['fldDeviceSynced'];
$dateCreated = $resultsInfo[0]['fldStartDate'];



$queryD = "SELECT DocID ";
$queryD .= "FROM tblDoctor ";
$queryD .= "ORDER BY DocID";



$doctorList = $thisDatabaseReader->select($queryD, "", 0, 1, 0, 0, false, false);

if (isset($_POST['btnSubmit'])) {
    foreach (($_POST['DocID']) as $DocID) {
        $DocID = $DocID;
    }
}

//
//query for movie pick initialization 
if ($debug) {
    print '<p> initialize variables</p>';
} else {
    $DocID = "";
    $pmkPatientID = "";
    $patientEmail = "";
    $phone = '';
    // $update = "";
}

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1d form error flags
//
// Initialize Error Flags one for each form element we validate
// in the order they appear in section 1c.
$DocIDERROR = false;
$pmkPatientIDERROR = false;
$patientEmailERROR = false;
$phoneERROR = false;



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
//
if (isset($_POST["btnSubmit"])) {
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

    $DocID = htmlentities($_POST["fnkCLIN"], ENT_QUOTES, "UTF-8");
    $resultsInfo[] = $DocID;

    $pmkPatientID = htmlentities($_POST["pmkPatientID"], ENT_QUOTES, "UTF-8");
    $resultsInfo[] = $pmkPatientID;

    $patientEmail = filter_var($_POST["fldPatientEmail"], FILTER_SANITIZE_EMAIL, 'UTF-8');
    $resultsInfo[] = $patientEmail;

    $phone = htmlentities($_POST['fldPhone'], ENT_QUOTES, 'UTF-8');
    $resultsInfo[] = $phone;


//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2c Validation
//

    if ($DocID == "") {
        $errorMsg[] = "Please enter Clinician ID";
        $DocIDERROR = true;
    }

    if ($pmkPatientID == "") {
        $errorMsg[] = "Please enter your PatientID";
        $pmkPatientIDERROR = true;
    }

//email checking
    if ($patientEmail == "") {
        $errorMsg[] = "Please enter patient email address";
        $patientEmailERROR = true;
    } elseif (!verifyEmail($patientEmail)) {
        $errorMsg[] = "Your email address appears to be incorrect.";
        $patientEmailERROR = true;
    }

    if ($phone == "") {
        $errorMsg[] = "Please enter patient's phone number";
        $phoneERROR = true;
    } elseif (!verifyPhone($phone)) {
        $errorMsg[] = "Your phone number appears to be incorrect.";
        $phoneERROR = true;
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

        //    $dataEntered = false;
        try {

            if ($debug) {
                print '<p> before begin transaction</p>';
            }
            $thisDatabaseWriter->db->beginTransaction();
            if ($debug) {
                print '<p> begin transaction</p>';
            }
//if($update){
//    $query= 'UPDATE tblPatient SET ';
//} else{
            // $query = 'if NOT exists (SELECT pmkPatientID = ? from tblPatient';
            $query = 'INSERT INTO tblPatient SET ';
//}
            $query .= 'fnkCLIN = ?, ';
            $query .= 'pmkPatientID = ?, ';
            $query .= 'fldPatientEmail = ?, ';
            $query .= 'fldPhone = ?, ';
            $query .= 'fldActive = 1, ';
            $query .= 'fldDeviceSynced = CURRENT_TIMESTAMP, ';
            $query .= 'fldStartDate = CURRENT_TIMESTAMP ';



            $results = $thisDatabaseWriter->insert($query, $resultsInfo, 0, 0, 0, 0, false, false);

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

if (isset($_POST["btnSubmit"]) AND empty($errorMsg)) {
    $message = '<h2>You have successfully been registered for a user account with Rehabilitation Compliance</h2>';
    $message .= 'Please consult with your doctor for more registration details.</p>';
    $message .= '<p>Username: ' . $pmkPatientID;

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2g Mail to user
//
// Process for mailing a message which contains the forms data
// the message was built in section 2f.
    $to = $patientEmail; // the person who filled out the form
    $cc = "";
    $bcc = "";
    $from = "Rehab Compliance Registration  <contact@complianceregistration.com>";

// subject of mail should make sense to your form
    $todaysDate = strftime("%x");
    $subject = "Registration Complete: " . $todaysDate;

    $mailed = sendMail($to, $cc, $bcc, $from, $subject, $message);
}

//#############################################################################
//
// SECTION 3 Display Form
//
?>
<article id="main">
    <?php
//####################################
// SECTION 3a.
// If its the first time coming to the form or there are errors we are going
// to display the form.
    if (isset($_POST["btnSubmit"]) AND empty($errorMsg)) { // closing of if marked with: end body submit
        print "<h1>Your Request has ";
        if (!$mailed) {
            print "not ";
        }
        print "been processed</h1>";
        print "<p>A copy of this message has ";
        if (!$mailed) {
            print "not ";
        }
        print "been sent</p>";
        print "<p>To: " . $patientEmail . "</p>";
        print $message;
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
        <!--<div>-->
        <h2>Add Patient</h2>
        <form action="<?php print $phpSelf; ?>"
              method="post"
              id="frmRegister">
            <!--<fieldset>-->
                <div class="form-group">
                <!--class="wrapper">-->
                <!--                    <legend><h2>Add Patient</h2></legend><br>-->
                <?php
                print '<label for="fnkCLIN"><b>Clinician</b> ';
                print '<select id="fnkCLIN" name = "fnkCLIN"';
                print '        name="fnkCLIN" ';
                print '        tabindex="100" placeholder="Enter Clinician ID" >';
                if ($DocIDERROR)
                    print 'class="mistake"';

                foreach ($doctorList as $row) {
                    print '<option ';
                    if ($doctorList == $row["DocID"])
                        print " value='selected' ";
                    print 'value="' . $row["DocID"] . '">' . $row["DocID"];
                    print '</option>';
                }
                print '</select></label><br>';
                ?>
                </div>

                <div class="form-group">
                    <label for="pmkPatientID" class="required"><b>Patient ID</b>
                    <input type="text" id="pmkPatientID" name="pmkPatientID"
                           value="<?php print $pmkPatientID; ?>"
                           tabindex="110" maxlength="45" placeholder="Enter patient ID"
                           <?php if ($pmkPatientIDERROR) print 'class="mistake"'; ?>
                           onfocus="this.select()"
                           >
                </label><br>
                </div>

                <div class="form-group">
                    <label for="fldPatientEmail" class="required"> <b>Patient Email</b>
                    <input type="text" id="fldPatientEmail" name="fldPatientEmail"
                           value="<?php print $patientEmail; ?>"
                           tabindex="120" maxlength="45" placeholder="Enter patient's email address"
                           <?php if ($patientEmailERROR) print 'class="mistake"'; ?>
                           onfocus="this.select()" 
                           autofocus>
                </label><br>
                </div>

                <div class="form-group">
                    <label for="fldPhone" class="required"><b> Patient Phone #</b>
                    <input type="text" id="fldPhone" name="fldPhone"
                           value="<?php print $phone; ?>"
                           tabindex="130" maxlength="45" placeholder="Enter patient's phone #"
                           <?php if ($phoneERROR) print 'class="mistake"'; ?>
                           onfocus="this.select()" 
                           autofocus>
                </label><br>
                </div>
                
                <!--</fieldset>  ends contact -->

    <div class="form-group">
                <!--<fieldset class="buttons">-->
                <input type="submit" id="btnSubmit" name="btnSubmit" value="Submit" tabindex="900" class="button">
            <!--</fieldset>  ends buttons -->
    </div>
        </form>
        <?php
    } // end body submit
    ?>
    </article>
    <!--</div>-->


    <?php
//include "footer.php";
    if ($debug)
        print "<p>END OF PROCESSING</p>";
    ?>