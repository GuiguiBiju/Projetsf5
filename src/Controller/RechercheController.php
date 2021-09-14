<?php

namespace App\Controller;

use App\Repository\AbonneRepository;
use App\Repository\LivreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RechercheController extends AbstractController
{
    #[Route('/recherche', name: 'recherche_index')]
    public function index(LivreRepository $lr, Request $rq, AbonneRepository $ar): Response
    {
        $livres_empruntes = $lr->livresEmpruntes();
        $mot = $rq->query->get("search"); //on recupere l'inpute nommé search dans le formulaire
        $livres = $lr->recherche($mot); //recupere tout les livres dont le titre correspond à la recherche
        $abonnes = $ar->recherche($mot);//recupere tout les abonnes dont le pseudo correspond à la recherche
        return $this->render('recherche/index.html.twig', compact("livres", "mot","livres_empruntes","abonnes"));
    }
}
