<?php


namespace App\Form;


use App\Entity\UserAccount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserAccountType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {

        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\UserAccount'
        ));
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('balance');
        $builder->add('status',ChoiceType::class, array(
            'choices'  => array(
                'Active' => UserAccount::STATUS_ACTIVE,
                'Banned' => UserAccount::STATUS_BANNED,
            )
        ));
        $builder->add('createdAt');

    }
}