<?php 
/*** 
 * This lookups the current URL from the database and returns a redirect of some
 * form. The response is delivered to the client and connection closed. The 
 * final part of the code updates the access results in the database. 
 * 
 * Response and quick close from http://www.php.net/manual/en/features.connection-handling.php
 */

$server=$_SERVER["SERVER_NAME"];
$uri=$_SERVER["REDIRECT_URL"];

$redirection='http://google.com';

set_time_limit(0); 
ignore_user_abort(true);    
// buffer all upcoming output - make sure we care about compression: 
if(!ob_start("ob_gzhandler")) 
    ob_start();         
//echo $stringToOutput;    
echo date(DATE_RFC822);
// get the size of the output 
$size = ob_get_length();    
//TODO: other redirection types and codes
header('HTTP/1.1 302 Found');
header('Location: '.$redirection);

// send headers to tell the browser to close the connection
header('Content-Length: '.$size); 
header('Connection: close');    
// flush all output 
ob_end_flush(); 
ob_flush(); 
flush();    
// close current session 
if (session_id()) session_write_close(); 

sleep(50);
$message=date(DATE_RFC822);
echo $message;
?>