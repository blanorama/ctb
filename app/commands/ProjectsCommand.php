<?php

class ProjectsCommand extends BaseCommand {

	protected $name = 'p:list';
	protected $description = 'List favorite projects.';

	public function fire()
	{
		$phprojekt = new Phprojekt();

		$this->doLogin($phprojekt);
		$this->listProjects($phprojekt);
	}

	/**
	 * @param Phprojekt $phprojekt
	 */
	private function listProjects($phprojekt)
	{
		$phprojekt->listProjects($this);
	}

}
