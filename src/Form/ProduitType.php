<?php

namespace App\Form;

use App\Entity\CategorieProduit;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom' , TextType::class)
            ->add('description', TextType::class)
            ->add('prixUnitaire', NumberType::class)
            ->add('quantiteStock', NumberType::class)
            ->add('reference', TextType::class)
            ->add('dateCreation', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('dateModification', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('tva' , NumberType::class)
            ->add('categorieProduit', EntityType::class, [
                'class' => CategorieProduit::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
