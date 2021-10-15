<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModifierProfilFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo')
            ->add('prenom')
            ->add('nom')
            ->add('telephone')
            ->add('email')
            ->add('Campus', null,[
                "choice_label"=>"nom"
            ])
            ->add('image',null,[
                'label'=>'Image : '
                ])
            //->add('roles')
           // ->add('password')
            //->add('administrateur')
            //->add('actif')
            //->add('estInscrit')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
