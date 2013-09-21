<?php

/**
 * StripeBundle for Symfony2
 *
 * This Bundle is part of Symfony2 Payment Suite
 *
 * @author Denys Pasishnyi <dpcat237@gmail.com>
 * @package StripeBundle
 *
 * Denys Pasishnyi 2013
 */

namespace dpcat237\StripeBundle\Tests\Services;

use dpcat237\StripeBundle\Services\StripeManager;

/**
 * Stripe manager
 */
class StripeManagerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var string
     * 
     * Currency
     */
    const CURRENCY = 'USD';

    /**
     * @var string
     * 
     * Currency
     */
    const API_TOKEN = '2374932748923';


    /**
     * @var integer
     * 
     * Cart amount
     */
    const CART_AMOUNT = 50;

    /**
     * @var integer
     *
     * Cart number
     */
    const CART_NUMBER = 4242424242424242;


    /**
     * @var integer
     *
     * Cart expire month
     */
    const CART_EXPIRE_MONTH = 12;


    /**
     * @var integer
     *
     * Cart expire year
     */
    const CART_EXPIRE_YEAR = 2017;


    /**
     * @var string
     * 
     * Cart description
     */
    const CART_DESCRIPTION = 'This is my cart description';


    /**
     * @var PaymentManager
     * 
     * Payment manager object
     */
    private $stripeManager;


    /**
     * @var PaymentEventDispatcher
     * 
     * Paymetn event dispatcher object
     */
    private $paymentEventDispatcher;


    /**
     * @var StripeTransactionWrapper
     * 
     * Wrapper for Paypall Transaction instance
     */
    private $stripeTransactionWrapper;


    /**
     * @var PaymentBridgeInterface
     * 
     * Payment bridge object
     */
    private $paymentBridgeInterface;


    /**
     * @var StripeMethod class
     * 
     * Stripe Method object
     */
    private $stripeMethod;


    /**
     * Setup method
     */
    public function setUp()
    {

        $this->paymentBridgeInterface = $this
            ->getMockBuilder('Mmoreram\PaymentCoreBundle\Services\Interfaces\PaymentBridgeInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->stripeTransactionWrapper = $this
            ->getMockBuilder('dpcat237\StripeBundle\Services\Wrapper\StripeTransactionWrapper')
            ->disableOriginalConstructor()
            ->getMock();

        $this->paymentEventDispatcher = $this
            ->getMockBuilder('Mmoreram\PaymentCoreBundle\Services\PaymentEventDispatcher')
            ->disableOriginalConstructor()
            ->getMock();

        $this->stripeMethod = $this
            ->getMockBuilder('dpcat237\StripeBundle\StripeMethod')
            ->disableOriginalConstructor()
            ->getMock();

        $this->stripeManager = new StripeManager($this->paymentEventDispatcher, $this->stripeTransactionWrapper, $this->paymentBridgeInterface);
    }


    /**
     * Testing payment error
     *
     * @expectedException \Mmoreram\PaymentCoreBundle\Exception\PaymentException
     */
    public function testPaymentError()
    {
        $this
            ->stripeMethod
            ->expects($this->once())
            ->method('getAmount')
            ->will($this->returnValue(self::CART_AMOUNT * 100));

        $this
            ->stripeMethod
            ->expects($this->once())
            ->method('getApiToken')
            ->will($this->returnValue(self::API_TOKEN));

        $this
            ->stripeMethod
            ->expects($this->any())
            ->method('setTransactionId');

        $this
            ->stripeMethod
            ->expects($this->any())
            ->method('setTransactionStatus');

        $this
            ->paymentBridgeInterface
            ->expects($this->once())
            ->method('getCurrency')
            ->will($this->returnValue(self::CURRENCY));

        $this
            ->paymentBridgeInterface
            ->expects($this->once())
            ->method('getAmount')
            ->will($this->returnValue(self::CART_AMOUNT));

        $cart = array(
            'number' => self::CART_NUMBER,
            'exp_month' => self::CART_EXPIRE_MONTH,
            'exp_year' => self::CART_EXPIRE_YEAR,
        );

        $this
            ->stripeTransactionWrapper
            ->expects($this->once())
            ->method('create')
            ->with($this->equalTo(array(
                'card' => $cart,
                'amount' => self::CART_AMOUNT * 100,
                'currency' => strtolower(self::CURRENCY),
            )))
            ->will($this->returnValue(array(
                'paid'    =>  '0',
                'id'        =>  '123'
            )));

        $this
            ->paymentEventDispatcher
            ->expects($this->once())
            ->method('notifyPaymentOrderLoad')
            ->with($this->equalTo($this->paymentBridgeInterface), $this->equalTo($this->stripeMethod));

        $this
            ->paymentEventDispatcher
            ->expects($this->once())
            ->method('notifyPaymentOrderDone')
            ->with($this->equalTo($this->paymentBridgeInterface), $this->equalTo($this->stripeMethod));

        $this
            ->paymentEventDispatcher
            ->expects($this->any())
            ->method('notifyPaymentOrderSuccess')
            ->with($this->equalTo($this->paymentBridgeInterface), $this->equalTo($this->stripeMethod));

        $this
            ->paymentEventDispatcher
            ->expects($this->once())
            ->method('notifyPaymentOrderFail')
            ->with($this->equalTo($this->paymentBridgeInterface), $this->equalTo($this->stripeMethod));

        $this->stripeManager->processPayment($this->stripeMethod, self::CART_AMOUNT);
    }


    /**
     * Testing payment error
     * 
     */
    public function testPaymentSuccess()
    {
        $this
            ->stripeMethod
            ->expects($this->once())
            ->method('getAmount')
            ->will($this->returnValue(self::CART_AMOUNT * 100));

        $this
            ->stripeMethod
            ->expects($this->once())
            ->method('getApiToken')
            ->will($this->returnValue(self::API_TOKEN));

        $this
            ->stripeMethod
            ->expects($this->once())
            ->method('setTransactionId')
            ->with($this->equalTo('123'))
            ->will($this->returnValue($this->stripeMethod));

        $this
            ->stripeMethod
            ->expects($this->once())
            ->method('setTransactionStatus')
            ->with($this->equalTo('closed'))
            ->will($this->returnValue($this->stripeMethod));

        $this
            ->paymentBridgeInterface
            ->expects($this->once())
            ->method('getCurrency')
            ->will($this->returnValue(self::CURRENCY));

        $this
            ->paymentBridgeInterface
            ->expects($this->once())
            ->method('getAmount')
            ->will($this->returnValue(self::CART_AMOUNT));

        $this
            ->paymentBridgeInterface
            ->expects($this->once())
            ->method('getCartDescription')
            ->will($this->returnValue(self::CART_DESCRIPTION));

        $cart = array(
            'number' => self::CART_NUMBER,
            'exp_month' => self::CART_EXPIRE_MONTH,
            'exp_year' => self::CART_EXPIRE_YEAR,
        );

        $this
            ->stripeTransactionWrapper
            ->expects($this->once())
            ->method('create')
            ->with($this->equalTo(array(
                'card' => $cart,
                'amount' => self::CART_AMOUNT * 100,
                'currency' => strtolower(self::CURRENCY),
            )))
            ->will($this->returnValue(array(
                'paid'    =>  '1',
                'id'        =>  '123'
            )));

        $this
            ->paymentEventDispatcher
            ->expects($this->once())
            ->method('notifyPaymentOrderLoad')
            ->with($this->equalTo($this->paymentBridgeInterface), $this->equalTo($this->stripeMethod));

        $this
            ->paymentEventDispatcher
            ->expects($this->once())
            ->method('notifyPaymentOrderDone')
            ->with($this->equalTo($this->paymentBridgeInterface), $this->equalTo($this->stripeMethod));

        $this
            ->paymentEventDispatcher
            ->expects($this->any())
            ->method('notifyPaymentOrderSuccess')
            ->with($this->equalTo($this->paymentBridgeInterface), $this->equalTo($this->stripeMethod));

        $this
            ->paymentEventDispatcher
            ->expects($this->once())
            ->method('notifyPaymentOrderFail')
            ->with($this->equalTo($this->paymentBridgeInterface), $this->equalTo($this->stripeMethod));

        $this->stripeManager->processPayment($this->stripeMethod, self::CART_AMOUNT);
    }
}