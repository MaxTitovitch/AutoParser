<?

function getSetting(){
	try {
		return explode("
", htmlentities(file_get_contents('./data/.env')));
	} catch(Exception $extension){
		return 99;
	}
}

$settings = getSetting();

$SERVER = "";
$PORT = "";
$USERNAME = "";
$PASSWORD = "";

foreach ($settings as $setting) {
	if(stripos($setting, "DB_HOST") === 0) {
		$SERVER = explode('=', $setting)[1];
	}
	if(stripos($setting, "DB_PORT") === 0) {
		$PORT = explode('=', $setting)[1];
	}
	if(stripos($setting, "DB_USERNAME") === 0) {
		$USERNAME = explode('=', $setting)[1];
	}
	if(stripos($setting, "DB_PASSWORD") === 0) {
		$PASSWORD = explode('=', $setting)[1];
	}
}

function getServer() {
	return $SERVER;
}

echo getServer();