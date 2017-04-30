<?php


$deviceToken = '0bb4c01dae3e96cb21e0033832751a47871f28e113e3a2be6cf3ab7bdb897fec';                        

// Passphrase for the private key (ck.pem file)
// $pass = ;
// Get the parameters from http get or from command line

$message = "Hello";
$badge = '1';
$sound = 'default';

// Construct the notification payload
$body = array();
$body['aps'] = array('alert' => $message);
if ($badge)
    $body['aps']['badge'] = $badge;
if ($sound)
    $body['aps']['sound'] = $sound;
/* End of Configurable Items */

$ctx = stream_context_create();
stream_context_set_option($ctx, 'ssl', 'local_cert', 'certificate.pem');

// assume the private key passphase was removed.
// stream_context_set_option($ctx, 'ssl', 'passphrase', $pass);
$fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);

if (!$fp) {
    print "Failed to connect $err $errstr\n";
    return;
} else {
    print "Connection OK


";
}

$payload = json_encode($body);

// request one 
$msg = chr(0) . pack("n",32) . pack('H*',$deviceToken) . pack("n",strlen($payload)) . $payload;
print "sending message :" . $payload . "\n";

fwrite($fp, $msg);

fclose($fp);

?>
