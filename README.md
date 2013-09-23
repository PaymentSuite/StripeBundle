Stripe Platform for Symfony Payment Suite
-----

[![Payment Suite](http://mmoreram.github.io/PaymentCoreBundle/public/images/payment-suite.png)](https://github.com/mmoreram/PaymentCoreBundle)  [![Payment Suite](http://mmoreram.github.io/PaymentCoreBundle/public/images/still-maintained.png)]()  [![Build Status](https://travis-ci.org/mmoreram/PaymillBundle.png?branch=master)](https://travis-ci.org/dpcat237/StripeBundle) [![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/dpcat237/StripeBundle/badges/quality-score.png?s=10dab38a47f5ca4c11a2de2e4f1237555c5e8660)](https://scrutinizer-ci.com/g/dpcat237/StripeBundle/)

> This Bundle is under development but already functional and partially tested.
> Any comment, suggestion or contribution will be very appreciated.
>
> [@dpcat237](https://github.com/dpcat237)



Table of contents
-----

1.  [About Stripe Bundle](#about-stripe-bundle)
2.  [Installing Payment Environment](#installing-payment-environment)
3.  [Installing Stripe Bundle](#installing-stripe-bundle)
4.  [Configuration](#configuration)
5.  [Router](#router)
6.  [Display](#display)
7.  [Customize](#customize)
8.  [Testing and more documentation](#testing-and-more-documentation)
9.  [Contribute](http://github.com/mmoreram/PaymentCoreBundle/blob/master/Resources/docs/contribute.md)

About Stripe Bundle
=====

This bundle bring you a possibility to make simple payments through [Stripe](https://stripe.com). StripeBundle is payment method for Symfony2 Payment Suite and it's built following [PaymentCore](https://github.com/mmoreram/PaymentCoreBundle) specifications. PaymentCore brings for developers easy way to implement several payment methods.

Installing Payment Environment
=====

StripeBundle works using an standard, defined in PaymentCoreBundle. You will find [here](http://github.com/mmoreram/PaymentCoreBundle) everything about how to configure your environment to work with this suite.

Installing Stripe Bundle
=====

You have to add next line into you composer.json file.

    "require": {
        "php": ">=5.3.3",
        "symfony/symfony": "2.3.*",
        ...
        "dpcat237/stripe-bundle": "dev-master"
    },

Then you have to use composer to update your project dependencies.

    php composer.phar update

And register StripeBundle in your `AppKernel.php` file.

    return array(
        // ...
        new Mmoreram\PaymentCoreBundle\PaymentCoreBundle(),
        new dpcat237\StripeBundle\StripeBundle(),
        // ...
    );

Configuration
=====

Configure the StripeBundle parameters in your `config.yml`.

    stripe:

        # stripe keys
        public_key: XXXXXXXXXXXX
        private_key: XXXXXXXXXXXX

        # By default, controller route is /payment/stripe/execute
        controller_route: /my/custom/route

        # Configuration for payment success redirection
        #
        # Route defines which route will redirect if payment success
        # If order_append is true, Bundle will append cart identifier into route
        #    taking order_append_field value as parameter name and
        #    PaymentOrderWrapper->getOrderId() value
        payment_success:
            route: cart_thanks
            order_append: true
            order_append_field: order_id

        # Configuration for payment fail redirection
        #
        # Route defines which route will redirect if payment fails
        # If cart_append is true, Bundle will append cart identifier into route
        #    taking cart_append_field value as parameter name and
        #    PaymentCartWrapper->getCartId() value
        payment_fail:
            route: cart_view
            cart_append: false
            cart_append_field: cart_id

About Stripe `public_key` and `private_key` you can learn more in [Stripe documentation page](https://stripe.com/docs/tutorials/dashboard#api-keys).

Router
=====

StripeBundle allows developer to specify the route of controller where stripe payment is processed.  
By default, this value is `/payment/stripe/execute` but this value can be changed in configuration file.  
Anyway StripeBundle's routes must be parsed by the framework, so these lines must be included into `routing.yml` file.

    stripe_payment_routes:
        resource: .
        type: stripe

Display
=====

Once your StripeBundle is installed and well configured, you need to place your payment form.

StripeBundle gives you all form view as requested by the payment module.

    {% block content %}

            <div class="payment-wrapper">

                {{ stripe_render() }}

            </div>

    {% endblock content %}

    {% block foot_script %}

        {{ parent() }}

        {{ stripe_scripts() }}

    {% endblock foot_script %}


Customize
=====

`stripe_render()` just print a basic form.

As every project need its own form design, you can overwrite default form located in: `app/Resources/StripeBundle/views/Stripe/view.html.twig` following [Stripe documentation](https://stripe.com/docs/tutorials/forms).

In another hand, Stripe [recommend](https://stripe.com/docs/tutorials/forms#create-a-single-use-token) use [jQuery form validator](https://github.com/stripe/jquery.payment).


Testing and more documentation
=====

For testing you can use these example [these example](https://stripe.com/docs/testing).
More detail about Stripe API you can find in this [web](https://stripe.com/docs/api/php).
