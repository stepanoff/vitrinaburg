<?php
class VAdminLinkPager extends CLinkPager
{
    public $route = '';
    
    public function run()
    {
        $pages = $this->getPages();
        $pages->route = $this->route;
        $this->setPages($pages);
        parent::run();
    }

}