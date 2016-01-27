<?php

/**
 * Class DrawController
 */
class DrawController {
	public $painters;

	/**
	 * DrawController constructor.
	 *
	 * The array must contain class name or objects with draw types
	 *  $painters = [
	 * 		['painter' => 'Circle', 'types' => 'circle'],
	 * 		['painter' => $square, 'types' => ['square', 'big_square']]
	 * 	];
	 *
	 * User 'default' type for draw all unknown types
	 *
	 * @param array|null $painters
	 *
	 */
	public function __construct(array $painters = null)
	{
		if($painters){
			foreach ($painters as $painterData) {
				if(!isset($painterData['painter']) || !isset($painterData['types'])){
					throw new Exception('Wrong painters array. Look for example in docs');
				}
				if(is_object($painterData['painter'])){
					$painter = $painterData['painter'];
				} else {
					$painter = new $painterData['painter'];
				}
				$this->addPainter($painter, $painterData['types']);
			}
		}
	}

	/**
	 * Set new draw handler for some type
	 *
	 * @param Drawable $painter
	 * @param $types
	 */
	public function addPainter(Drawable $painter, $types){
		if(!is_array($types)){
			$types = [$types];
		}
		foreach ($types as $type) {
			$this->painters[$type] = $painter;
		}
	}

	/**
	 * Returns handler for some type
	 *
	 * @param $type
	 * @return Drawable
	 */
	public function getPainter($type){
		return isset($this->painters[$type]) ? $this->painters[$type] : false;
	}

	/**
	 * Draw array of shapes
	 *
	 * @param array $shapes
	 */
	public function drawShapes(array $shapes)
	{
		foreach ($shapes as $shape) {
			$this->drawShape($shape);
		}
	}

	/**
	 *	Draw a single shape
	 *
	 * @param array $shape
	 * @return mixed
	 */
	public function drawShape(array $shape)
	{
		$type = array_key_exists($shape['type'], $this->painters) ? $shape['type'] : 'default';
		if(isset($this->painters[$type])){
			$this->painters[$type]->draw($shape);
		}
	}
	
}

interface Drawable {
	public function draw($shape);
}

class Circle implements Drawable {
	public function draw($shape){
		echo 'circle' . PHP_EOL;
	}
}
class Square implements Drawable {
	function draw($shape){
		echo 'square' . PHP_EOL;
	}
}

// Example

//User data
$shapes = [
		['type' => 'circle', 'params' => []],
		['type' => 'circle', 'params' => []],
		['type' => 'square', 'params' => []],
];

// New controller with some config
$controller = new DrawController([['painter' => 'Circle', 'types' => ['circle', 'big_circle', 'dot_circle']]]);
// Additional drawler for square
$squareDrawler = new Square();

// Setting drawler for some types
$controller->addPainter($squareDrawler, 'square');
$controller->addPainter($squareDrawler, ['big_square', 'dot_square']);

//Draw user data
$controller->drawShapes($shapes);
