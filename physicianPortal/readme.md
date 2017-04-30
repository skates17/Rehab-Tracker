**The physicianPortal folder refers to the code used in the online portal.**


**Purpose:** Clinicians can use this portal to view their pateints' compliance and update target intensity. 
-- Admin users have access to add other clinicians and/or patients andcan also view all patient information


**bin:**
-database.php, pass.php --> these files are used in all files to access the database except for login.php and logout.php (which use .dbh to connect to the database)
-all code connects to Brandon Goodwin's database through webdb at UVM

**lib:**
-contains constants, custom-functions, mail-message, security, and validation-functions
    --if you are adding a new page/document to website, make sure to add to whiteListPages[] on security.php
    --all sql function definitions to read/write to/from database
    
   
**Path of file usage:**
-- start at index.php to see an overview of patients (redirected to login.php if not logged in yet)
--> navigate to All Patients on nav bar (patientSessions.php) to see all patient information for the clinician who is logged in
--> navigate to Patient Sessions on nav bar (selectPatient.php) and select a patient --> sets global variable for $_SESSION('fldPatient')
--> auto-directs to PatientSessions.php where can see all patient information and sessions, ability to update intensity (updateIntensity.php)
----> ability to go back to select another patient, re sets the global variable

--click "logout" to log out (logout.php) and get redirected to login page (login.php)


**The following only shows up if on Admin account**
--> navigate to Add Clinician (addPhysician.php), add another clinician
--> navigate to Add Patient (form.php), add another patient



