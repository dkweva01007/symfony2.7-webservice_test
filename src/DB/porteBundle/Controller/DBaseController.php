<?php

// src/OC/PlatformBundle/Controller/DbController.php

namespace DB\porteBundle\Controller;

use DB\porteBundle\Entity\account;
use DB\porteBundle\Entity\Account_historic;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DomCrawler\Crawler;

class DBaseController extends Controller {

    public function logservice(&$ch) {
        curl_setopt($ch, CURLOPT_URL, 'http://localhost/test4/web/app_dev.php/login');
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookie.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookie.txt');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        //$doc = new Crawler($response);
        //$doc = $doc->filterXPath('descendant-or-self::hidden/p');
       // dump($doc);
        curl_setopt($ch, CURLOPT_URL, 'http://localhost/test4/web/app_dev.php/login_check');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        $params = array(
            '_username' => "admin",
            '_password' => "admin",
        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        $response = curl_exec($ch);
        curl_setopt($ch, CURLOPT_POST, false);
    }

    public function getallAction() {
        //test requet get
        $ch = curl_init();
        $users = null;
        $this->logservice($ch);
        curl_setopt($ch, CURLOPT_URL, 'http://localhost/test4/web/app_dev.php/service/accounts.json');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        dump($response);
        $users = json_decode($response, true);
        curl_close($ch);  
        //dump($users);
        //decript JSON
        return $this->render('DBporteBundle:Consult:test.html.twig', array(
                    'entities' => $users['entities']));
    }

    public function adduserAction(Request $request) {

        //test création user
        $info = NULL;
        $ch = curl_init();
        $user = array();
        $formBuilder = $this->createFormBuilder();
        $formBuilder
                ->add('mail', 'text')
                ->add('Envoyer', 'submit');

        $form = $formBuilder->getForm();
        $form->handleRequest($request);
        $user = $form->getData();
        $user = json_encode($user);
        //verification que l'envoie est correcté et effectué
        if ($form->isValid() && $form->isSubmitted()) {
            curl_setopt($ch, CURLOPT_URL, 'http://localhost/test4/web/app_dev.php/service/accounts');
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $user);
            $response = curl_exec($ch);
            if (!$response) {
                throw $this->createNotFoundException(
                        'Something wrong'
                );
            }
            //decript JSON
            $info = json_decode($response);

            return $this->render('DBporteBundle:Consult:test2.html.twig', array(
                        'form' => $form->createView(),
                        'info' => $user,
            ));
        }
        return $this->render('DBporteBundle:Consult:test2.html.twig', array(
                    'form' => $form->createView(),
                    'info' => $info,
        ));
    }

    //test de modification du compte uttilisateur
    public function putuserAction(Request $request, $id) {

        $ch = curl_init();
        //recupération info du compte
        curl_setopt($ch, CURLOPT_URL, 'http://localhost/test4/web/app_dev.php/service/accounts/' . $id . '.json');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        $user = json_decode($response, true);
        //récupération des historique du compte
        curl_setopt($ch, CURLOPT_URL, 'http://localhost/test4/web/app_dev.php/service/accounthistoric_by_users/' . $id . '.json');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        $historic = json_decode($response, true);
        $formBuilder = $this->createFormBuilder();
        $formBuilder
                ->add('amount', 'money', array('currency' => 'EUR', 'precision' => 2))
                ->add('limitDate', 'datetime')
                ->add('Envoyer', 'submit');

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        //verification que l'envoie est correcté et effectué
        if ($form->isValid() && $form->isSubmitted()) {
            $custom = $form->getData();
            $custom = json_encode($custom);
            curl_setopt($ch, CURLOPT_URL, 'http://localhost/test4/web/app_dev.php/service/accounts/' . $id . '.json');
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $custom);
            $response = curl_exec($ch);
            dump($response);
        }
        if (!isset($historic['entity'])) {
            $historic['entity'] = NULL;
        }
        return $this->render('DBporteBundle:Consult:test3.html.twig', array(
                    'user' => $user['entity']['mail'],
                    'solde' => $user['entity']['amount'],
                    'limit_date' => $user['entity']['limit_date'],
                    'form' => $form->createView(),
                    'listhistoric' => $historic['entity'],
        ));
    }

}
