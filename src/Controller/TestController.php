<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * c'est la premiere route de notre rojet
     * chemin de l'url et nom de la route (il faut toujours avoir un name different pour une route)
     */
    #[Route('/test', name: 'test')]
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/TestController.php',
        ]);
    }

    #[Route('/test/nouvelleRoute', name: 'test2')]
    public function nouvelle_route(): Response
        {

        /* La méthode 'render' permet de générer l'affichage d'un fichier qui se trouve dans le dossier 'templates
        le 1er parametres est le nom du c=fichier
        le 2nd parametre n'est pas obligatoire, il doit etre de type array et contiendra toutes les variables que l'on veut transmettre à la vue
        */
        return $this->render("base.html.twig", ["prenom" => "Guillaume"]);
    }

    #[Route('/test/tableau', name: 'test_tableau')]
    public function tableau(): Response
        {
            $tableau = ["un", 2, true];
            $tableau2 = [ "nom" => "cérien", "prenom" => "jean", "age" => 30];

            //Exo: je veux transmettre la valeur de la variable $tableau2 à ma vue dans une variable nomée "personne"
            //Ensuite afficher, "je m'appel" suivi du prenom et age

        return $this->render("test/tableau.html.twig", [
            "tableau" => $tableau,
            "personne" => $tableau2
            ]);
    }

    #[Route('/test/objet', name: 'test_objet')]
    public function objet()
    {
        $objet = new \stdClass(); //on créer un objet standard (std) vide et on lui attribue des valeurs
        $objet->nom = "Mentor";
        $objet->prenom = "Gérard";
        $objet->age = "54";

        return $this->render("test/tableau.html.twig", ["personne" => $objet]);
    }

    #[Route('/test/salut/{prenom}')] // dans le chemin, les {} signifient que cette partie du chemin est variable. cela peut etre n'importe quel chaine de caracteres. Le nom mis entre {} est le nom de la variable passée en paramètre de la méthode prenom ci dessous
    public function prenom($prenom)
    {
        return $this->render("base.html.twig", ["prenom" => $prenom]);
    }

/*
    Exo: ajouter une route, "/test/liste/{nombre}"
            Le nombre passé en paramètre devra être envoyé à une vue qui étend base.html.twig
            Cette vue va afficher la liste des nombres de 1 jusqu'au nombre passé dans le chemin dans une table html
            dans la première colonne, le nombre
            dans la deuxieme colonne, le nombre multiplié par 2
*/

#[Route('/test/liste/{nombre}')] // dans le chemin, les {} signifient que cette partie du chemin est variable. cela peut etre n'importe quel chaine de caracteres. Le nom mis entre {} est le nom de la variable passée en paramètre de la méthode prenom ci dessous
    public function nombre($nombre)
    {
        return $this->render("test/nombre.html.twig", ["nombre" => $nombre]);
    }

    /*
    Exo: créer une nouvelle route qui prend un nombre dans l'url et qui affiche le resultat de ce nombre au carré
*/

#[Route('/test/liste2/{nombre}')] // dans le chemin, les {} signifient que cette partie du chemin est variable. cela peut etre n'importe quel chaine de caracteres. Le nom mis entre {} est le nom de la variable passée en paramètre de la méthode prenom ci dessous
    public function nombre2($nombre)
    {
        $test = true;
        return $this->render("test/nombre.html.twig", [
            "nombre" => $nombre,
            "nombre2" => $test
        ]);
    }

/*
    Créer un contrôleur Livre

    Dans ce contrôleur, créer une route "/mes-livres"
    
    Dans la méthode (nommée mesLivres par exemple), créer une variable array qui va
    contenir des arrays
    titre = "Dune", auteur = "Frank Herbert"
    titre = "1984", auteur = "George Orwell"
    titre = "Le Seigneur des Anneaux", auteur = "J.R.R. Tolkien"
    
    Donc dans la variable $liste, il aura 3 valeurs, 
        chaque valeur est un tableau avec un indice "titre" et un indice "auteur"
        
    Afficher la liste des livres dans une table HTML
*/


}
