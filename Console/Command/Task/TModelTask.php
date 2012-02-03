<?php
App::uses('ModelTask','Console/Command/Task');

/**
 * Task class for creating and updating model files.
 *
 * @package       Cake.Console.Command.Task
 */
class TModelTask extends ModelTask {
/**
 * tasks
 *
 * @var array
 */
	public $tasks = array('DbConfig', 'Fixture', 'Test', 'Tdd.Template');
}