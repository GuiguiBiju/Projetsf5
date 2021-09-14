<?php

namespace App\Controller;

use App\Entity\Emprunt;
use App\Form\EmpruntType;
use App\Repository\AbonneRepository;
use App\Repository\EmpruntRepository;
use App\Repository\LivreRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/emprunt')]
#[IsGranted('ROLE_BIBLIO')] // toutes les routes de ce controller sont restreintes aux utilisateurs ayant le ROLE_BIBLIO
class EmpruntController extends AbstractController
{
    #[Route('/', name: 'emprunt_index', methods: ['GET'])]
    public function index(EmpruntRepository $empruntRepository): Response
    {
        return $this->render('emprunt/index.html.twig', [
            'emprunts' => $empruntRepository->findAll(),
            'empruntsNonRendus' => $empruntRepository->empruntsNonRendus()
        ]);
    }

    #[Route('/new', name: 'emprunt_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $emprunt = new Emprunt();
        $form = $this->createForm(EmpruntType::class, $emprunt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($emprunt);
            $entityManager->flush();
            $this->addFlash("success", "nouvel emprunt ajouté avec succès");
            return $this->redirectToRoute('emprunt_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('emprunt/new.html.twig', [
            'emprunt' => $emprunt,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'emprunt_show', methods: ['GET'])]
    public function show(Emprunt $emprunt): Response
    {
        return $this->render('emprunt/show.html.twig', [
            'emprunt' => $emprunt,
        ]);
    }

    #[Route('/{id}/edit', name: 'emprunt_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Emprunt $emprunt): Response
    {
        $form = $this->createForm(EmpruntType::class, $emprunt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash("success", "emprunt édité avec succès");
            return $this->redirectToRoute('emprunt_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('emprunt/edit.html.twig', [
            'emprunt' => $emprunt,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'emprunt_delete', methods: ['POST'])]
    public function delete(Request $request, Emprunt $emprunt): Response
    {
        if ($this->isCsrfTokenValid('delete'.$emprunt->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($emprunt);
            $entityManager->flush();
            $this->addFlash("success", "l'emprunt à été supprimer correctement'");
        }

        return $this->redirectToRoute('emprunt_index', [], Response::HTTP_SEE_OTHER);
    }
}
