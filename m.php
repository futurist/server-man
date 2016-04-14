<?php
session_start();

# Check for session timeout, else initiliaze time
if (isset($_SESSION['timeout'])) {
		# Check Session Time for expiry
		#
		# Time is in seconds. 10 * 60 = 600s = 10 minutes
		if ($_SESSION['timeout'] + 1 * 6 < time()){
			  session_destroy();
        exit();
		}
}

// Function to get the client IP address
function get_client_ip() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
    $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
    $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
    $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
    $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
    $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return end(explode(':', $ipaddress));
}

$output = "";
$action = (isset($_REQUEST['action']) ? $_REQUEST['action'] : null);
if($action=='host') {
    if(md5($_REQUEST['pass'])=='3942ca519290c233a5a2890709947635'){
        $_SESSION['timeout'] = time();
	      $file = fopen('host.ip', 'w');
        fwrite($file, "". get_client_ip() );
	      fclose($file);
    }
	  header("Location: ".$_SERVER['SCRIPT_NAME']);
}
if($action=='start') {
	  // $output = shell_exec("service wdapache stop");
	  $file = fopen('wdapache.001', 'w');
	  fwrite($file, "start");
	  fclose($file);
	  header("Location: ".$_SERVER['SCRIPT_NAME']);
}

if($action=='stop'){
	  // $output = shell_exec("service wdapache stop");
	  unlink('wdapache.001');
	  header("Location: ".$_SERVER['SCRIPT_NAME']);
}

$start = file_exists( "wdapache.001" );

//$output = shell_exec("ps aux --sort -%cpu | head -10");
$output1 = shell_exec("/usr/bin/w");
$output = shell_exec("ps aux --sort=-%cpu,-rss ");

$sysload = sys_getloadavg();
if($sysload[0]>100 || $sysload[1]>100){
	  $file = fopen('load.py', 'a');
	  fwrite($file, str_repeat("*", 10) . "\n".$output1."\n".$output."\n" );
	  fclose($file);
}

?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="Keywords" content="">
        <meta name="Description" content="">
        <title>manager</title>
        <style>
	       input{ width:100px; hegiht:50px; border:1px solid #ccc; background:none; }
	       .ps {  }
        </style>
    </head>
    <body>

        <div align="left" style="">
            <form method="POST" action="<?php echo $_SERVER['SCRIPT_NAME'] ?>">
                <input type="text" disabled name="ip" value="<?php echo get_client_ip();?>"/>
                <input type="password" name="pass"/>
                <input type="hidden" name="action" value="host"/>
                <input type="submit" name="" value="充许"/>
            </form>

            <?php if(isset($_SESSION['timeout'])){ ?>
            <br><br>
            <?php if( ! $start) {?>
	              <input type="button" value="Start" onclick=" this.disabled='disabled'; window.location='?action=start' ">
            <?php }else{ ?>
	              <input type="button" value="Stop" onclick=" this.disabled='disabled'; window.location='?action=stop' ">
            <?php } ?>

            <br><br><br>

            <pre class="ps">
                <?php echo htmlspecialchars($output1); ?>
            </pre>

            <pre class="ps">
                <?php echo htmlspecialchars($output); ?>
            </pre>

            <?php } ?>

        </div>

    </body>
</html>
