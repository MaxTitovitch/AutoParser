<?

require __DIR__ . '/vendor/redbean/rb-mysql.php';
require __DIR__ . '/setting.php';

header('Content-Type: text/json; charset=utf-8');

if(!empty($_POST)){
	R::setup( 'mysql:host=' . $SERVER . ':' . $PORT . ';dbname=autoparser', $USERNAME, $PASSWORD ); 
	if (R::testConnection()) {
		$ids = explode('a', $_POST['ids']);
		unset($ids[count($ids)-1]);
		array_reverse($ids);
		$myId = 0;
		foreach ($ids as $id) {
			$category = R::load('transitions', (int)$id);
			$transitions[] = [
				'date' => $category->date,
				'time' => $category->time,
				'car' => $category->car, 
				'link' => $category->link, 
				'price' => $category->price, 
				'created' => $category->created
			];
			if($myId++ == 9) break;
		}

	    R::close();
	    echo json_encode($transitions);    
	}
}