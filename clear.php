<?

require __DIR__ . '/vendor/redbean/rb-mysql.php';
require __DIR__ . '/setting.php';

use Parsing\Parser;


R::setup( 'mysql:host=' . $SERVER . ':' . $PORT . ';dbname=autoparser', $USERNAME, $PASSWORD ); 
if (R::testConnection()) {
	R::wipe('autos');
	R::close();
}