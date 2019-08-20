<?

header('Content-Type: text/html; charset=utf-8');
ini_set('max_execution_time', 3600);

require __DIR__ . '/vendor/autoload.php';

use Sunra\PhpSimple\HtmlDomParser;
use Sunra\PhpSimpl;
function parse($url, $lastUrl=false){
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if($lastUrl) curl_setopt($ch, CURLOPT_REFERER, $lastUrl);

    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}
function changeXML($path) {
	// $array = [];
	// $doc = new DOMDocument();
	// $doc->load($path);
	// $offers = $doc->getElementsByClassName('flip-entry');

	// foreach ($offers as $offer) {
	// 	$currentVendorCode = $offer->getElementsByTagName('vendorCode')->item(0)->nodeValue;
	// 	if(array_search($currentVendorCode, $articuls) !== false) {
	// 		$descr = getDescriptionValues(
	// 			explode("<br />", $offer->getElementsByTagName('description')->item(0)->nodeValue)
	// 		);
	// 		$categoryID = $offer->getElementsByTagName('categoryId')->item(0)->nodeValue;
	// 	}
	// }
	// return $array;

    $document = HtmlDomParser::str_get_html(parse($path));
    // echo $document;
	// $offers = $document->find(".flip-entry");
	// echo count($offers);
}


$array = changeXML("file:///C:/Users/Max/Downloads/Розетка/М.html");

// echo '<pre>';
// print_r($array);
// echo '</pre>';