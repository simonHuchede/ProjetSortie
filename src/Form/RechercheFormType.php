<?php

namespace App\Form;

use App\Entity\Campus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RechercheFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add( 'campus',
                EntityType::class,
                [
                    'mapped' => false,
                    'class' => Campus::class,
                    'choice_label' => 'nom',
                ])
            ->add('search',null,[
                "label"=>"le nom de la sortie contient : "
            ])
            ->add('start', DateType::class, [
                'widget' => 'choice',
            ])
            ->add('end', DateType::class, [
                'widget' => 'choice',
            ])
            ->add('organisateur',CheckboxType::class,[
                "label"=>"Sorties dont je suis l'organisateur/trice",
                "required"=>false
            ])
            ->add('inscrit',CheckboxType::class,[
                "label"=>"Sorties auxquelles je suis inscrit/e",
                "required"=>false
            ])
            ->add('nonInscrit',CheckboxType::class,[
                "label"=>"Sorties auxquelles je ne suis pas inscrit/e",
                "required"=>false
            ])
            ->add('passees',CheckboxType::class,[
                "label"=>"Sorties passées",
                "required"=>false
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
