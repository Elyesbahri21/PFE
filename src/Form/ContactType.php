<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', null, [
                'attr' => ['class' => 'form-control'] // Add CSS class to email field
            ])
            ->add('content', TextareaType::class, [
                'attr' => ['class' => 'form-control'] // Add CSS class to content field
            ])
            ->add('envoyer', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary'] // Add CSS class to submit button
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
