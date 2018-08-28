<?php

namespace App\Classes;

class TwigPaginationExtension extends \Twig_Extension
{

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('get_pagination_range', array($this, 'getPages'))
        );
    }


    public function getPages($page, $total, $limit)
    {
        $pages = intval(ceil($total / $limit));
        if ($pages < 2) return [];

        $current = $page;
        $delta = 2;
        $left = $current - $delta;
        $right = $current + $delta + 1;
        $range = [];
        $rangeWithDots = [];
        $l = 0;

        for ($i = 1; $i <= $pages; $i++) {
            if ($i == 1 || $i == $pages || $i >= $left && $i < $right) {
                $range[] = $i;
            }
        }


        foreach ($range as $i) {
            if ($l) {
                if ($i - $l === 2) {
                    $rangeWithDots[] = $l + 1;
                } else if ($i - $l !== 1) {
                    $rangeWithDots[] = '...';
                }
            }

            $rangeWithDots[] = $i;

            $l = $i;
        }

        return $rangeWithDots;
    }
}