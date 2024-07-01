<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Contrat;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom', TextType::class, [
            'attr' => ['class' => 'form-control'],
            'required' => true, // Adding required attribute
            
        ])
        ->add('date_debut', DateType::class, [
            'widget' => 'single_text',
            'attr' => ['class' => 'form-control'],
            'required' => true, // Adding required attribute
        ])
        ->add('date_fin', DateType::class, [
            'widget' => 'single_text',
            'attr' => ['class' => 'form-control'],
            'required' => true, // Adding required attribute
        ])
        // ->add('contrat', EntityType::class, [
        //     'class' => Contrat::class,
        //     'choice_label' => 'nom',
        //     'multiple' => true,
        //     'expanded' => true,
        //     'attr' => ['class' => 'form-control'],
        // ])
        ->add('client', EntityType::class, [
            'class' => Client::class,
            'choice_label' => 'nom',
            'attr' => ['class' => 'form-control'],
            'required' => true, // Adding required attribute
        ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
