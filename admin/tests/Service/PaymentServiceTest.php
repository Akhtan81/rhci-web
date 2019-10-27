<?php

namespace App\Tests\Service;

use App\Service\PaymentService;
use App\Tests\Classes\WebTestCase;

/**
 * @covers \App\Service\PaymentService
 */
class PaymentServiceTest extends WebTestCase
{
    /** @var PaymentService */
    private $service;

    protected function setUp()
    {
        parent::setUp();
        $this->service = new PaymentService();
    }

    public function provider()
    {
        return [
            [100, 62],
            [1000, 891],
            [10000, 9180],
        ];
    }

    /**
     * @dataProvider provider
     *
     * @small
     *
     * @param $sum
     * @param $expected
     */
    public function test_getPartnerAmount($sum, $expected)
    {
        $result = $this->service->getPartnerAmount($sum);

        $this->assertEquals($expected, $result);
    }
}
