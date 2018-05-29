<?php

namespace AppBundle\Form;


use Symfony\Component\Form\FormEvent;
//import de la classe FormEvent pour recuperer l'event souhaité
use Symfony\Component\Form\FormEvents;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /*
         Deux solutions si pas connaissances des event:
         
         1 - creer un deuxieme formulaire qui n'aurait pas le pwd obligatoire pour la modification
         2 - conditionner le champs password au passage via les $option par ex sur la methode appelante
        */

        //l'objet event contient un objet form qui contient lui meme les données user associée
        $listener = function (FormEvent $event) {
            $userData = $event->getData();
            $userForm = $event->getForm();

            if($userData->getId()){

                //si mon user est créé j'ai un id mon champs est optionnel

                $userForm->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'invalid_message' => 'Les mots de passe ne sont pas identiques',
                    'options' => ['attr' => ['class' => 'password-field']],
                    'first_options' => [
                        'label' => 'Password',
                        'attr' => [
                            'placeholder' => 'Laisser vide si inchangé'
                        ]],
                    'second_options' => [
                        'label' => 'Vérification du mot de passe',
                        'attr' => [
                            'placeholder' => 'Laisser vide si inchangé'
                        ]],
                ]);
            } else {

                //sinon mon user est nouveau , mon champs est obligatoire

                $userForm->add('password', RepeatedType::class, array(
                    'type' => PasswordType::class,
                    'constraints' => [new NotBlank()],
                    'invalid_message' => 'Les mots de passe ne sont pas identiques',
                    'options' => array('attr' => array('class' => 'password-field')),
                    //'required' => true,
                    'first_options' => array('label' => 'Password'),
                    'second_options' => array('label' => 'Vérification du mot de passe'),
                ));
            }
        };

        $builder
        ->add('username')
        ->addEventListener(FormEvents::PRE_SET_DATA, $listener)
        ->add('email')
        ->add('isActive')
        ->add('role');
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
            'attr' => ['novalidate' => 'novalidate']
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_user';
    }


}
