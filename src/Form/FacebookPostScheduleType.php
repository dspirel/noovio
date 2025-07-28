<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class FacebookPostScheduleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('facebook_page', TextType::class,
            [
                'label_attr' => ['class' => 'form-label text-center', 'for' => 'facebook_page'],
                'attr' => ['class' => 'form-control']
            ])
            ->add('ai_prompt', TextType::class,
            [
                'label_attr' => ['class' => 'form-label text-center', 'for' => 'ai_prompt'],
                'attr' => ['class' => 'form-control']
            ])
            ->add('images', ChoiceType::class, [
                'choices' => array_column($options['images'], 'name', 'name'), // ['img1.jpg' => 'img1.jpg']
                'multiple' => true,
                'expanded' => true,
                'label' => 'Select Images',
                'choice_attr' => function ($choice, $key, $value) use ($options) {
                    $url = '';
                    foreach ($options['images'] as $image) {
                        if ($image['name'] === $value) {
                            $url = $image['url'];
                            break;
                        }
                    }
                    return [
                        'data-image-url' => $url,
                        'class' => 'image-checkbox'
                        ];
                    }
                ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'images' => [],
            'allow_extra_fields' => true
        ]);
    }
}
