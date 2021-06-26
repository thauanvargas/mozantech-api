<?php

namespace App\DataFixtures;

use App\Entity\Order;
use App\Entity\PaymentMethod;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        for ($i = 1; $i < 6; $i++) {
            $product = new Product();
            $product->setName('Produto ' . $i);
            $product->setPrice(mt_rand(10, 100));

            if($i <= 3) {
                $order = new Order();
                $order->addProduct($product);

                $paymentMethod = new PaymentMethod();
                $name = $i === 1 ? "Credit card" : ($i === 2 ? "Multibanco" : "Paypal");
                $paymentMethod->setName($name);

                if($name === "Multibanco") {
                    $paymentMethod->setSendPaymentEmail(true);
                }

                if($name === "Paypal") {
                    $paymentMethod->setDiscount(10);
                    $order->setTotalPrice($product->getPrice() - ($product->getPrice() * 0.10));
                }else{
                    $order->setTotalPrice($product->getPrice());
                }

                $order->setPaymentMethod($paymentMethod);


                $manager->persist($paymentMethod);
                $manager->persist($order);
            }

            $manager->persist($product);
        }


        $manager->flush();
    }
}
