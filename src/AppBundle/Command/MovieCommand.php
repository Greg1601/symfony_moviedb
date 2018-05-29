<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bridge\Doctrine\RegistryInterface as Doctrine;
use AppBundle\Entity\Movie;

class MovieCommand extends Command
{   
    private $doctrine;

    public function __construct(Doctrine $doctrine)
    {
        $this->doctrine = $doctrine;
        parent::__construct();
    }
    //configuration commande
    protected function configure()
    {   
        $this
         // the name of the command (the part after "bin/console")
         ->setName('app:movie:image')
         ->setDescription('Import movie image to database')
         ->setHelp('This command allows you to get movie images by API');

        //->addArgument('username', InputArgument::OPTIONAL, 'The username of the user.')
         /*->addOption(
            'username',
            'u',
            InputOption::VALUE_REQUIRED,
            'Name of the user'
        )*/
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            'Movie poster import',
            '============',
            '',
        ]);
        
        $em = $this->doctrine->getManager();

        //je recupere tout mes film à mettre a jour
        $movies = $this->doctrine->getRepository(Movie::class)->findAll();
        
        foreach($movies as $movie){

            $title = $movie->getTitle();

            //execute une requete HTTP avec retour
            $result = $this->getCurl($title);
            
            //si je n'ai pas eu d'erreur sur mon retour
            if($result){

                //$result contient un objet stdclass 
                $posterUrl = $result->Poster;

                $output->writeln([
                $posterUrl,
                '**************',
                '',
                 ]);

                $movie->setImageUrl($posterUrl);
            }     
        }

        $em->flush();

        $output->writeln([

            'Finish !',
            '============',
            '',
        ]);
   
    }

    private function getCurl($title){
        //si erreur curl_init : sudo apt-get install php-curl
        //Exemple : http://www.omdbapi.com/?t=andre&apikey=55429286
        //Encoder une url : http://php.net/manual/fr/function.urlencode.php
        // url encode permet de pallier au probleme d'espace et accents qui pourait corrompre l'url ex  /movie/show/The%20Jungle%20Book pour The Jungle Book 
        
        $dbmovieApiUrl = 'http://www.omdbapi.com/?t='.urlencode($title).'&apikey=55429286';

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $dbmovieApiUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //demande le retour de l'appel
        
        $jsonResponse = curl_exec($curl);

        $response = json_decode($jsonResponse);

        curl_close($curl);

        if(isset($response->Response) && $response->Response == "False"){
            $response = null;
        }
       
        return $response;
    }
}