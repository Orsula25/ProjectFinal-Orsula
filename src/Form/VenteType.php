<?php

namespace App\Form;

use App\Entity\Vente;
use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\DetailVenteType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType as DoctrineEntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class VenteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // les champs propres à Vente
            ->add('dateVente', DateTimeType::class, [
                'widget' => 'single_text',
                'html5' => true,
            ])
            ->add('client', DoctrineEntityType::class, [
                'class' => Client::class,
                'choice_label' => 'nom',
                'label' => 'Client',
            ])
            ->add('detailVentes', CollectionType::class, [
                'entry_type' => DetailVenteType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vente::class,
        ]);
    }
}
