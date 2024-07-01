<?php
namespace App\Form;

use App\Entity\Visite;
use App\Entity\Contrat;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class VisiteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateTimeType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'], 
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Préventive' => 'Préventive',
                    'Curative' => 'Curative',
                    'Évolutive' => 'Évolutive',
                ],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'form-control'], 
            ])
            // ->add('pv', TextType::class, [
            //     'attr' => ['class' => 'form-control'], 
            // ])
            ->add('pv', FileType::class, [
                'label' => 'Pv de visite (PDF file)',
                'attr' => ['class' => 'form-control-file'],
                'label_attr' => ['class' => 'form-label'],
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF document',
                    ])
                ],
            ])
        
            ->add('contrat', EntityType::class, [
                'class' => Contrat::class,
                'choice_label' => 'nom',
                'attr' => ['class' => 'form-control'], // Bootstrap class for form-control
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Visite::class,
        ]);
    }
}
