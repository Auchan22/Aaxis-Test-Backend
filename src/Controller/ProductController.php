<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/product")
 */
class ProductController extends AbstractController
{
    /**
     * @var ProductRepository $productRepository
     */
    private $productRepository;
    public function __construct(ProductRepository $repository)
    {
        $this->productRepository = $repository;
    }

    /**
     * @Route("/", name="list_products", methods={"GET"})
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $productos = $this->productRepository->findAll();

        return new JsonResponse($productos);
    }

    /**
     * @Route("/", name="create_products", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        dd($request->toArray());

        return new JsonResponse();
    }
//
//    public function update(): JsonResponse
}
