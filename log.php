<?

require __DIR__ . '/vendor/redbean/rb-mysql.php';
require __DIR__ . '/setting.php';

header('Content-Type: text/json; charset=utf-8');

if(!empty($_POST)){
	R::setup( 'mysql:host=' . $SERVER . ':' . $PORT . ';dbname=autoparser', $USERNAME, $PASSWORD ); 
	if (R::testConnection()) {

		$transition = R::dispense('transitions');
		 
		$transition->date = $_POST['date'];
		$transition->time = $_POST['time'];
		$transition->car = $_POST['car'];
		$transition->link = $_POST['link'];
		$transition->price = $_POST['price'];
		 
		R::store($transition); 
		$id = $transition->id;

	    R::close();
	    echo json_encode($id);    
	}
}