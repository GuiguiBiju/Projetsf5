<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Livre;
use App\Form\LivreType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface as EntityManager;
use App\Repository\LivreRepository;

#[Route('/admin')]
class LivreController extends AbstractController
{
    #[Route('/livre', name: 'livre_index')]
    public function index(LivreRepository $lr): Response
    {
        // findAll est une methode propre a LivreRepository
        return $this->render('livre/index.html.twig', [
            "liste" => $lr->findAll (),
            "livres_empruntes" => $lr->livresEmpruntes()
        ]);
    }
    
    #[Route('/mes-livre', name: 'livres')]
    public function mesLivres(): Response
    {
        $array=[["titre" => "dunes", "auteur" => "Franck Herbert"],["titre" => "1984", "auteur" => "George Orwell"],["titre" => "Le seigneur des anneaux", "auteur" => "J.R.R Tolkien"]];
        return $this->render('livre/livre.html.twig', [
            'liste' => $array,
        ]);
    }

    /*
    pour instancier un objet de la classe Request, on va utiliser l'injection de dépendance
    On définit un paramêtre dans une méthode d'un controleur de la classe Request et dans cette méthode, on pourra utiliser l'objet qui contiendra des propriétés avec toutes les valeurs des superglobales de php
    ex: $request->query     : cette propriété est l'objet qui a les valeurs de $_GET
        $request->request   : propriété qui a les valeurs de $_POST 
    */
    #[Route('/livre/ajouter', name: 'livre_ajouter')]
    public function ajouter(Request $request, EntityManager $em, CategorieRepository $cr) // injection de dépendance 
    {
        //dump($_POST); //equivalent de var_dump
        //dump($request); 

        if($request->isMethod("POST")) // si je demande un POST
        {
            $titre = $request->request->get("titre"); // la méthode 'get permet de recuperer les valeurs des inputs du formulaire
            $auteur = $request->request->get("auteur");
            $categorie_id = $request->request->get("categorie");
            if( $titre && $auteur ) // si $titre et $auteur ne sont pas vides
            {
                $nouveauLivre = new Livre;
                $nouveauLivre->setTitre($titre);
                $nouveauLivre->setAuteur($auteur);
                $nouveauLivre->setCategorie($cr->find($categorie_id));
                /* On va utiliser l'objet $em de la classe EntityManager poure enregistrer en BDD
                La méthode "persist" permet de préparer une requete INSERT INTO. le paramètres DOIT ETRE un objet d'une classe Entity */
                $em->persist($nouveauLivre);
                /* la méthode 'flush' éxecute toutes les requètes en attente. la BDD est modifiée quand cette méthode est lancée ( et pas avant ) */ 
                $em->flush();
                return $this->redirectToRoute("livre_index"); //redirection vers la liste des livres
            }
        }
        //EXO: Afficher un formulaire pour pouvoir ajouter un livre
        // Ajouter un lien dans le menu pour acceder à cette route
        return $this->render('livre/ajout.html.twig',[
            "categories" => $cr->findAll()
        ]);
    }

    #[Route('/livre/modifier/{id}', name: 'livre_modifier')]
    public function modifier(EntityManager $em, Request $request, LivreRepository $lr, $id)
    {
        $livre = $lr->find($id); //find reourne l'objet livre dont l'id vaut $id en BDD
        // createForm() va créer un objet représentant le formulaire créé à partir de la classe LivreType
        // Le 2eme paramètre est un objet Entity qui sera lié au formulaire
        $form = $this->createForm(LivreType::class, $livre);
        // la méthode 'HandleRequest' permet à $form de gérer les informations venant de la requete HTTP
        //exemple: est ce que le formulaire à été soumis? ...
        $form->handleRequest($request);
        if( $form->isSubmitted() && $form->isValid()) //si le formulaire est soumis et valide
        {
            if ($fichier = $form->get("couverture")->getData())
            { // si le formulaire renvoi un fichier
                //on recupere le nom du fichier qui à été téléversé
                $nomFichier = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
                //on remplace les espaces par des _
                $nomFichier = str_replace(" ", "_", $nomFichier);

                // on ajoute un string au nom du fichier pour éviter les doublons et l'extension du fichier
                $nomFichier .= uniqid() . "." . $fichier->guessExtension();

                // on copie le fichier uploadé dans un dossier du dossier 'public' avec le nouveau nom de fichier
                $fichier->move($this->getParameter("dossier_images"), $nomFichier);

                // on modifie l'entité $livre
                $livre->setCouverture($nomFichier);
            }

            //toutes les modifications des objets Entity qui ont été instancié à partir de la bdd vont être enregistrées en bdd quand on va utiliser le flush
            $em->flush();
            $this->addFlash("success", "le livre à été modifié avec succès");
            return $this->redirectToRoute("livre_index");
        }

        return $this->render('livre/form.html.twig', [
            "formLivre" => $form->createView()
        ]);
    }
    
    #[Route('/livre/supprimer/{id}', name: 'livre_supprimer')]
    public function supprimer(Request $request, EntityManager $em, Livre $livre)
    {
        // si le paramètre placé dans le chemin est une propriété d'une classe Entity, on peut récuperer directement l'objet dont la propriété vaut ce qui sera passé dans l'url ($livre contiendra le livre dont l'id sera passé dans l'url (ou son titre si le {titre} est demandé dans l'url))
        //dd($livre); //dd = dump & die : var_dump et l'exécution du code est arretté

        if($request->isMethod("POST"))
        {
            $em->remove($livre); // la requete delete est en attente
            $em->flush(); //toutes les requetes en attentes sont executées
            $this->addFlash("success", "le livre a été supprimer avec succès");
            return $this->redirectToRoute("livre_index");
        }


        return $this->render("livre/supprimer.html.twig", ["livre" => $livre]);
    }

    #[Route('/livre/fiche/{id}', name: 'livre_show', methods: ['GET'])]
    public function show(Livre $livre): Response
    {
        //la fonction compact() php retourne un array associatif à partir des variables qui ont le meme nom que les parametres passé a compact
        // par ex: si j'ai 2 variables
        //$nom ="boby"
        //$prenom ="nordine"
            //$personne = compact("nom","prenom"); est equivalent à
            //$personne =[  "nom" => "boby", "prenom" => "nordine"];

        //return $this->render('livre/show.html.twig', compact("livre"));
        return $this->render('livre/show.html.twig', [
            'livre' => $livre,
        ]);
    }

    #[Route('/livre/nouveau', name: 'livre_nouveau')]
    public function nouveau(Request $request, EntityManager $em)
    {
        $livre = new Livre; //on créer un nouvel objet livre
        $form = $this->createForm(LivreType::class, $livre);
        // la méthode 'HandleRequest' permet à $form de gérer les informations venant de la requete HTTP
        $form->handleRequest($request);
        if( $form->isSubmitted() && $form->isValid()) //si le formulaire est soumis et valide
        {
            if ($fichier = $form->get("couverture")->getData())
            { // si le formulaire renvoi un fichier
                //on recupere le nom du fichier qui à été téléversé
                $nomFichier = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
                $nomFichier = str_replace(" ", "_", $nomFichier);
                $nomFichier .= uniqid() . "." . $fichier->guessExtension();
                $fichier->move($this->getParameter("dossier_images"), $nomFichier);
                // on modifie l'entité $livre
                $livre->setCouverture($nomFichier);
            }
            $em->persist($livre);
            $em->flush();
            $this->addFlash("success", "le nouveau livre a été enregistré");
            return $this->redirectToRoute("livre_index");
        }

        return $this->render('livre/form.html.twig', [
            "formLivre" => $form->createView()
        ]);
    }



}
