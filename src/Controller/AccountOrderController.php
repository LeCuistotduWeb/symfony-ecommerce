<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountOrderController extends AbstractController
{
    /**
     * @Route("/compte/mes-commandes", name="account_order")
     */
    public function index(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findBySuccessOrders($this->getUser());

        return $this->render('account/order.html.twig', [
            'orders' => $orders,
        ]);
    }

    /**
     * @Route("/compte/mes-commandes/{reference}", name="account_order_show")
     * @param $reference
     * @param OrderRepository $orderRepository
     * @return Response
     */
    public function show($reference, OrderRepository $orderRepository): Response
    {
        $order = $orderRepository->findOneByReference($reference);

        if($order->getUser() != $this->getUser()){
            return $this->redirectToRoute('account_order');
        };

        return $this->render('account/show.html.twig', [
            'order' => $order,
        ]);
    }
}
