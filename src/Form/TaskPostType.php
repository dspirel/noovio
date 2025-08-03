<?php

namespace App\Form;

use App\Entity\TaskPost;
use App\Entity\TaskSchedule;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class TaskPostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,
            [
                'label_attr' => ['class' => 'form-label text-center', 'for' => 'name'],
                'attr' => ['class' => 'form-control']
            ])
            ->add('title', TextType::class,
            [
                'label_attr' => ['class' => 'form-label text-center', 'for' => 'facebook_page'],
                'attr' => ['class' => 'form-control']
            ])
            ->add('postText', TextType::class,
            [
                'label_attr' => ['class' => 'form-label text-center', 'for' => 'ai_prompt'],
                'attr' => ['class' => 'form-control']
            ])
            ->add('mediaUrls', ChoiceType::class, [
                // 'mapped' => false,
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
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TaskPost::class,
            'images' => [],
            'allow_extra_fields' => true
        ]);
    }
}
