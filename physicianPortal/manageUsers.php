<?php
include 'top.php';
//include 'form.php';
//include 'managePatients.php';
//print '<br>';
//include 'addPhysician.php';

/* the purpose of this page is to display a form to allow a user and allow us
 * to add a new user or update an existing user 
 * 
 * Written By: Meaghan Winter

 */

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


$DocID = $resultsInfo [0]['DocID'];
$fldEmail = $resultsInfo[0]["fldEmail"];
$fldPass = $resultsInfo[0]["fldPass"];
$fldAdmin = $resultsInfo[0]['fldAdmin'];

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
    $fldEmail = "";
    $fldPass = "";
    $fldAdmin = '';
}

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1d form error flags
//
// Initialize Error Flags one for each form element we validate
// in the order they appear in section 1c.
$DocIDERROR = false;
$fldEmailERROR = false;
$fldPassERROR = false;
$fldAdminERROR = false;



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

    $DocID = htmlentities($_POST["DocID"], ENT_QUOTES, "UTF-8");
    $resultsInfo[] = $DocID;

    $fldEmail = filter_var($_POST["fldEmail"], FILTER_SANITIZE_EMAIL, 'UTF-8');
    $resultsInfo[] = $fldEmail;

    $fldPass = htmlentities($_POST["fldPass"], ENT_QUOTES, "UTF-8");
    $resultsInfo[] = $fldPass;

    $fldAdmin = htmlentities($_POST['fldAdmin'], ENT_QUOTES, 'UTF-8');
    $resultsInfo[] = $fldAdmin;


//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2c Validation
//

    if ($DocID == "") {
        $errorMsg[] = "Please enter Clinician ID";
        $DocIDERROR = true;
    }

    //email checking
    if ($fldEmail == "") {
        $errorMsg[] = "Please enter patient email address";
        $fldEmailERROR = true;
    } elseif (!verifyEmail($fldEmail)) {
        $errorMsg[] = "Your email address appears to be incorrect.";
        $fldEmailERROR = true;
    }


    if ($fldPass == "") {
        $errorMsg[] = "Please enter your password";
        $fldPassERROR = true;
    }

    if ($fldAdmin == "") {
        $errorMsg[] = "Please select admin status";
        $fldAdminERROR = true;
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
            $query = 'INSERT INTO tblDoctor SET ';
//}
            $query .= 'DocID = ?, ';
            $query .= 'fldEmail = ?, ';
            $query .= 'fldPass = ?, ';
            $query .= 'fldAdmin = ? ';

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
    $message = '<h2>You have successfully been registered for a CLINICIAN account with Rehabilitation Compliance</h2>';
    $message .= '<p>Clinician: ' . $DocID;

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2g Mail to user
//
// Process for mailing a message which contains the forms data
// the message was built in section 2f.
    $to = $fldEmail; // the person who filled out the form
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
        print "<p>To: " . $fldEmail . "</p>";
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
        <h2>Add Clinician </h2>

        <form action="<?php print $phpSelf; ?>"
              method="post"
              id="frmRegister">

            <div class="form-group">
                <label for="DocID" class="required"><b>Clinician ID</b>
                    <input type="text" id="DocID" name="DocID"
                           value="<?php print $DocID; ?>"
                           tabindex="110" maxlength="45" placeholder="Enter Clinician ID"
                           <?php if ($DocIDERROR) print 'class="mistake"'; ?>
                           onfocus="this.select()"
                           >
                </label><br>
            </div>

            <div class="form-group">
                <label for="fldEmail" class="required"><b> Clinician Email</b>
                    <input type="text" id="fldEmail" name="fldEmail"
                           value="<?php print $fldEmail; ?>"
                           tabindex="120" maxlength="45" placeholder="Enter clinician's email address"
                           <?php if ($fldEmailERROR) print 'class="mistake"'; ?>
                           onfocus="this.select()" 
                           autofocus>
                </label><br>
            </div>

            <div class="form-group">
                <label for="fldPass" class="required"><b> Password</b>
                    <input type="password" id="fldPass" name="fldPass"
                           value="<?php print $fldPass; ?>"
                           tabindex="130" maxlength="45" placeholder="Enter password"
                           <?php if ($fldPassERROR) print 'class="mistake"'; ?>
                           onfocus="this.select()" 
                           autofocus>
                </label><br>
            </div>

            <div class ="form-group">
                <label for="fldAdmin" class = "required"> <b>Security Access</b>
                       <input type="radio" name="fldAdmin" value="0"> Clinician Only
                    <input type="radio" name="fldAdmin" value="1"> Admin
                </label>

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

