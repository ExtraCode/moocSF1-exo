<?php

namespace App\Controller;

use App\Entity\Prestation;
use App\Form\PrestationType;
use App\Repository\PrestationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/prestation', name: 'app_prestation_')]
class PrestationController extends AbstractController
{
    #[Route('/', name: 'liste')]
    public function index(PrestationRepository $prestationRepository, UserRepository $userRepository): Response
    {

        $prestations = $prestationRepository->listPrestations(10);
        $lastUser = $userRepository->lastUserRegistered();

        return $this->render('prestation/index.html.twig', [
            'prestations' => $prestations,
            'lastUser' => $lastUser
        ]);
    }

    #[Route('/ajouter', name: 'ajouter')]
    public function ajouter(Request $request, EntityManagerInterface $entityManager): Response
    {
        $prestation = new Prestation();
        $form = $this->createForm(PrestationType::class,$prestation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $prestation->setDateCreation(new \DateTime());

            $entityManager->persist($prestation);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'La prestation a bien été ajoutée !'
            );

            return $this->redirectToRoute('app_prestation_liste');


        }

        return $this->render('prestation/ajouter.html.twig', [
            'form'=> $form
        ]);
    }
}
