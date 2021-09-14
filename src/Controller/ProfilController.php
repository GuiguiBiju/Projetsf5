<?php

namespace App\Controller;

use App\Entity\Emprunt;
use App\Entity\Livre;
use App\Repository\LivreRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'profil_index')]
    #[IsGranted('ROLE_LECTEUR')]
    public function index(): Response
    {
        /* pour avoir les informations de l'utilisateur connecté:
            2 solutions:
            - dans le twig: app.user
            - dans le controlleur: $abonneConnecte = $this->getUser();    
        */
        return $this->render('profil/index.html.twig');
    }

    #[Route('/profil/emprunter/{id}', name: 'profil_emprunter')]
    public function emprunter(Livre $livre, EntityManagerInterface $em, LivreRepository $lr)
    {
        $livresEmpruntes = $lr->livresEmpruntes();
        if(in_array($livre, $livresEmpruntes))
        {
            $this->addFlash("danger", "le livre <strong>" .$livre->getTitre() . "</strong> n'est pas disponible !");
            return $this->redirectToRoute("accueil");
        }
        /* Exo: l'utilisateur emprunte aujourd'hui le livre sur lequel il a cliqué */
        $emprunt = new Emprunt;
        $emprunt->setDateEmprunt(new DateTime()); // new DateTime() créer un objet datetime avec la date du jour
        $emprunt->setLivre($livre);               // $livre a été recuperé de la bdd avec l'id qui est passé dans le chemin
        $emprunt->setAbonne( $this->getUser());   // $this->getUser() retourne un objet contenant les infos de l'abonné actuellement connecté

        $em->persist($emprunt); // comme $emprunt est un nouvel emprunt à insérer dans la bdd, il faut utiliser $em->persist
        $em->flush();           // $em->flush() enregistre en bdd
        $this->addFlash("success", "le livre à été emprunté!");
        return $this->redirectToRoute("profil_index");

    }




}
