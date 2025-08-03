<?php

namespace App\Form;

use App\Entity\TaskSchedule;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class TaskScheduleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var TaskSchedule|null $taskSchedule */
        $taskSchedule = $options['data'] ?? null;
        $pageName = null;
        if ($taskSchedule->getFacebookPage()) { $pageName = $taskSchedule->getFacebookPage(); }
        elseif ($taskSchedule->getInstagramPage()) { $pageName = $taskSchedule->getInstagramPage(); }

        $repeat = null;
        if ($taskSchedule->getRepeatEvery()) {
            $repeatEvery = $taskSchedule->getRepeatEvery();
            $repeat = $repeatEvery->d ? $repeatEvery->d : null;
        }

        $builder
            ->add('name')
            ->add('nextRunAt', DateTimeType::class, [
                'model_timezone' => 'Europe/Berlin',
                'view_timezone' => 'Europe/Berlin',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please select both date and time.'
                    ])
                ]
            ])
            ->add('repeatEvery', IntegerType::class, [
                'mapped' => false,
                'label' => 'Repeat every (days):',
                'data' => $repeat
            ])
            ->add('targetPlatform', ChoiceType::class, [
                'multiple' => false,
                'expanded' => true,
                'choices' => [
                    'Facebook' => 'facebook',
                    'Instagram' => 'instagram'
                ],
                'required' => true,
            ])
            ->add('pageName', TextType::class, [
                'data' => $pageName,
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TaskSchedule::class,
        ]);
    }
}
