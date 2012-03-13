<?php
App::uses('FixtureTask','Console/Command/Task');
class TddFixtureTask extends FixtureTask {
	public $package = "none";

	public function bake($model, $useTable = false, $importOptions = array()) {
		$this->Template->set('package',$this->package);
		parent::bake($model, $useTable, $importOptions);
	}

}
?>