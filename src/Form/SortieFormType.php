<?php

namespace App\Form;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('dateHeureDebut')
            ->add('duree')
            ->add('dateLimiteInscription')
            ->add('nbInscriptionsMax')
            ->add('infosSortie')
            /*->add('etat', EntityType::class,[
                'label'=>'etat : ',
                'class'=>Etat::class,
                'choice_label'=>'libelle',
                'expanded'=>true,
                "multiple"=>false
            ])*/
            ->add('lieu')
            //->add('campus',EntityType::class)
            //->add('participants')
            //->add('organisateur') b
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
