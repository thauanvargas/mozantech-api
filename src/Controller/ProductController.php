<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="indexProduct", methods={"GET"})
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();

        $products = $em->getRepository(Product::class)->findAll();

        if (count($products) === 0) {
            return $this->json([
                'status' => "nOK",
                'message' => "There's no Orders yet, make sure to load the fixtures or create a new one."
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->json([
            'products' => $products
        ], Response::HTTP_OK, [], ['groups' => 'product']);
    }

    /**
     * @Route("/product", name="createProduct", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $content = json_decode($request->getContent());

        if (!$content->name || !$content->price)
            return $this->json([
                    "status" => "nOK",
                    "message" => "Bad Request"
                ]
                , Response::HTTP_BAD_REQUEST);

        $product = new Product();

        $product->setName($content->name);
        $product->setPrice($content->price);

        $em->persist($product);
        $em->flush();

        return $this->json([
            'status' => 'ok',
            'product' => $product
        ], Response::HTTP_CREATED);
    }

    /**
     * @Route("/product/{id}", name="showProduct", methods={"GET"})
     * @param int $id
     * @return Response
     */
    public function show(int $id): Response
    {
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository(Product::class)->find($id);

        if (!$product) {
            return $this->json([
                'status' => "nOK",
                'message' => "Order not found"
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'product' => $product
        ], Response::HTTP_OK, [], ['groups' => 'order', 'product', 'paymentMethod']);
    }

}
