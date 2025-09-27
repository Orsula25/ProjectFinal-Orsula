<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Type\EntityType;
use Symfony\Component\Form\Extension\Type\IntegerType;
use Symfony\Component\Form\Extension\Type\MoneyType;
use App\Entity\Produit;

class DetailVenteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('produit', EntityType::class, [
                'class' => Produit::class,
                'choice_label' => 'nom',
                'label' => 'Produit',
            ])
            ->add('quantite', IntegerType::class, [
            
            ])
            ->add('prixUnitaire', MoneyType::class, [
            
            ]);
         
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
