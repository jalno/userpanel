<?php
namespace packages\userpanel\views;
use \packages\userpanel\view;
class listview extends view{
	protected $dataList = array();
	protected $currentPage;
	protected $totalPages;
	protected $itemsPage;
	public function setDataList($data){
		$this->dataList = $data;
	}
	public function setPaginate($currentPage, $totalPages, $itemsPage){
		$this->currentPage = $currentPage;
		$this->totalPages = $totalPages;
		$this->itemsPage = $itemsPage;
	}
}
