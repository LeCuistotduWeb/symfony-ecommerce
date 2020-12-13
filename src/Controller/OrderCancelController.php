<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderCancelController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/commande/erreur/{$stripeSessionId}", name="order_cancel")
     * @param $stripeSessionId
     * @param OrderRepository $orderRepository
     * @return Response
     */
    public function index($stripeSessionId, OrderRepository $orderRepository): Response
    {
        $order = $orderRepository->findOneByStripeSessionId($stripeSessionId);

        if(!$order || $order->getUser() != $this->getUser()){
            $this->redirectToRoute('home');
        }

        // enovyer un mail pour echec de paiment.

        return $this->render('order_cancel/index.html.twig', [
            'order'=> $order,
        ]);
    }
}
