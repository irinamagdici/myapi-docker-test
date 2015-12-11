Cache management <br/>
<?php
$old_path = getcwd();
//echo 'current path: '.$old_path;
$env = isset($_GET['env'])?$_GET['env']:"api";
$env_var_path = "";
switch($env) {
	case 'api':
		$env_var_path = "API_HOME";
		break;
	default:
		$env_var_path = "API_HOME";
}

#chdir(getenv($env_var_path));
chdir('/var/www/html/');
echo '<br/>Env Var: '.$env_var_path.' env: '.$env;
echo '<br/>Env val: '.getenv("LIVE_HOME");
echo '<br/>Current user: '.get_current_user();
echo '<br/>Moved to: '.getcwd().'<br/>';
echo '<br/> all env vars:  '.var_dump($_ENV);
$output = shell_exec('bash delete-cache-script.sh '.getenv($env_var_path));
chdir($old_path);
echo "Cache Deleted From: ". $output;
?>
