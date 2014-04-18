<?php

namespace Innova\SupportBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SupportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options = array ())
    {
        $builder->add('userEmail', 'email',    array ('required' => true));
        $builder->add('subject',   'text',     array ('required' => true));
        $builder->add('content',   'tinymce', array ('required' => true));
    }

    public function getName()
    {
        return 'innova_support';
    }
}