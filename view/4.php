<?

ini_set('max_execution_time', 3600);

require __DIR__ . '/vendor/autoload.php';

use Sunra\PhpSimple\HtmlDomParser;
use Sunra\PhpSimpl;

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

    $document = HtmlDomParser::file_get_html($path);
    echo $document;
	// $offers = $document->find(".flip-entry");
	// echo count($offers);
}


$array = changeXML("C:\Users\Max\Downloads\Розетка\М.html");

// echo '<pre>';
// print_r($array);
// echo '</pre>';