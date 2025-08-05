<?php

namespace App\Form;

use App\Entity\TaskPost;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddPostsToScheduleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('taskPosts', EntityType::class, [
                'class' => TaskPost::class,
                'multiple' => true,
                'expanded' => true, // this makes it render as checkboxes
                'choices' => $options['available_posts'],
                'choice_label' => function (TaskPost $post) {
                    // You can customize the label however you want
                    return sprintf(
                        'Post #%d: "%s" by %s',
                        $post->getId(),
                        $post->getTitle(),
                        $post->getName(),
                    );
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'available_posts' => [],
        ]);
    }
}

