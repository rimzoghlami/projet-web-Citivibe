<?php

namespace App\Form;

use App\Entity\Evenement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class EvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
           //->add('ide')
            ->add('nome')
            ->add('categoriee', ChoiceType::class, [
                'label' => 'Catégorie',
                'choices' => [
                    'Sport' => 'Sport',
                    'Art' => 'Art',
                    'Education' => 'Education',
                    'Loisir' => 'Loisir',
                    'Comunauté' => 'Comunauté',
                    'Environnement' => 'Environnement',
                    'Education' => 'Education',
                ],
                'placeholder' => 'Choisir une catégorie', // Optionnel : affiche un libellé par défaut
                // Autres options
            ])          
            ->add('date')
            ->add('heure')
            ->add('page')
            ->add('description')
            ->add('nbrplaces')
            ->add('photo', FileType::class, [
                'label' => 'Image', // Set label for the file input
                'mapped' => false, // This field is not mapped to any property of your entity
                'required' => false, // Not required, since image upload is optional
                'attr' => [
                    'accept' => 'image/*', // Specify accepted file types (images)
                    // Use JavaScript to display the selected file name in a separate label
                    'onchange' => 'document.getElementById("image-file-name").textContent = this.files[0].name;',
                ],])
            ->add('latitude')
            ->add('longitude')
            ->add('page')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,
        ]);
    }
}
