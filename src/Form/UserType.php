<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("firstname", TextType::class, [
                "label" => "user.form.label.firstname"
            ])
            ->add("lastname", TextType::class, [
                "label" => "user.form.label.lastname"
            ])
            ->add("username", TextType::class, [
                "label" => "user.form.label.username"
            ])
            ->add("email", EmailType::class, [
                "label" => "user.form.label.email"
            ])
            ->add("password", RepeatedType::class, [
                "type" => PasswordType::class,
                'invalid_message' => 'user.form.password.does_not_coincide',
                'options' => ['attr' => ['class' => 'password-field']],
                'first_options' => ['label' => 'user.form.label.password'],
                'second_options' => ['label' => 'user.form.label.repeatPassword'],
            ])
        ->add("submit", SubmitType::class, [
            "label" => "global.save"
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "translation_domain" => "BlogTrans",
            'data_class' => User::class,
        ]);
    }
}
