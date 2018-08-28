<?php

namespace App\Tests\Classes;

use App\Classes\TwigPaginationExtension;
use PHPUnit\Framework\TestCase;

class TwigPaginationExtensionTest extends TestCase
{
    public function provider()
    {
        return [
            [1, 10, 5, 0],
            [1, 10, 10, 0],
            [1, 10, 12, 2],
            [1, 10, 20, 2],
            [1, 10, 22, 3],
            [1, 10, 30, 3],
        ];
    }

    /**
     * @dataProvider provider
     */
    public function testGetPages($page, $limit, $total, $pages)
    {
        $extension = new TwigPaginationExtension();

        $items = $extension->getPages($page, $total, $limit);

        self::assertEquals($pages, count($items));
    }
}