<?php

namespace App\Controller;

use App\Classes\Search;
use App\Form\SearchType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/nos-produits", name="products")
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function index(Request $request, ProductRepository $productRepository): Response
    {
        $search = New Search();
        $form = $this->createForm(SearchType::class, $search);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $products = $productRepository->findWithSearch($search);
        }else {
            $products = $productRepository->findAll();
        }

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/protduit/{slug}", name="product")
     * @param $slug
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function show($slug, ProductRepository $productRepository): Response
    {

        $product = $productRepository->findOneBySlug($slug);
        if(!$product) {
            return $this->redirectToRoute('products');
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }
}
