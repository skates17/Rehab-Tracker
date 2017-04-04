<?php
include 'top.php';
print '<div id="header">
    <h1>Manage Physicians</h1>
</div>';
//-------------form to add physician to database
include 'addPhysician.php';
print '<br>';
//------------form to update/deactivate a physician

/* the purpose of this page is to display a form to allow a user and allow us
 * to add a new user or update an existing user 
 * 
 * Written By: Meaghan Winter

 */
?>

<h2>Activate/Deactivate Physician</h2>

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
$physician = $resultsInfo [0]['DocID'];
$active = $resultsInfo[0]['fldActive'];
//
//query for movie pick initialization 
if ($debug) {
    print '<p> initialize variables</p>';
} else {
    $physician = "";
    $active = "";
}

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1d form error flags
//
// Initialize Error Flags one for each form element we validate
// in the order they appear in section 1c.
$physicianERROR = false;
$activeERROR = false;

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
$queryP = "SELECT DocID ";
$queryP .= "FROM tblDoctor ";
$queryP .= "ORDER BY  DocID";


// Step Three: code can be in initialize variables or where step four needs to be
// $physician is an associative array
$physicianList = $thisDatabaseReader->select($queryP, "", 0, 1, 0, 0, false, false); //

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
    $physician = htmlentities($_POST["DocID"], ENT_QUOTES, "UTF-8");
    $resultsInfo[] = $physician;
    $active = htmlentities($_POST["fldActive"], ENT_QUOTES, "UTF-8");
    $resultsInfo[] = $active;


//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2c Validation
//

    if ($physician == "") {
        $errorMsg[] = "Please select physician";
        $docIDERROR = true;
    }
    if ($active == "") {
        $errorMsg[] = "Please activate/deactivate physician";
        $activeERROR = true;
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

            $query = "UPDATE tblDoctor SET ";
            $query .= "fldActive = ?, ";
            $query .= "WHERE DocID = '$physician'";
            $results = $thisDatabaseWriter->select($query, $active, 0, 1, 2, 0, false, false);

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
?>

<?php
//####################################
// SECTION 3a.
// If its the first time coming to the form or there are errors we are going
// to display the form.
if (isset($_POST["btnUpdate"]) AND empty($errorMsg)) { // closing of if marked with: end body submit
    print "<h1>Success!</h1>";
    print "<p>You have successfully updated '$physician' to '$active'. ";
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
        <form  class = "form-signin"
               action="<?php print $phpSelf; ?>"
               method="post"
               id="frmRegister">
            <fieldset
                <!--                    patient dropdown-->
                <?php
                print '<div class="form-group">'
                        . '<label for="DocID">Physician ';
                print '<select id="DocID" name = "DocID"';
                print '        name="DocID" ';
                print '        tabindex="300" >';


                foreach ($physicianList as $row) {
                    print '<option ';
                    if ($physicianList == $row["DocID"])
                        print " value='selected' ";
                    print 'value="' . $row["DocID"] . '">' . $row["DocID"];
                    print '</option>';
                }
                print '</select></label></div>';
                ?>

                <!--                    <fieldset class="radio">-->

                <br>
                <div class="form-group">                       
                    <label for="radActivate">
                        <input type="radio" 
                               id="radActivate" 
                               name="fldActive" 
                               value="1">Activate
                    </label><br>

                    <label for="radDeactivate">
                        <input type="radio" 
                               id="radDeactivate" 
                               name="fldActive" 
                               value="0">Deactivate
                    </label></div><br>

                <div class="form-group">   <input type="submit" id="btnUpdate" 
                                                  name="btnUpdate" value="Save" tabindex="900" class="button">
                </div>
            </fieldset> <!-- ends buttons -->
        </form>
        <?php
    } // end body submit
    ?>
</article>
</div>


<?php
include "footer.php";
if ($debug)
    print "<p>END OF PROCESSING</p>";
?>