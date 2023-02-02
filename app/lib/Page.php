<?php

namespace app\lib;

use think\Paginator;

class Page extends Paginator
{

    /**
     * 上一页按钮
     *
     * @param string $text
     *
     * @return string
     */
    protected function getPreviousButton(string $text = "<span class='d-inline-block animate-right hover-bg-danger-left'></span>"): string
    {

        if ($this->currentPage() <= 1) {
            return $this->getDisabledTextWrapper($text);
        }

        $url = $this->url(
            $this->currentPage() - 1
        );

        return $this->getAvailablePreviousNextPageWrapper($url, $text);
    }

    /**
     * 下一页按钮
     *
     * @param string $text
     *
     * @return string
     */
    protected function getNextButton(string $text = '<i class="fal fa-long-arrow-right fs-5"></i>'): string
    {
        if (!$this->hasMore) {
            return $this->getDisabledTextWrapper($text);
        }

        $url = $this->url($this->currentPage() + 1);

        return $this->getAvailablePreviousNextPageWrapper($url, $text);
    }

    protected function getAvailablePreviousNextPageWrapper(string $url, string $page): string
    {
        $getParam = '';
        $get = app()->request->get();
        unset($get['p']);
        if (!empty($get)) {
            $getParam .= '&' . http_build_query($get);
        }

        return '<a class="d-inline-block px-1 mx-2 pb-2 hover-page-item pb-2 " href="' . htmlentities($url) . $getParam . '">' . $page . '</a>';
    }


    /**
     * 页码按钮
     *
     * @return string
     */
    protected function getLinks(): string
    {
        if ($this->simple) {
            return '';
        }

        $block = [
            'first' => null,
            'slider' => null,
            'last' => null,
        ];

        $side = 3;
        $window = $side * 2;

        if ($this->lastPage < $window + 6) {
            $block['first'] = $this->getUrlRange(1, $this->lastPage);
        } elseif ($this->currentPage <= $window) {
            $block['first'] = $this->getUrlRange(1, $window + 2);
            $block['last'] = $this->getUrlRange($this->lastPage - 1, $this->lastPage);
        } elseif ($this->currentPage > ($this->lastPage - $window)) {
            $block['first'] = $this->getUrlRange(1, 2);
            $block['last'] = $this->getUrlRange($this->lastPage - ($window + 2), $this->lastPage);
        } else {
            $block['first'] = $this->getUrlRange(1, 2);
            $block['slider'] = $this->getUrlRange($this->currentPage - $side, $this->currentPage + $side);
            $block['last'] = $this->getUrlRange($this->lastPage - 1, $this->lastPage);
        }

        $html = '';

        if (is_array($block['first'])) {
            $html .= $this->getUrlLinks($block['first']);
        }

        if (is_array($block['slider'])) {
            $html .= $this->getDots();
            $html .= $this->getUrlLinks($block['slider']);
        }

        if (is_array($block['last'])) {
            $html .= $this->getDots();
            $html .= $this->getUrlLinks($block['last']);
        }

        return $html;
    }

    /**
     * 渲染分页html
     *
     * @return mixed
     */
    public function render()
    {
        if ($this->hasPages()) {
            if ($this->simple) {
                return sprintf(
                    '<div class="d-inline-block">%s %s </div>',
                    $this->getPreviousButton(),
                    $this->getNextButton()
                );
            } else {
                return sprintf(
                    '<div class="d-inline-block">%s %s %s</div>',
                    $this->getPreviousButton(),
                    $this->getLinks(),
                    $this->getNextButton()
                );
            }
        }
    }

    /**
     * 生成一个可点击的按钮
     *
     * @param string $url
     * @param string $page
     *
     * @return string
     */
    protected function getAvailablePageWrapper(string $url, string $page): string
    {
        $getParam = '';
        $get = app()->request->get();
        unset($get['p']);
        if (!empty($get)) {
            $getParam .= '&' . http_build_query($get);
        }

        return '<a class="d-inline-block fs-6 text-gray-6 mx-3 py-2 page-item " href="' . htmlentities($url) . $getParam . '">' . $page . '</a>';
    }

    /**
     * 生成一个禁用的按钮
     *
     * @param string $text
     *
     * @return string
     */
    protected function getDisabledTextWrapper(string $text): string
    {
        return '<a class="d-inline-block fs-6 text-gray-6 mx-3 py-2 text-danger" href="JavaScript:;">' . $text . '</a>';
    }

    /**
     * 生成一个激活的按钮
     *
     * @param string $text
     *
     * @return string
     */
    protected function getActivePageWrapper(string $text): string
    {
        return '<a class="d-inline-block px-1 mx-2 pb-2 hover-page-item pb-2 active" href="JavaScript:;">' . $text . '</a>';
    }

    /**
     * 生成省略号按钮
     *
     * @return string
     */
    protected function getDots(): string
    {
        return $this->getDisabledTextWrapper('...');
    }

    /**
     * 批量生成页码按钮.
     *
     * @param array $urls
     *
     * @return string
     */
    protected function getUrlLinks(array $urls): string
    {
        $html = '';

        foreach ($urls as $page => $url) {
            $html .= $this->getPageLinkWrapper($url, $page);
        }

        return $html;
    }

    /**
     * 生成普通页码按钮
     *
     * @param string $url
     * @param string $page
     *
     * @return string
     */
    protected function getPageLinkWrapper(string $url, string $page): string
    {
        if ($this->currentPage() == $page) {
            return $this->getActivePageWrapper($page);
        }

        return $this->getAvailablePageWrapper($url, $page);
    }
}