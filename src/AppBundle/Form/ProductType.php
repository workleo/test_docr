<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Product;
use AppBundle\Form\Type\TagsInputType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProductType  extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'attr' => ['autofocus' => true],
                'label' => 'Product Name',
            ])
            ->add('price', null,[
                'label' => 'Product Price',
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['rows' => 3],
                'label' => 'Product Description',
            ])

            ->add('tags', TagsInputType::class, [
                'label' => 'Tags',
                'required' => false,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}