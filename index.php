<?

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/entities/Auto.php';
require __DIR__ . '/services/Parser.php';
require __DIR__ . '/vendor/redbean/rb-mysql.php';
require __DIR__ . '/setting.php';

use Parsing\Parser;
use entity\Auto;
 
header('Content-Type: text/html; charset=utf-8');
 $SSS = '1;';
// $parser = new Parser(getQuantity());
// $autos = $parser->getAutos(null, null, null);
$autos = null;
if(empty($_GET)) {
	$autos = getAutos();
} else {
	$autos = getFilteredAutos($_GET);
}

Mustache_Autoloader::register();
$mustache = new Mustache_Engine;
$loader = new Mustache_Loader_FilesystemLoader(__DIR__. '/view', ['extension' => '.html']);

$links = getLinks();
$params = [
	'autos' => $autos,
	'quantity' => getQuantity(),
	'links' => $links
];
echo $mustache->render($loader->load('index'), $params);

function getQuantity(){
	try {
		return htmlentities(file_get_contents("./data/quantity.txt"));
	} catch(Exception $extension){
		return 99;
	}
}

function getFilteredAutos($getData) {
	$whereString = ' WHERE';	
	$whereString = addFrom($whereString, $getData);
	$whereString = addRegion($whereString, $getData);
	$whereString = addMark($whereString, $getData);
	$whereString = addYear($whereString, $getData);
	$whereString = addMilage($whereString, $getData);
	$whereString = addKpp($whereString, $getData);
	$whereString = $whereString == ' WHERE' ? '' : mb_substr($whereString, 0, mb_strlen($whereString)-4);
	$autos = checkPrice(getAutos($whereString ), $getData['price']);
	return $autos;
}

function checkPrice($autos, $getField) {
	if($getField != null){
		$newArray = [];
		$start = (int)(explode('_', $getField)[0]);
		$end = (int)(explode('_', $getField)[1]);
		$end = $end != 0 ? $end : 100000000;
		for ($i=0; $i < count($autos); $i++) { 
			$price = str_replace([' ', 'грн'], '', $autos[$i]->price);
			if(mb_stristr($price, '$')) {
				$price = changePrice($price);
			}
			if($price >= $start AND $price <= $end){
				$newArray[] = $autos[$i];
			}
		}
		return $newArray;
	}
	return $autos;
}

function changePrice($price) {
	return (int)(mb_substr($price, 0, mb_strlen($price)-1)) * 25;
}

function getLike($whereString, $getField, $fieldName) {
	if($getField != null){
		$elements = explode('_', $getField);
		$whereString .= ' (';
		for ($i=0; $i < count($elements); $i++) { 
		 	$whereString .= ' ' . $fieldName . ' LIKE("%' . $elements[$i] . '%")';
		 	if($i < count($elements) - 1) $whereString .= ' OR';
		} 
		$whereString .= ') AND';
	}
	return $whereString;
}

function getBetween($whereString, $getField, $fieldName) {
	if($getField != null){
		$start = explode('_', $getField)[0];
		$end = explode('_', $getField)[1];
		$end = $end != '0' ? $end : '10000000';
		$whereString .= ' (' . $fieldName . ' BETWEEN ' . $start . ' AND ' . $end . ') AND';
	}
	return $whereString;
}

function addFrom($whereString, $getData) {
	return getLike($whereString, $getData['from'], '`link`');
}

function addRegion($whereString, $getData) {
	return getLike($whereString, $getData['region'], '`address`');
}

function addMark($whereString, $getData) {
	return getLike($whereString, $getData['mark'], '`car_make`');
}

function addYear($whereString, $getData) {
	return getBetween($whereString, $getData['year'], '`year_of_issue`');
}

function addMilage($whereString, $getData) {
	return getBetween($whereString, $getData['mileage'], '`mileage`');
}

function addKpp($whereString, $getData) {
	return getLike($whereString, $getData['kpp'], '`engine_capacity`');
}

function getAutos($whereString = ''){

	$objectAutos = [];
	global $SERVER, $PORT, $USERNAME, $PASSWORD;
	R::setup( 'mysql:host=' . $SERVER . ':' . $PORT . ';dbname=autoparser', $USERNAME, $PASSWORD ); 
	if (R::testConnection()) {
		$autos = R::findAll('autos', $whereString . ' ORDER BY DATE_FORMAT(publication_date, \'%d.%m.%Y\') DESC, publication_time DESC');
		foreach ($autos as $auto){
		   $objectAutos[] = saveAuto($auto);
		}
		R::close();
	}
	return $objectAutos;
}

function saveAuto($auto) {
	return new Auto(
		$auto->auto_photo,
		$auto->publication_date,
		$auto->publication_time,
		$auto->car_make,
		$auto->car,
		$auto->price,
		$auto->auto_type,
		$auto->engine_capacity,
		$auto->year_of_issue,
		$auto->mileage,
		$auto->color_hex,
		$auto->user_type,
		$auto->address,
		$auto->link
	);	 
}


function getLinks(){
	try {
		return htmlentities(file_get_contents("./data/links.txt"));
	} catch(Exception $extension){
		return 'https://www.olx.ua/transport/legkovye-avtomobili/' . "\n" .
	        "https://auto.ria.com/search/?categories.main.id=1&price.currency=1&sort[0].order=dates.created.desc&abroad.not=0&custom.not=1&size=20&page=" . "\n" .
	        "http://rst.ua/oldcars/?task=newresults&make%5B%5D=0&year%5B%5D=0&year%5B%5D=0&price%5B%5D=0&price%5B%5D=0&engine%5B%5D=0&engine%5B%5D=0&gear=0&fuel=0&drive=0&condition=0&from=sform&body%5B%5D=10&body%5B%5D=6&body%5B%5D=1&body%5B%5D=3&body%5B%5D=2&body%5B%5D=5&body%5B%5D=11&body%5B%5D=4&body%5B%5D=27&start=";

	}
}