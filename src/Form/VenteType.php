<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Utilisateur;
use App\Entity\Vente;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Type\CollectionType;
use App\Form\DetailVenteType;

class VenteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('dateVente')
        ->add('etat')
        ->add('client')
        ->add('detailVentes',CollectionType::class, [
            'entry_type' => DetailVenteType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vente::class,
        ]);
    }
}
