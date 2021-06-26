<?php

namespace App\Controller;

use App\Entity\PaymentMethod;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentMethodController extends AbstractController
{
    /**
     * @Route("/payment-method", name="indexPaymentMethod", methods={"GET"})
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();

        $paymentMethods = $em->getRepository(PaymentMethod::class)->findAll();

        if (count($paymentMethods) === 0) {
            return $this->json([
                'status' => "nOK",
                'message' => "There's no Payment Methods yet, make sure to load the fixtures or create a new one."
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->json([
            'paymentMethods' => $paymentMethods
        ], Response::HTTP_OK, [], ['groups' => 'paymentMethod']);
    }

    /**
     * @Route("/payment-method", name="createPaymentMethod", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $content = json_decode($request->getContent());

        if (!$content->name)
            return $this->json([
                    "status" => "nOK",
                    "message" => "Please provide the payment method name"
                ]
                , Response::HTTP_BAD_REQUEST);

        $paymentMethod = new PaymentMethod();

        $paymentMethod->setName($content->name);
        $paymentMethod->setDiscount(isset($content->sendPaymentEmail) ? $content->discount : null);
        $paymentMethod->setSendPaymentEmail(isset($content->sendPaymentEmail));

        $em->persist($paymentMethod);
        $em->flush();

        return $this->json([
            'status' => 'ok',
            'paymentMethod' => $paymentMethod
        ], Response::HTTP_CREATED);
    }

    /**
     * @Route("/payment-method/{id}", name="showPaymentMethod", methods={"GET"})
     * @param int $id
     * @return Response
     */
    public function show(int $id): Response
    {
        $em = $this->getDoctrine()->getManager();

        $paymentMethod = $em->getRepository(PaymentMethod::class)->find($id);

        if (!$paymentMethod) {
            return $this->json([
                'status' => "nOK",
                'message' => "Order not found"
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'paymentMethod' => $paymentMethod
        ], Response::HTTP_OK, [], ['groups' => 'order', 'product', 'paymentMethod']);
    }

}
