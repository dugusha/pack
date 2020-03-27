<?php
/**
 * 分页格式标准化
 * Created by PhpStorm.
 * User: gaoruishen
 * Date: 2020/3/27
 * Time: 11:21 AM
 */

namespace GRS\Yii2;
use yii\base\Component;

class PageCtl extends Component{
    protected $page = 1; //页码
    protected $pageSize = 20; //单页的数据数量
    protected $defaultPage = 1; //默认页码，如果$page存储格式不合法返回默认页码
    protected $defaultPageSize = 20; //默认单页数据量，如果$pageSize数据不合法返回默认单页数据量
    public $pageKey = 'page'; //标识[页码]的字段
    public $pageSizeKey = 'page_size'; //标识[一页数据数量]的字段
    public $totalCountKey = 'total'; //标识[数据总数]的字段
    public $totalPageKey = 'total_page'; //标识[总页数]的字段
    public $dataKey = 'list'; //标识[数据]的字段


    public function getPage() {
        if(is_numeric($this->page) && $this->page >0 ) {
            return $this->page;
        }
        return $this->defaultPage;
    }

    public function setPage($page) {
        $this->page = $page;
        return $this;
    }

    public function getPageSize() {
        if(is_numeric($this->pageSize) && $this->pageSize >0) {
            return $this->pageSize;
        }
        return $this->defaultPageSize;
    }

    public  function setPageSize($pageSize) {
        $this->pageSize = $pageSize;
    }

    public function formatResult($count,$data) {
        return [
            $this->dataKey => $data,
            $this->pageKey => $this->getPage(),
            $this->pageSizeKey => $this->getPageSize(),
            $this->totalCountKey => (int)$count,
            $this->totalPageKey => floor (($count + $this->getPageSize() -1)/$this->getPageSize())
        ];
    }
}