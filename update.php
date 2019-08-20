<?

ini_set('max_execution_time', 3600);

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/entities/Auto.php';
require __DIR__ . '/services/Parser.php';
require __DIR__ . '/vendor/redbean/rb-mysql.php';
require __DIR__ . '/setting.php';

use Parsing\Parser;
 
$parser = new Parser(getQuantity(), explode("\n", getLinks()));
$autos = $parser->getAutos(null, null, null);

R::setup( 'mysql:host=' . $SERVER . ':' . $PORT . ';dbname=autoparser', $USERNAME, $PASSWORD );  
if (R::testConnection()) {
	R::wipe('autos');
	foreach ($autos as $auto){
	   saveAuto($auto);
	}
	R::close();
}

function saveAuto($autoLast) {
	$auto = R::dispense('autos');
	 
	$auto->auto_photo = $autoLast->autoPhoto;
	$auto->publication_date = $autoLast->publicationDate;
	$auto->publication_time = $autoLast->publicationTime;
	$auto->car_make = $autoLast->carMake;
	$auto->car = $autoLast->car;

	$auto->price = $autoLast->price;
	$auto->auto_type = $autoLast->autoType;
	$auto->engine_capacity = $autoLast->engineCapacity;
	$auto->year_of_issue = $autoLast->yearOfIssue;
	$auto->mileage = $autoLast->mileage;

	$auto->color_hex = $autoLast->colorHex;
	$auto->user_type = $autoLast->userType;
	$auto->address = $autoLast->address;
	$auto->link = $autoLast->link;
	 
	R::store($auto); 
}

function getQuantity(){
	try {
		return htmlentities(file_get_contents("./data/quantity.txt"));
	} catch(Exception $extension){
		return 99;
	}
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