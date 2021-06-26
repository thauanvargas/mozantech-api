<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\PaymentMethod;
use App\Entity\Product;
use Doctrine\Common\Util\Debug;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class OrderController extends AbstractController
{
    /**
     * @Route("/order", name="indexOrder", methods={"GET"})
     * @return Response
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();

        $orders = $em->getRepository(Order::class)->findAll();

        if (count($orders) === 0) {
            return $this->json([
                'status' => "nOK",
                'message' => "There's no Orders yet, make sure to load the fixtures or create a new one."
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->json([
            'orders' => $orders
        ], Response::HTTP_OK, [], ['groups' => 'order', 'product', 'paymentMethod']);
    }

    /**
     * @Route("/order", name="createOrder", methods={"POST"})
     * @param Request $request
     * @param LoggerInterface $logger
     * @return Response
     */
    public function create(Request $request, LoggerInterface $logger): Response
    {
        $em = $this->getDoctrine()->getManager();

        $order = new Order();

        $content = json_decode($request->getContent());
        $totalPrice = 0;


        if (!$content->products || !$content->paymentMethod)
            return $this->json([
                    "status" => "nOK",
                    "message" => "Please provide the product(s) and payment method"
                ]
                , Response::HTTP_BAD_REQUEST);

        foreach ($content->products as $productId) {
            $product = $em->getRepository(Product::class)->find($productId);

            if (!$product) {
                return $this->json([
                        "status" => "nOK",
                        "message" => "Couldn't find any Product with the id " . $productId
                    ]
                    , Response::HTTP_BAD_REQUEST);
            }

            $totalPrice += $product->getPrice();

            $order->addProduct($product);
        }

        $paymentMethod = $em->getRepository(PaymentMethod::class)->find($content->paymentMethod);

        if (!$paymentMethod) {
            return $this->json([
                    "status" => "nOK",
                    "message" => "Couldn't find any Payment Method with the id " . $content->paymentMethod
                ]
                , Response::HTTP_BAD_REQUEST);
        }

        if($paymentMethod->getDiscount() !== null) {
            $totalPrice = $totalPrice - ($totalPrice * ($paymentMethod->getDiscount() / 100));
        }

        if($paymentMethod->getSendPaymentEmail()) {
            $logger->info('Email was sent');
        }

        $order->setPaymentMethod($paymentMethod);
        $order->setTotalPrice($totalPrice);

        $em->persist($order);
        $em->flush();


        return $this->json([
            'status' => 'success',
            'orders' => $order
        ], Response::HTTP_OK, [], ['groups' => 'order', 'product', 'paymentMethod']);
    }

    /**
     * @Route("/order/{id}", name="showOrder", methods={"GET"})
     * @param int $id
     * @return Response
     */
    public function show(int $id): Response
    {
        $em = $this->getDoctrine()->getManager();

        $order = $em->getRepository(Order::class)->find($id);

        if (!$order) {
            return $this->json([
                'status' => "nOK",
                'message' => "Order not found"
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'order' => $order
        ], Response::HTTP_OK, [], ['groups' => 'order', 'product', 'paymentMethod']);
    }

    /**
     * @Route("/order/{id}/pay", name="payOrder", methods={"PUT"})
     * @param int $id
     * @return Response
     */
    public function payOrder(int $id): Response
    {
        $em = $this->getDoctrine()->getManager();

        $order = $em->getRepository(Order::class)->find($id);

        if (!$order) {
            return $this->json([
                'status' => "nOK",
                'message' => "Order not found"
            ], Response::HTTP_NOT_FOUND);
        }

        if($order->getStatus() !== "paid") {
            $order->setStatus("paid");
            $em->persist($order);
            $em->flush();
        }else{
            return $this->json([
                'status' => "ok",
                'message' => "This order has already been paid."
            ], Response::HTTP_OK);
        }

        return $this->json([
            'order' => $order
        ], Response::HTTP_OK, [], ['groups' => 'order', 'product', 'paymentMethod']);
    }

}
