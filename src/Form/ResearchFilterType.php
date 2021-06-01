<?php

namespace App\Form;

use App\Entity\Site;
use App\ResearchFilter\ResearchFilter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResearchFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('site', EntityType::class, [
                'required' => false,
                'class' => Site::class,
                'choice_label' => 'nom'
            ])
            ->add('nomSortie', TextType::class, [
                'required' => false,
                'label' => 'Nom'
            ])
            ->add('dateMin', DateType::class, [
                'required' => false,
                'html5' => true,
                'widget' => 'single_text',
                'label' => 'Date minimum'
            ])
            ->add('dateMax', DateType::class, [
                'required' => false,
                'html5' => true,
                'widget' => 'single_text',
                'label' => 'Date maximum'
            ])
            ->add('specificitees', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    "Sorties dont je suis l'organisateur·trice" => 'organisateur',
                    "Sorties auxquelles je suis inscrit·e" => 'inscrit',
                    "Sorties auxquelles je ne suis pas inscrit·e" => 'noninscrit',
                    "Sorties passées" => 'sortiespassees'

                ],
                'multiple' => true,
                'expanded' => true,
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ResearchFilter::class
        ]);
    }
}
