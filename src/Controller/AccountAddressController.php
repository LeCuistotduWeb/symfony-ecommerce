<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountAddressController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/compte/adresses", name="account_address")
     */
    public function index(): Response
    {
        return $this->render('account/address.html.twig', []);
    }

    /**
     * @Route("/compte/ajouter-une-adresse", name="account_address_add")
     * @param Request $request
     * @return Response
     */
    public function add(Request $request): Response
    {
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address, []);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $address->setUser($this->getUser());
            $form->getData();
            $this->entityManager->persist($address);
            $this->entityManager->flush();
            return $this->redirectToRoute('account_address');
        }

        return $this->render('account/address_form.html.twig', [
            'form'=> $form->createView(),
        ]);
    }

    /**
     * @Route("/compte/modifier-une-adresse/{id}", name="account_address_edit")
     * @param Request $request
     * @param $id
     * @param AddressRepository $addressRepository
     * @return Response
     */
    public function edit(Request $request, $id, AddressRepository $addressRepository): Response
    {
        $address = $addressRepository->findOneById($id);
        $form = $this->createForm(AddressType::class, $address);

        if(!$address || $address->getUser() != $this->getUser()){
            return $this->redirectToRoute('account_address');
        }

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->entityManager->flush();
            return $this->redirectToRoute('account_address');
        }

        return $this->render('account/address_form.html.twig', [
            'form'=> $form->createView(),
        ]);
    }

    /**
     * @Route("/compte/supprimer-une-adresse/{id}", name="account_address_delete")
     * @param $id
     * @param AddressRepository $addressRepository
     * @return Response
     */
    public function delete($id, AddressRepository $addressRepository): Response
    {
        $address = $addressRepository->findOneById($id);

        if($address || $address->getUser() === $this->getUser()){
            $this->entityManager->remove($address);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('account_address');
    }
}
