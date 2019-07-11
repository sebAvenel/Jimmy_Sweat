<?php

namespace App\Form;

use App\Entity\Video;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', TextareaType::class ,
                ['attr' =>
                    [
                        'rows' => 7,
                        'placeholder' => 'Intégrez le code YouTube, Exemple:
    <iframe width="560" height="315" 
        src="https://www.youtube.com/embed/XXxxXxXXXxXxx" 
        frameborder="0" allow="accelerometer;
        autoplay; encrypted-media; gyroscope; 
        picture-in-picture" allowfullscreen>
    </iframe>'
                    ], 'label' => false, 'required' => false
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
        ]);
    }
}
