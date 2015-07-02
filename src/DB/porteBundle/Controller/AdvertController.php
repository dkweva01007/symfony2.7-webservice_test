<?php

// src/OC/PlatformBundle/Controller/AdvertController.php

namespace DB\porteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdvertController extends Controller {

    // La route fait appel à DBporteBundle:Advert:view,
    // on doit donc définir la méthode viewAction.
    // On donne à cette méthode l'argument $id, pour
    // correspondre au paramètre {id} de la route
    public function viewAction($id) {
        // On crée la réponse sans lui donner de contenu pour le moment
        $response = new Response;
        // On définit le contenu
        $response->setContent("Ceci est une page d'erreur 404");
        // On définit le code HTTP à « Not Found » (erreur 404)
        $response->setStatusCode(Response::HTTP_NOT_FOUND);
        // On retourne la réponse
        return $response;
    }

    public function viewSlugAction($slug, $year, $format) {
        return new Response(
                "On pourrait afficher l'annonce correspondant au
            slug '" . $slug . "', créée en " . $year . " et au format " . $format . "."
        );
    }

    public function indexAction() {
        // On veut avoir l'URL de l'annonce d'id 5.
        $url = $this->get('router')->generate(
                'd_bporte_view', // 1er argument : le nom de la route
                array('id' => 5), true   // 2e argument : les valeurs des paramètres
        );
        // $url vaut « /platform/advert/5 »
        return new Response("L'URL de l'annonce d'id 5 est : " . $url);
    }

}
