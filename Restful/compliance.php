<!doctype html>
<html>
	<body>
		<?php
		
		$host_name = 'webdb.uvm.edu';
		$db_name = "BGOODWIN_CS275";
		$user = "bgoodwin_admin";
		$pass = "9ORjQCNJwzLx";

		// Connect to the database
		$connect = mysqli_connect($host_name, $user, $pass, $db_name) or die('Unable to connect.');

		// Check if connection was successful
		if (mysqli_connect_errno()) {
			printf("Connect failed: %s\n", mysqli_connect_error());
			exit();
		}

		$query = "SELECT `pmkPatientID`,`fldStartDate`,`fldComplianceChecked` FROM `tblPatient`";

		$result = mysqli_query($connect, $query) or die(mysqli_error($connect));
		
		while ($row = $result->fetch_assoc()) {
			
			$startDate = $row['fldStartDate'];
			$userID = $row['pmkPatientID'];
			$lastUpdated = $row['fldComplianceChecked'];
		
			$results = str_split($startDate);
		
			$day = (($results[8]).($results[9]));
			$month = (($results[5]).($results[6]));
			$year = (($results[2]).($results[3]));
		
			$newStartDate = (($month)."/".($day)."/".($year));
			
			if($lastUpdated == ""){
				// Connect to the database
				$connect = mysqli_connect($host_name, $user, $pass, $db_name) or die('Unable to connect.');

				// Check if connection was successful
				if (mysqli_connect_errno()) {
					printf("Connect failed: %s\n", mysqli_connect_error());
					exit();
				}

				$query2 = "UPDATE `tblPatient` SET `fldComplianceChecked`='$newStartDate' WHERE `pmkPatientID`='$userID'";

				$result2 = mysqli_query($connect, $query2) or die(mysqli_error($connect));
				
			}
		}

		// Grab the date and format it to math database date
		$date = date("m/d/Y");
		
		$result = str_split($date);
		
		$month = (($result[0]).($result[1]));
		$day = (($result[3]).($result[4]));
		$year = (($result[8]).($result[9]));
		
		$the_date = (($month)."/".($day)."/".($year));
		
		// Calculate the last 7 days
		$seven_days_ago = (date("m/d/Y", strtotime('-7 days')));
		
		$result = str_split($seven_days_ago);
		
		$month = (($result[0]).($result[1]));
		$day = (($result[3]).($result[4]));
		$year = (($result[8]).($result[9]));
		
		$seven_days_ago = (($month)."/".($day)."/".($year));
		
		//Calculate date 6 days ago
		$six_days_ago = (date("m/d/Y", strtotime('-6 days')));
		
		$result = str_split($six_days_ago);
		
		$month = (($result[0]).($result[1]));
		$day = (($result[3]).($result[4]));
		$year = (($result[8]).($result[9]));
		
		$six_days_ago = (($month)."/".($day)."/".($year));
		
		// Calculate date 5 days ago
		$five_days_ago = (date("m/d/Y", strtotime('-5 days')));
		
		$result = str_split($five_days_ago);
		
		$month = (($result[0]).($result[1]));
		$day = (($result[3]).($result[4]));
		$year = (($result[8]).($result[9]));
		
		$five_days_ago = (($month)."/".($day)."/".($year));
		
		// Calculate date 4 days ago
		$four_days_ago = (date("m/d/Y", strtotime('-4 days')));
		
		$result = str_split($four_days_ago);
		
		$month = (($result[0]).($result[1]));
		$day = (($result[3]).($result[4]));
		$year = (($result[8]).($result[9]));
		
		$four_days_ago = (($month)."/".($day)."/".($year));
		
		// Calculate date 3 days ago
		$three_days_ago = (date("m/d/Y", strtotime('-3 days')));
		
		$result = str_split($three_days_ago);
		
		$month = (($result[0]).($result[1]));
		$day = (($result[3]).($result[4]));
		$year = (($result[8]).($result[9]));
		
		$three_days_ago = (($month)."/".($day)."/".($year));
		
		// Calculate date 2 days ago
		$two_days_ago = (date("m/d/Y", strtotime('-2 days')));
		
		$result = str_split($two_days_ago);
		
		$month = (($result[0]).($result[1]));
		$day = (($result[3]).($result[4]));
		$year = (($result[8]).($result[9]));
		
		$two_days_ago = (($month)."/".($day)."/".($year));
		
		// Calculate date 1 day ago
		$one_day_ago = (date("m/d/Y", strtotime('-1 days')));
		
		$result = str_split($one_day_ago);
		
		$month = (($result[0]).($result[1]));
		$day = (($result[3]).($result[4]));
		$year = (($result[8]).($result[9]));
		
		$one_day_ago = (($month)."/".($day)."/".($year));
		
		// Define user array to hold users not in compliance
		$user_array = array();
		$i = 0;
		
		// Connect to the database
		$connect = mysqli_connect($host_name, $user, $pass, $db_name) or die('Unable to connect.');

		// Check if connection was successful
		if (mysqli_connect_errno()) {
			printf("Connect failed: %s\n", mysqli_connect_error());
			exit();
		}

		$query3 = "SELECT `pmkPatientID`,`fldComplianceChecked`,`fnkDocID` FROM `tblPatient`";

		$result3 = mysqli_query($connect, $query3) or die(mysqli_error($connect));
		
		while ($row = $result3->fetch_assoc()) {
			$pmkPatientID = $row['pmkPatientID'];
			$fldComplianceChecked = $row['fldComplianceChecked'];
			$doctor = $row['fnkDocID'];
			
			if($fldComplianceChecked == $seven_days_ago){
				
				// Connect to database and pull dates that match current date
				$connect = mysqli_connect($host_name, $user, $pass, $db_name) or die('Unable to connect.');

				// Check if connection was successful
				if (mysqli_connect_errno()) {
					printf("Connect failed: %s\n", mysqli_connect_error());
					exit();
				}

				$query4 = "SELECT `pmkPatientID`,`fldDate`,`fldSessionCompliance` FROM `tblSession` WHERE (`pmkPatientID`='$pmkPatientID') AND (`fldDate` = '$seven_days_ago' OR `fldDate` = '$six_days_ago' OR `fldDate` = '$five_days_ago' OR `fldDate` = '$four_days_ago' OR `fldDate` = '$three_days_ago' OR `fldDate` = '$two_days_ago' OR `fldDate` = '$one_day_ago')";

				$result4 = mysqli_query($connect, $query4) or die(mysqli_error($connect));
		
				$num_rows = mysqli_num_rows($result4);
				
				$index = 0;
				$num_sessions = 0;
		
				if($num_rows == 0){
					echo "There were no results.";
				} else {
				
					// Set pointer back to beginning
					mysqli_data_seek($result4, 0);
				
					while ($row = $result4->fetch_assoc()) {
						$the_user = $row['pmkPatientID'];
						$session_compliance = $row['fldSessionCompliance'];
						
						// CHECK FOR UNIQUE DATES FOR EACH USER
						$user_date = $row['fldDate'];
						if($session_compliance==1){ // here is where i need to check for compliance as well
							
							// INCREASE NUMBER OF SESSIONS FOR USER
							$num_sessions = $num_sessions + 1;
						}
					}
				}
				
				// CHECK COMPLIANCE
				if($num_sessions < 5){
					// print user compliance
					echo ("User: ".$the_user.". ");
					echo ("Number of sessions: ".$num_sessions.". ");
					echo ("Not in compliance. ");
					
					// Connect to database and pull dates that match current date
					$connect = mysqli_connect($host_name, $user, $pass, $db_name) or die('Unable to connect.');

					// Check if connection was successful
					if (mysqli_connect_errno()) {
						printf("Connect failed: %s\n", mysqli_connect_error());
						exit();
					}

					$query5 = "SELECT `fldEmail` FROM `tblDoctor` WHERE `DocID` = '$doctor'";

					$result5 = mysqli_query($connect, $query5) or die(mysqli_error($connect));
					
					while ($row = $result5->fetch_assoc()) {
						$doc_email = $row['fldEmail'];
					}
					
					$msg = "The user: ".$the_user." is not in compliance. Please check the physician portal to view patient information.";
					$subject = "Patient Not In Compliance";
					
					mail($doc_email,$subject,$msg);
				}
				
				// Connect to database and pull dates that match current date
				$connect = mysqli_connect($host_name, $user, $pass, $db_name) or die('Unable to connect.');

				// Check if connection was successful
				if (mysqli_connect_errno()) {
					printf("Connect failed: %s\n", mysqli_connect_error());
					exit();
				}

				$query6 = "UPDATE `tblPatient` SET `fldComplianceChecked` = '$the_date' WHERE `pmkPatientID` = '$the_user'";

				$result6 = mysqli_query($connect, $query6) or die(mysqli_error($connect));
				
			}
		}
		
		mysql_close($connect);
		
		?>
	</body>
</html>