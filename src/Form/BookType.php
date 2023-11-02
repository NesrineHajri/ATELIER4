<?php

namespace App\Form;
use App\Entity\Author;
use App\Entity\Book; 

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ref')
            ->add('title')
            ->add('publicationDate', DateType::class, [
               
            ])
            ->add('category', ChoiceType::class, [
                'choices' => [
                    'Science-Fiction' => 'Science-Fiction',
                    'Mystery' => 'Mystery',
                    'Autobiography' => 'Autobiography',
                ],
            ])
            ->add('author', EntityType::class, [
                'class' => Author::class, // Assuming your Author entity class is named Author
                'choice_label' => 'username', // Display the "username" property as the choice label
                'multiple' => false, // Allow selecting only one author
                'expanded' => false, // Display as a select box
            ])

            ->add('save', SubmitType::class);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
