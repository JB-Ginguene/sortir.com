<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Sortie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager=$entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('dateHeureDebut', DateTimeType::class,[
                'widget' => 'single_text',
                'label' => 'Date et heure de début',
                /*'html5' => false,
                'attr' => [
                    'value'=> date('d/m/y H:i')
                ],
                'format' => 'dd/MM/y H:i',

                'translation_domain' => 'Default'
            */])
            ->add('dateLimiteInscription', DateTimeType::class,[
                'widget' => 'single_text',
                'label' => 'Date limite d\'inscription',
            ])
            ->add('nbInscriptionsMax', null,[
                'label'=>'Nombre d\'inscriptions maximum'
                ])
            ->add('duree',null,[
                'label'=>'Durée'
            ])
            ->add('infosSortie',TextareaType::class,[
                'label' => 'Informations (15 caractères minimum)'
            ])
            //->add('urlPhoto')
            ->add('site', EntityType::class,[
                'class'=>Site::class,
                'choice_label'=>'nom',
                'disabled'=>true

            ])//$builder->add('dueDate', 'text', array('read_only' => true));
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
