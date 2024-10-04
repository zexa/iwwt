<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class BookFormType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("title", TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Title is required']),
                    new Assert\Length([
                        'max' => 2048,
                        'maxMessage' => 'Title cannot be longer than {{ limit }} characters',
                    ]),
                ],
            ])
            ->add("author", TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Author is required']),
                    new Assert\Length([
                        'max' => 1024,
                        'maxMessage' => 'Author name cannot be longer than {{ limit }} characters',
                    ]),
                ],
            ])
            ->add("isbn", TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'ISBN is required']),
                    new Assert\Regex([
                        'pattern' => '/^(?:\d{9}[\dXx]|\d{13})$/',
                        'message' => 'The ISBN must be valid (ISBN-10 or ISBN-13)',
                    ]),
                ],
            ])
            ->add("publication_date", DateType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\Date(['message' => 'Please enter a valid date']),
                ],
            ])
            ->add("genres", CollectionType::class, [
                'entry_type' => TextType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'prototype_name' => '__name__',
                'by_reference' => false,
                'label' => false,
                'constraints' => [
                    new Assert\Count([
                        'min' => 1,
                        'minMessage' => 'You must specify at least one genre',
                    ]),
                ],
            ])
            ->add('copies', NumberType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Number of copies is required']),
                    new Assert\PositiveOrZero(['message' => 'Number of copies must be zero or positive']),
                ],
            ])
        ;
    }
}
