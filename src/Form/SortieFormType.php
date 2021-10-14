<?php

namespace App\Form;


use App\Entity\Sortie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('dateHeureDebut',DateTimeType::class, [
                'widget' => 'single_text'
            ])
            ->add('duree', null, [
                'attr' => ['min' => 0]
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('nbInscriptionsMax', null, [
                'attr' => ['min' => 0]
            ])
            ->add('infosSortie')
            /*->add('etat', EntityType::class,[
                'label'=>'etat : ',
                'class'=>Etat::class,
                'choice_label'=>'libelle',
                'expanded'=>true,
                "multiple"=>false
            ])*/
            ->add('lieu',null,[
                "choice_label"=>"nom",
                "multiple"=>false
            ])
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
