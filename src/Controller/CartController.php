<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/mon-panier", name="cart")
     * @param Cart $cart
     * @return Response
     */
    public function index(Cart $cart, ProductRepository $productRepository): Response
    {
        return $this->render('cart/index.html.twig', [
            'cart' => $cart->getFull(),
        ]);
    }

    /**
     * @Route("/cart/add/{id}", name="add_to_cart")
     * @param Cart $cart
     * @param $id
     * @return Response
     */
    public function add(Cart $cart, $id): Response
    {
        $cart->add($id);
        return $this->redirectToRoute("cart");
    }

    /**
     * @Route("/cart/remove", name="remove_my_cart")
     * @param Cart $cart
     * @return Response
     */
    public function remove(Cart $cart): Response
    {
        $cart->remove();
        return $this->redirectToRoute("products");
    }

    /**
     * @Route("/cart/delete/{id}", name="delete_to_cart")
     * @param Cart $cart
     * @param $id
     * @return Response
     */
    public function delete(Cart $cart, $id): Response
    {
        $cart->delete($id);
        return $this->redirectToRoute("cart");
    }

    /**
     * @Route("/cart/decrease/{id}", name="decrease_to_cart")
     * @param Cart $cart
     * @param $id
     * @return Response
     */
    public function decrease(Cart $cart, $id): Response
    {
        $cart->decrease($id);
        return $this->redirectToRoute("cart");
    }
}
