<?php

namespace App\Form;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\DBAL\Types\TextType;
use PHPUnit\TextUI\XmlConfiguration\Logging\Text;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DomCrawler\Field\TextareaFormField;
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
