<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Movie;
use AppBundle\Service\Slugger;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Movie controller.
 *
 * @Route("/admin/movie")
 */
class MovieController extends Controller
{
    /**
     * Lists all movie entities.
     *
     * @Route("/", name="movie_index")
     * @Method("GET")
     */
    public function indexAction()
    {  
        $em = $this->getDoctrine()->getManager();

        $movies = $em->getRepository('AppBundle:Movie')->findAll();

        return $this->render('admin/movie/index.html.twig', array(
            'movies' => $movies,
        ));
    }

    /**
     * Creates a new movie entity.
     *
     * @Route("/new", name="movie_new")
     * @Method({"GET", "POST"})
     * 
     */
    public function newAction(Request $request, Slugger $slugger)
    {
        $movie = new Movie();
        $form = $this->createForm('AppBundle\Form\MovieType', $movie);
        $form->handleRequest($request);
       
        if ($form->isSubmitted() && $form->isValid()) {
             
            //je recupere mon fichier qui est du type uploadFile
            $file = $movie->getImage();

            if($file){
                //je genere un nom de fichier unique avec l'extension fournie au depart pour eviter qu'il soit ecrasé à l'import
                $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
                
                //je deplace le fichier dans le repertoire souhaité ici definit dans config.yml sous posters_directory
                $file->move(
                    //recupere parametre setté en constante dans un fichier yaml 
                    $this->getParameter('posters_directory'),
                    $fileName
                );

                /* 
                movie prend un objet file au premier envoit par le formulaire mais pour l'enregistrement
                definitif , il faut renvoyer une chaine de caractere comprenant le nom du fichier
                */
                $movie->setImage($fileName);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($movie);
            $em->flush();

            return $this->redirectToRoute('admin_movie_show', array('id' => $movie->getId()));
        }

        return $this->render('admin/movie/new.html.twig', array(
            'movie' => $movie,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a movie entity.
     *
     * @Route("/{id}", name="admin_movie_show")
     * @Method("GET")
     */
    public function showAction(Movie $movie)
    {
        $deleteForm = $this->createDeleteForm($movie);

        return $this->render('admin/movie/show.html.twig', array(
            'movie' => $movie,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing movie entity.
     *
     * @Route("/{id}/edit", name="movie_edit")
     * @Method({"GET", "POST"})
     * 
     * 2. bis
     */
    public function editAction(Request $request, Movie $movie, Slugger $slugger)
    {   
        /* 
            On memorise le nom de l'ancienne image si l'on souhaite uniquement modifier une autre donnée
            ce qui nous permet de ne pas remettre notre image à null (valeur par defaut)
        */
  
        $deleteForm = $this->createDeleteForm($movie);
        $editForm = $this->createForm('AppBundle\Form\MovieType', $movie);

        $currentImage = $movie->getImage();

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            
            //je recupere mon fichier qui est du type uploadFile
            $newImage = $movie->getImage();
      
            if($newImage){
                //je genere un nom de fichier unique avec l'extension fournie au depart pour eviter qu'il soit ecrasé à l'import
                $fileName = $this->generateUniqueFileName().'.'.$newImage->guessExtension();
                
                //je deplace le fichier dans le repertoire souhaité ici definit dans config.yml sous posters_directory
                $file->move(
                    //recupere parametre setté en constante dans un fichier yaml 
                    $this->getParameter('posters_directory'),
                    $fileName
                );

                $movie->setImage($fileName);
            } else {
                $movie->setImage($currentImage);
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('movie_index', array('id' => $movie->getId()));
        }

        return $this->render('admin/movie/edit.html.twig', array(
            'movie' => $movie,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a movie entity.
     *
     * @Route("/{id}", name="movie_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Movie $movie)
    {
        $form = $this->createDeleteForm($movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($movie);
            $em->flush();
        }

        return $this->redirectToRoute('movie_index');
    }

    /**
     * Creates a form to delete a movie entity.
     *
     * @param Movie $movie The movie entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Movie $movie)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('movie_delete', array('id' => $movie->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }
}
