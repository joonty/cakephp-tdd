<?php
App::uses('TestTask','Console/Command/Task');
class TddTestTask extends TestTask {
	public $package = "none";

	public function bake($type, $className) {
		$this->Template->set('package',$this->package);
		parent::bake($type,$className);
	}

}
?>
