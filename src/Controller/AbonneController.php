<?php

namespace App\Controller;

use App\Entity\Abonne;
use App\Form\AbonneType;
use App\Repository\AbonneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/abonne')]
class AbonneController extends AbstractController
{
    #[Route('/', name: 'abonne_index', methods: ['GET'])]
    public function index(AbonneRepository $abonneRepository): Response
    {
        return $this->render('abonne/index.html.twig', [
            'abonnes' => $abonneRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'abonne_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserPasswordHasherInterface $hasher): Response
    {
        $abonne = new Abonne();
        $form = $this->createForm(AbonneType::class, $abonne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            //on va recuperer le mdp du formulaire pour l'encoder
            $mdp = $form->get("password")->getData(); //getData renvoi la valeur de l'objet password recupereé par le get (car get renvoie un objet)
            $mdp = $hasher->hashPassword($abonne, $mdp);
            $abonne->setPassword($mdp);

            $entityManager->persist($abonne);
            $entityManager->flush();
            $this->addFlash("success", "abonné ajouté avec succès");
            return $this->redirectToRoute('abonne_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('abonne/new.html.twig', [
            'abonne' => $abonne,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'abonne_show', methods: ['GET'])]
    public function show(Abonne $abonne): Response
    {
        return $this->render('abonne/show.html.twig', [
            'abonne' => $abonne,
        ]);
    }

    #[Route('/{id}/edit', name: 'abonne_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UserPasswordHasherInterface $hasher, Abonne $abonne): Response
    {
        $form = $this->createForm(AbonneType::class, $abonne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //on va recuperer le mdp du formulaire pour l'encoder
            $mdp = $form->get("password")->getData(); //getData renvoi la valeur de l'objet password recupereé par le get (car get renvoie un objet)
            if($mdp) // si le mdp n'est pas vide, alors on l'encode et on le met a jour
            {
                $mdp = $hasher->hashPassword($abonne, $mdp);
                $abonne->setPassword($mdp);
            }// sinon, on ne change pas le mdp
            

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash("success", "abonné édité correctement");
            return $this->redirectToRoute('abonne_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('abonne/edit.html.twig', [
            'abonne' => $abonne,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'abonne_delete', methods: ['POST'])]
    public function delete(Request $request, Abonne $abonne): Response
    {
        if ($this->isCsrfTokenValid('delete'.$abonne->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($abonne);
            $entityManager->flush();
            $this->addFlash("success", "Abonné supprimé");
        }

        return $this->redirectToRoute('abonne_index', [], Response::HTTP_SEE_OTHER);
    }
}
