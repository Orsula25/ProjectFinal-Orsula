<?php

namespace App\Form;

use App\Entity\Achat;
use App\Entity\Fournisseur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType as DoctrineEntityType;



class AchatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            // les champs propres à Achat
            ->add('dateAchat', DateTimeType::class, [
                'widget' => 'single_text',
                'html5' => true,
            ])
            ->add('etat', ChoiceType::class, [
                'choices' => [
                    'En cours' => 'En cours',
                    'Terminé' => 'Terminé',
                ],
                'label' => 'État',
            ])
            ->add('fournisseur', DoctrineEntityType::class, [
                'class' => Fournisseur::class,
                'choice_label' => 'nom',
                'label' => 'Fournisseur',
            ])
            ->add('detailAchats', CollectionType::class, [
            'entry_type'    => DetailAchatType::class,
            'entry_options' => [
                'label' => false,      // ← enlève le "0", "1", "2", ...
            ],
            'allow_add'     => true,
            'allow_delete'  => true,
            'by_reference'  => false,
            'label'         => false,  // pas de label pour la collection elle-même
        ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Achat::class,
        ]);
    }



}
