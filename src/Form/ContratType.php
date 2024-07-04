<?php
namespace App\Form;

use App\Entity\Client;
use App\Entity\Contrat;
use App\Entity\Produit;
use App\Entity\User;
use Doctrine\DBAL\Types\DateType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType as TypeDateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContratType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('nom', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label'],
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 3, 'max' => 255]),
                ],
                'required' => true,
            ])
            ->add('entreprise', null, [
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label'],
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 3, 'max' => 255]),
                ],
                'required' => true,
            ])
            ->add('date_debut', TypeDateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
                'label' => 'Date debut',
                'label_attr' => ['class' => 'form-label'],
                'required' => true,
            ])
            ->add('date_fin', TypeDateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
                'label' => 'Date fin',
                'label_attr' => ['class' => 'form-label'],
                'required' => true,
            ])
            
            ->add('produit', EntityType::class, [
                'class' => Produit::class,
                'choice_label' => 'nom',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('brochure', FileType::class, [
                'label' => 'Contrat (PDF file)',
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
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contrat::class,
        ]);
    }
}
