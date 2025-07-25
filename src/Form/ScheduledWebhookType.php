<?php

namespace App\Form;

use App\Entity\WebhookSchedule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScheduledWebhookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cronExpression')
            ->add('webhookUrl')
            ->add('jsonData');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WebhookSchedule::class,
        ]);
    }
}
