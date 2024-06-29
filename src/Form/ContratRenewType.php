<?php

// src/Form/ContratRenewType.php

namespace App\Form;

use App\Entity\Contrat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContratRenewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date_debut', DateType::class, [
                'label' => 'Nouvelle date debut',
                'widget' => 'single_text',
            ])
            ->add('date_fin', DateType::class, [
                'label' => 'Nouvelle date fin',
                'widget' => 'single_text',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contrat::class,
        ]);
    }
}
