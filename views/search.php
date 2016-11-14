<?php
namespace packages\userpanel\views;
use \packages\userpanel\views\listview;
use \packages\base\views\traits\form as formTrait;
class search extends listview{
	use formTrait;
	public function setResults($results){
		$this->setDataList($results);
	}
	public function getResults(){
		return $this->getDataList();
	}
	public function setTotalResults($count){
		$this->setData($count, 'totalResults');
	}
	public function getTotalResults(){
		return $this->totalItems;
	}
	public function export(){
		$export = parent::export();
		$export['data']['items'] = array();
		foreach($this->getDataList() as $item){
			$export['data']['items'][] = array(
				'title' => $item->getTitle(),
				'link' => $item->getLink(),
				'description' => $item->getDescription()
			);
		}
		return $export;
	}
}
