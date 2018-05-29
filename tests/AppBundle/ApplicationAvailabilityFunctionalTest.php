<?php

namespace Tests\AppBundle;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApplicationAvailabilityFunctionalTest extends WebTestCase
{
    /**
     * FRONT URLS SUCCESSS
     * 
     * @dataProvider urlFrontProvider
     */
    public function testFrontPageIsSuccessful($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    //provider des url a tester via l'annotation
    public function urlFrontProvider()
    {
        /*
         Attention : si une base de test est créé (c'est elle qui sera utilisée), ce sont les element de la base de test qui vont etre testé
         si la base de test n'est pas iso (egale) avec la base de developpement certaines url , dans notre cas ne vont pas etre pareil
         il faut donc aller voir ce qui est disponible en BDD de TEST 
        */
        return array(
            array('/'),
            array('/movie/show/Blackfish'),
            array('/login'),           
        );
    }

    /**
     * 
     * BACK URLS SUCCESSS (admin)
     * 
     * @dataProvider urlBackRoleUserProvider
     */
    public function testBackRoleUserPageIsSuccessful($url)
    {
        //je creer mon client avec les données qui vont me permettre de m'authentifier
        // et j'ai changé auparavant dans ma config de test le systeme pour se logger (plus rapide)
        $client = self::createClient(array(), array(
            'PHP_AUTH_USER' => 'utilisateur1',
            'PHP_AUTH_PW'   => 'user123',
        ));

        //donnée fournies par les fixtures
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function urlBackRoleUserProvider()
    {   //attention a saisir l'url exact (slash de fin etc ...)
        return array(
            array('/admin/movie/'),       
            array('/admin/movie/6')          
        );
    }

     /**
     * 
     * BACK URLS SUCCESS (admin)
     * 
     * @dataProvider urlBackRoleAdminProvider
     */
    public function testBackRoleAdminPageIsSuccess($url)
    {
        $client = self::createClient(array(), array(
            'PHP_AUTH_USER' => 'administrateur1',
            'PHP_AUTH_PW'   => 'admin123',
        ));

        $client->request('GET', $url);

        //je verifie que mon utilisateur administrateur ai acces aux pages interdites aux autres users
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function urlBackRoleAdminProvider()
    { 
        return array(
            array('/admin/movie/5/edit'),       
            array('/admin/moviecast/new/6'),          
            array('/admin/movie/new'),          
        );
    }


    /**
     * 
     * BACK URLS FAIL (admin) - le role user n'a pas le droit d'acceder aux page d'action
     * 
     * @dataProvider urlBackRoleUserForbiddenProvider
     */
    public function testBackRoleUserPageIsForbidden($url)
    {
        $client = self::createClient(array(), array(
            'PHP_AUTH_USER' => 'utilisateur1',
            'PHP_AUTH_PW'   => 'user123',
        ));

        $client->request('GET', $url);

        //je verifie que mon utilisateur est interdit (403) sur les pages suivantes
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function urlBackRoleUserForbiddenProvider()
    { 
        return array(
            array('/admin/movie/5/edit'),       
            array('/admin/moviecast/new/6'),          
            array('/admin/movie/new'),          
        );
    }


}