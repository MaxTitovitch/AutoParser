<?

function writeInFile($file, $data) {
	$file = fopen($file, 'w') or die;
	fwrite($file, $data);
	fclose($file);
}
$break = 
writeInFile('./data/quantity.txt', $_POST['quantity']);
writeInFile('./data/links.txt', $_POST['links']);

print_r($_POST); 