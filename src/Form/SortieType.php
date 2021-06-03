<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('dateHeureDebut', DateTimeType::class,[
                'date_widget' => 'choice'
            ])
            ->add('dateLimiteInscription')
            ->add('nbInscriptionsMax')
            ->add('duree')
            ->add('infosSortie')
            //->add('urlPhoto')
            ->add('site', EntityType::class,[
                'class'=>Site::class,
                'choice_label'=>'nom'
            ])
            ->add('lieu',EntityType::class,[
                'class'        => Lieu::class,
                'choice_label' => 'nom'
            ])
            ->add('enregistrer',SubmitType::class, [
                'label'=>'Enregistrer'
            ])
            ->add('publier',SubmitType::class,[
                'label'=>'Publier'
            ])->add('annuler', SubmitType::class,[
                'label'=>'Annuler'
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
