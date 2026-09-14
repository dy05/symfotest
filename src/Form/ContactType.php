<?php

namespace App\Form;

use App\Data\ContactData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
//use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('email', EmailType::class)
            ->add('phone', TelType::class)
            ->add('message', TextareaType::class)
            ->add('rgpd', CheckboxType::class)
//            ->add('name', TextType::class, ['label' => 'Your name'])
//            ->add('email', EmailType::class, ['label' => 'Your email'])
//            ->add('phone', TelType::class, ['label' => 'Phone number'])
//            ->add('message', TextareaType::class, ['label' => 'Message', 'attr' => ['rows' => 6]])
//            ->add('rgpd', CheckboxType::class, [
//                'label' => 'I agree that my data will be used to reply to my request.',
//            ])
//            ->add('submit', SubmitType::class, ['label' => 'Send message'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactData::class,
//            'csrf_protection' => false,
        ]);
    }
}
