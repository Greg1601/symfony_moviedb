<?php
namespace AppBundle\Entity;

use AppBundle\Service\Slugger;
use AppBundle\Entity\MovieCast;
use Doctrine\ORM\Mapping as ORM;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Movie
 *
 * @ORM\Table(name="movie")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MovieRepository")
 * 
 * Cette annotation active les events
 * @ORM\HasLifecycleCallbacks()
 */
class Movie
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

     /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", nullable=true)
     * @Assert\File(mimeTypes={ "image/gif", "image/jpeg", "image/png" })
     */
    private $image;

      /**
      *
      * @ORM\Column(name="imageUrl", type="string", nullable=true)
     */
    private $imageUrl;

    /**
     * @ORM\ManyToMany(targetEntity="Genre", inversedBy="movies", cascade={"persist"})
     */
    private $genres;

    /**
     * @ORM\OneToMany(targetEntity="MovieCast", mappedBy="movie")
     */
    private $moviecasts;

    public function __construct()
    {
        $this->genres = new ArrayCollection();
        $this->moviecasts = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Movie
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    

    public function addGenre(Genre $genre)
    {
        // On informe l'objet $person que cet objet movie lui est associé
        $genre->addMovie($this);
        // On ajoute l'objet $genre à la liste
        $this->genres[] = $genre;
    }

    /**
     * Get the value of moviecasts
     */ 
    public function getMovieCasts()
    {
        return $this->moviecasts;
    }

    /**
     * Ajoute un moviecast a l'array collection $moviecats
     * sert pour l'affichage et l'enregistrement
     * 
     * @param MovieCast $moviecast
     * @return Movie
     */
    public function addMovieCast(MovieCast $moviecast){
        $this->moviecasts[] = $moviecast;

        return $this;
    }

    /**
     * Get the value of genres
     */ 
    public function getGenres()
    {
        return $this->genres;
    }

    /**
     * Get the value of slug
     *
     * @return  string
     */ 
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set the value of slug
     *
     * @param  string  $slug
     *
     * @return  self
     */ 
    public function setSlug(string $slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get the value of image
     *
     */ 
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set the value of image
     */ 
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * indique les events sur lequel applySlug va etre effectué
     * 
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */ 
    public function applySlug(){

        //$slugger = new Slugger();

        //$sluggedTitle = $slugger->slugify($this->getTitle());

        //$this->setSlug($sluggedTitle);
    }

    /*
     * Renvoit la representation chaine de charactere de l'objet si demandé
     * grace a __toString()
     */

    public function __toString(){
        return $this->title;      
    }

    /**
     * Get the value of imageUrl
     */ 
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * Set the value of imageUrl
     *
     * @return  self
     */ 
    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }
}
