<?php

namespace App\Form;

use App\Entity\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'required' => true,
                'attr'=>['autocomplete'=>'off']
            ])
            ->add('lastName', TextType::class, [
                'required' => true,
                'attr'=>['autocomplete'=>'off']
            ])
            ->add('gender', ChoiceType::class, [
                'required' => true,
                'choices' => [
                    'Male' => 0,
                    'Female' => 1,
                    'Not sure' => 2
                ],
                'placeholder' => "Please select your gender"
            ])
            ->add('age', IntegerType::class, [
                'required' => true,
                'attr'=>['autocomplete'=>'off']
            ])
            ->add('profilePicture', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'application/jpeg',
                            'application/jpg',
                            'application/png'
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image document',
                    ])
                ],
            ])
            ->add('submit', SubmitType::class,[
                'attr'=>['class'=>'btn btn-success mt-3']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
