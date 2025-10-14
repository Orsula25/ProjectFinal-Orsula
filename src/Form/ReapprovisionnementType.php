<?php
namespace App\Form;

use App\Entity\Fournisseur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ReapprovisionnementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $hasRestrictedChoices = is_array($options['fournisseur_choices']) && count($options['fournisseur_choices']) > 0;

        $builder
            ->add('fournisseur', EntityType::class, [
                'class' => Fournisseur::class,
                'choice_label' => 'nom',
                'choices' => $hasRestrictedChoices ? $options['fournisseur_choices'] : null,
                'placeholder' => $hasRestrictedChoices ? 'Choisir un fournisseur lié' : 'Choisir un fournisseur',
                'required' => true,
            ])
            ->add('quantite', IntegerType::class, [
                'label' => 'Quantité',
                'attr' => ['min' => 1],
                'constraints' => [
                    new Assert\NotBlank(message: 'Indique une quantité.'),
                    new Assert\GreaterThan(value: 0, message: 'La quantité doit être > 0.'),
                ],
                'invalid_message' => 'Saisis un entier valide.',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'fournisseur_choices' => null,
        ]);
    }
}
