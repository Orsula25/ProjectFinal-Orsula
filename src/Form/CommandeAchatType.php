<?php

namespace App\Form;

use App\Entity\CommandeAchat;
use App\Entity\Fournisseur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeAchatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reference', TextType::class, [
                'label' => 'Référence (laisser vide pour auto)',
                'required' => false,
            ])
            ->add('fournisseur', EntityType::class, [
                'class' => Fournisseur::class,
                'label' => 'Fournisseur',
                'choice_label' => 'nom',
            ])
            ->add('lignesCommande', CollectionType::class, [
                'entry_type' => LigneCommandeType::class,
                'label' => 'Produits',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CommandeAchat::class,
        ]);
    }
}
