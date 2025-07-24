<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = $options['is_edit'] ?? false;
        $builder
            ->add('username', TextType::class, [
                'label_attr' => ['class' => 'form-label text-center', 'for' => 'username'],
                'attr' => ['class' => 'form-control']
            ])
            ->add('facebookIdentifier', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length(min: 3)
                    ],
                'label_attr' => ['class' => 'form-label text-center', 'for' => 'username'],
                'attr' => ['class' => 'form-control']
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Marketing' => 'ROLE_MARKETING',
                ],
                'multiple' => true,   // Because roles is an array
                'expanded' => true,   // Optional: shows checkboxes instead of a multiselect
                'label' => 'Roles',
            ])
            ->add('password', PasswordType::class, [
                'mapped' => $isEdit ? false : true,
                'required' => !$isEdit,
                'label' => $isEdit
                    ? 'New Password (leave blank to keep current)'
                    : 'Password',
                'label_attr' => ['class' => 'form-label text-center', 'for' => 'password'],
                'attr' => ['class' => 'form-control']
            ])
            // ->add('submit_button', SubmitType::class, [
            //     'attr' => ['class' => 'btn btn-lg btn-primary'],
            //     'label' => $isEdit ? 'Update' : 'Create'
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_edit' => false,
        ]);
    }
}
