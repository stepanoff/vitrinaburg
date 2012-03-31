<?php
class VitrinaSectionsTreeWidget extends CWidget {

    public $selectedSection = null; // selected node
    public $modelName = 'VitrinaSection'; // class name that contains tree nodes
    public $route = array('/vitrinaCollection/section/');
    public $routeIndex = 'sectionId';
    public $showNotEmpty = false; // show only not empty sections (according to counters)
    public $counters = array(); // counters in

    protected $structure = null;
    protected $model = null;

	public function init() {
		parent::init();

        $class = $this->modelName;
        $this->model = new $class;
        $this->structure = $this->model->getStructure(2);
	}
	
    public function run() {
		parent::run();

        if ($this->selectedSection)
        {
            $selectedSections = $this->model->getParents($this->selectedSection);
            $selectedSections[] = $this->selectedSection;
        }

		$this->render('sectionsTree', array(
			'structure' => $this->structure,
            'selected' => $selectedSections,
            'selectedSection' => $this->selectedSection,
            'counters' => $this->counters,
            'route' => $this->route,
            'routeIndex' => $this->routeIndex,
            'showNotEmpty' => $this->showNotEmpty,
		));
    }

}
