<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
     * @param ValidatorInterface $validator
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function create(Request $request, ValidatorInterface $validator, EntityManagerInterface $em): JsonResponse
    {
        $body = json_decode($request->getContent(), true);

        if(!is_array($body)){
            return new JsonResponse(["error" => "El cuerpo de la petición debe ser un array de objetos 'Product'"], Response::HTTP_BAD_REQUEST);
        }

        if(empty($body)){
            return new JsonResponse(["error" => "El array de objetos debe tener por lo menos 1 objeto"], Response::HTTP_BAD_REQUEST);
        }

        foreach ($body as $product) {
            if(!isset($product["sku"]) || !isset($product["description"]) || !isset($product["product_name"])){
                return new JsonResponse(['error' => "Se deben adjuntar los campos: 'sku', 'description', 'product_name'"], Response::HTTP_BAD_REQUEST);
            }

            $p = new Product();

            $p->setSku($product["sku"]);
            $p->setDescription($product["description"]);
            $p->setProductName($product["product_name"]);
            $p->setCreatedAt(new \DateTimeImmutable());
            $p->setUpdatedAt(new \DateTimeImmutable());

            $errors = $validator->validate($p, null, ["create"]);

            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage();
                }

                return new JsonResponse(['error' => 'Errores de validación: ' . implode(', ', $errorMessages)], Response::HTTP_BAD_REQUEST);
            }

            $em->persist($p);
        }

        $em->flush();

        return new JsonResponse(["msg" => "Se crearon todos los registros"], Response::HTTP_CREATED);
    }

    /**
     * @Route("/", name="update_products", methods={"PUT"})
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param ProductRepository $pr
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function update(Request $request, ValidatorInterface $validator, ProductRepository $pr, EntityManagerInterface $em): JsonResponse
    {
        $body = json_decode($request->getContent(), true);

        if(!is_array($body)){
            return new JsonResponse(["error" => "El cuerpo de la petición debe ser un array de objetos 'Product'"], Response::HTTP_BAD_REQUEST);
        }

        if(empty($body)){
            return new JsonResponse(["error" => "El array de objetos debe tener por lo menos 1 objeto"], Response::HTTP_BAD_REQUEST);
        }


        foreach ($body as $product) {

            if(!isset($product["sku"])){
                return new JsonResponse(['error' => "Se debe adjuntar el campo de 'sku'"], Response::HTTP_BAD_REQUEST);
            }
            if(!isset($product["description"]) && !isset($product["product_name"])){
                return new JsonResponse(['error' => "El producto con sku: ". $product["sku"] ." debe adjuntar por lo menos uno de los siguientes campos: 'description' o 'product_name'"], Response::HTTP_BAD_REQUEST);
            }

            $p = $pr->findOneBy(["sku" => $product["sku"]]);

            if(is_null($p)){
                return new JsonResponse(['error' => "El producto con el sku: ". $product["sku"] ." no existe"], Response::HTTP_NOT_FOUND);
            }

            if(isset($product["description"])){
                $p->setDescription($product["description"]);
            }

            if(isset($product["product_name"])){
                $p->setDescription($product["product_name"]);
            }

            $p->setUpdatedAt(new \DateTimeImmutable());

            $errors = $validator->validate($p, null, ["create"]);

            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage();
                }

                return new JsonResponse(['error' => 'Errores de validación: ' . implode(', ', $errorMessages)], Response::HTTP_BAD_REQUEST);
            }

        }

        $em->flush();

        return new JsonResponse(["msg" => "Se actualizaron todos los registros"], Response::HTTP_OK);
    }
}
