<?php
	//open the file,
	//extract an integer
	//increment the integer
	//write the integer back into the file
	//return a json encoded response;

	//File locking demo courtesy of ircmaxell, stack overflow.
	$file = fopen("count.txt", 'r+');
	flock($file, LOCK_EX);
	$hit_count = fread($file, 4048);
	$hit_count++;
	fseek($file, 0);
	fwrite($file, $hit_count);
	flock($file, LOCK_UN);
	fclose($file);
	
	echo json_encode(array('id' => $hit_count, 'content'=>'Hello World!'));
  
  
?>