<?php

namespace themes\clipone\Views;

use packages\base\DB\DBObject;
use packages\base\HTTP;

trait ListTrait
{
    private array $buttons = [];
    private bool $hasNextPage = false;
    private bool $hasPrevPage = false;
    private ?string $cursorName = null;
    private ?string $nextPageCursor = null;
    private ?string $prevPageCursor = null;

    public function setButton($name, $active, $params = [])
    {
        if (!isset($params['classes'])) {
            $params['classes'] = ['btn', 'btn-xs', 'btn-default'];
        }
        if (isset($params['title']) and $params['title']) {
            $params['classes'][] = 'tooltips';
        }
        if (!isset($params['link'])) {
            $params['link'] = '#';
        }
        $button = [
            'active' => $active,
            'params' => $params,
        ];
        $this->buttons[$name] = $button;
    }

    public function setButtonActive($name, $active)
    {
        if (isset($this->buttons[$name])) {
            $this->buttons[$name]['active'] = $active;

            return true;
        }

        return false;
    }

    public function setButtonParam($name, $parameter, $value)
    {
        if (isset($this->buttons[$name])) {
            $this->buttons[$name]['params'][$parameter] = $value;

            return true;
        }

        return false;
    }

    public function unsetButtonParam($name, $parameter)
    {
        if (isset($this->buttons[$name])) {
            unset($this->buttons[$name]['params'][$parameter]);

            return true;
        }

        return false;
    }

    public function getButtons(?array $names = []): array
    {
        $buttons = $this->buttons;
        if ($names) {
            foreach ($buttons as $name => $button) {
                if (!in_array($name, $names)) {
                    unset($buttons[$name]);
                }
            }
        }

        return $buttons;
    }

    public function hasButtons()
    {
        $have = false;
        foreach ($this->buttons as $btn) {
            if ($btn['active']) {
                $have = true;
                break;
            }
        }

        return $have;
    }

    public function genButtons(array $names = [], $responsive = true)
    {
        $buttons = [];
        foreach ($this->buttons as $name => $btn) {
            if ($btn['active'] and (!$names or in_array($name, $names))) {
                $buttons[$name] = $btn;
            }
        }
        $code = '';
        if ($buttons) {
            if ($responsive and count($buttons) > 1) {
                $code .= '<div class="visible-md visible-lg hidden-sm hidden-xs">';
            }
            foreach ($buttons as $btn) {
                $code .= '<a';
                if (isset($btn['params']['link']) and $btn['params']['link']) {
                    $code .= ' href="'.$btn['params']['link'].'"';
                }
                if (isset($btn['params']['classes']) and $btn['params']['classes']) {
                    if (is_array($btn['params']['classes'])) {
                        $btn['params']['classes'] = implode(' ', $btn['params']['classes']);
                    }
                    $code .= ' class="'.$btn['params']['classes'].'"';
                }
                if (isset($btn['params']['data']) and $btn['params']['data']) {
                    foreach ($btn['params']['data'] as $name => $value) {
                        $code .= ' data-'.$name.'=\''.$value."'";
                    }
                }
                if (isset($btn['params']['title']) and $btn['params']['title']) {
                    $code .= ' title="'.$btn['params']['title'].'"';
                }
                if (isset($btn['params']['target']) and $btn['params']['target']) {
                    $code .= ' target="'.$btn['params']['target'].'"';
                }
                $code .= '>';
                if (isset($btn['params']['icon']) and $btn['params']['icon']) {
                    $code .= '<i class="'.$btn['params']['icon'].'"></i>';
                }
                if (isset($btn['params']['text']) and $btn['params']['text']) {
                    $code .= $btn['params']['text'];
                }
                $code .= '</a> ';
            }
            if ($responsive and count($buttons) > 1) {
                $code .= '</div>';
                $code .= '<div class="visible-xs visible-sm hidden-md hidden-lg">';
                $code .= '<div class="btn-group">';
                $code .= '<a class="btn btn-primary dropdown-toggle btn-sm" data-toggle="dropdown" href="#"><i class="fa fa-cog"></i> <span class="caret"></span></a>';
                $code .= '<ul role="menu" class="dropdown-menu pull-right">';
                foreach ($buttons as $btn) {
                    $code .= '<li><a tabindex="-1"';
                    if (isset($btn['params']['link']) and $btn['params']['link']) {
                        $code .= ' href="'.$btn['params']['link'].'"';
                    }
                    if (isset($btn['params']['data']) and $btn['params']['data']) {
                        foreach ($btn['params']['data'] as $name => $value) {
                            $code .= ' data-'.$name.'="'.$value.'"';
                        }
                    }
                    $code .= '>';
                    if (isset($btn['params']['icon']) and $btn['params']['icon']) {
                        $code .= '<i class="'.$btn['params']['icon'].'"></i>';
                    }
                    if (isset($btn['params']['title']) and $btn['params']['title']) {
                        $code .= ' '.$btn['params']['title'];
                    }
                    $code .= '</a></li>';
                }
                $code .= '</ul></div></div>';
            }
        }

        return $code;
    }

    public function setCursorPaginate(int $itemsPage, string $cursorName, ?string $nextPageCursor = null, ?string $prevPageCursor = null)
    {
        $this->itemsPage = $itemsPage;

        $this->hasNextPage = null !== $nextPageCursor;
        $this->hasPrevPage = null !== $prevPageCursor;
        $this->cursorName = $cursorName;
        $this->nextPageCursor = $nextPageCursor;
        $this->prevPageCursor = $prevPageCursor;
    }

    public function paginator($selectbox = false, $mid_range = 7)
    {
        if ($this->cursorName) {
            if (!$this->hasNextPage and !$this->hasPrevPage) {
                echo '';
            }

            $getButtonUrl = fn (bool $hasPages, ?string $pageCursor = null) => $hasPages ? 'href="'.$this->pageurl($pageCursor).'"' : '';

            $paginateHtml = '<hr><ol class="pagination text-center pull-left">';
            $paginateHtml .= '<li class="prev'.(!$this->hasPrevPage ? ' disabled' : '').'"><a '.$getButtonUrl($this->hasPrevPage, $this->prevPageCursor).'>'.t('pagination.previousPage').'</a></li>';
            $paginateHtml .= '<li class="next'.(!$this->hasNextPage ? ' disabled' : '').'"><a '.$getButtonUrl($this->hasNextPage, $this->nextPageCursor).'>'.t('pagination.nextPage').'</a></li>';
            $paginateHtml .= '</ol>';

            echo $paginateHtml;

            return;
        }

        $return = '<hr><ol class="pagination text-center pull-left hidden-xs">';

        $prev_page = $this->currentPage - 1;
        $next_page = $this->currentPage + 1;

        if (1 != $this->currentPage and $this->totalItems >= 10) {
            $return .= '<li class="prev"><a href="'.$this->pageurl($prev_page).'">'.t('pagination.previousPage').'</a></li>';
        } else {
            $return .= '<li class="prev disabled"><a>'.t('pagination.previousPage').'</a></li>';
        }
        $start_range = $this->currentPage - floor($mid_range / 2);
        $end_range = $this->currentPage + floor($mid_range / 2);

        if ($start_range <= 0) {
            $end_range += abs($start_range) + 1;
            $start_range = 1;
        }

        if ($end_range > $this->totalPages) {
            $start_range -= $end_range - $this->totalPages;
            $end_range = $this->totalPages;
        }

        $range = range($start_range, $end_range);

        for ($i = 1; $i <= $this->totalPages; ++$i) {
            if ($range[0] > 2 and $i == $range[0]) {
                $return .= '<li><a> ... </a></li>';
            }
            // loop through all pages. if first, last, or in range, display
            if (1 == $i or $i == $this->totalPages or in_array($i, $range)) {
                if ($i == $this->currentPage) {
                    $return .= "<li class=\"active\"><a href=\"#\">{$i}</a></li>";
                } else {
                    $return .= '<li><a href="'.$this->pageurl($i)."\">{$i}</a></li>";
                }
            }
            if ($range[$mid_range - 1] < $this->totalPages - 1 and $i == $range[$mid_range - 1]) {
                $return .= '<li><a> ... </a></li>';
            }
        }
        if ($this->currentPage != $this->totalPages and $this->totalItems >= 10) {
            $return .= '<li class="next"><a href="'.$this->pageurl($next_page).'">'.t('pagination.nextPage').'</a></li>';
        } else {
            $return .= '<li class="next disabled"><a>'.t('pagination.nextPage').'</a></li>';
        }
        $return .= '</ol>';
        $return .= '<div class="visible-xs">';
        $return .= '<span class="paginate">'.t('pagination.page').': </span>';
        $return .= '<select class="paginate">';
        for ($i = 1; $i <= $this->totalPages; ++$i) {
            $return .= "<option value=\"{$i}\" data-url=\"".$this->pageurl($i).'"'.($i == $this->currentPage ? ' selected' : '').">{$i}</option>";
        }
        $return .= '</select></div>';
        echo $return;
    }

    public function export(...$args): array
    {
        if ($this->cursorName) {
            return [
                'data' => array_merge([
                    'items' => DBObject::objectToArray($this->getDataList()),
                ], $this->getCursorExportData()),
            ];
        } elseif (method_exists(parent::class, 'export')) {
            return parent::export(...$args);
        }

        return [];
    }

    public function getCursorExportData(): array
    {
        return [
            'items_per_page' => (int) $this->itemsPage,
            'cursor_name' => $this->cursorName,
            'next_page_cursor' => $this->nextPageCursor,
            'prev_page_cursor' => $this->prevPageCursor,
        ];
    }

    /**
     * @param int|string|null $page
     */
    private function pageurl($page = null, ?int $ipp = null)
    {
        if (null === $ipp) {
            $ipp = $this->itemsPage;
        }
        if (25 == $ipp) {
            $ipp = null;
        }
        $paginationData = HTTP::$request['get'];

        if ($this->cursorName) {
            if ($page) {
                $paginationData[$this->cursorName] = $page;
            } else {
                unset($paginationData[$this->cursorName]);
            }
        } elseif ($page and is_numeric($page)) {
            if (1 != $page) {
                $paginationData['page'] = $page;
            } else {
                unset($paginationData['page']);
            }
        }

        if ($ipp) {
            $paginationData['ipp'] = $ipp;
        } else {
            unset($paginationData['ipp']);
        }

        return $paginationData ? '?'.http_build_query($paginationData) : HTTP::$request['uri'];
    }
}
