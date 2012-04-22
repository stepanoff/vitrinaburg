<?php
class VitrinaSectionsTreeWidget extends CWidget {

    public $selectedSections = null; // selected nodes
    public $selectedSection = null; // selected node
    public $modelName = 'VitrinaSection'; // class name that contains tree nodes
    public $route = array('/vitrinaCollection/section/');
    public $routeIndex = 'sectionId';
    public $showNotEmpty = false; // show only not empty sections (according to counters)
    public $counters = array(); // counters in
    public $structure = null; // node structure with selected nodes

    protected $model = null;

	public function init() {
		parent::init();

	}
	
    public function run() {
		parent::run();

        $selectedSections = array();
        
        if ($this->structure === null)
        {
            $class = $this->modelName;
            $this->model = new $class;
            $this->structure = $this->model->getStructure(2);

            $selectedSections = array();
            if ($this->selectedSection || $this->selectedSections)
            {
                $ids = $this->selectedSections ? $this->selectedSections : array($this->selectedSection);
                foreach ($ids as $id)
                {
                    $tmp = $this->model->getParents($id);
                    if (count($tmp) > count($selectedSections))
                    {
                        $selectedSections = $tmp;
                        $selectedSections[] = $id;
                        $this->selectedSection = $id;
                    }
                }
            }
            $selectedSections = array_unique($selectedSections);
        }
        else
            $selectedSections = is_array($this->selectedSections) ? $this->selectedSections : array();

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
