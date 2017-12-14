<?php
/**
 * Plugin Class File
 *
 * Created:   December 14, 2017
 *
 * @package:  Modern Framework for Wordpress
 * @author:   Kevin Carwile
 * @since:    {build_version}
 */
namespace Modern\Wordpress\Helpers\Form\SymfonyForm;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access denied.' );
}

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Modern\Wordpress\Helpers\Form\SymfonyForm;

/**
 * FieldsetType Class
 */
class FieldsetType extends AbstractType
{
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'legend' => '',
                'inherit_data' => true,
                'options' => array(),
                'fields' => array(),
                'label' => false,
            ])
            ->addAllowedTypes('fields', ['array', 'callable']);
    }
	
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!empty($options['fields'])) {
            if (is_callable($options['fields'])) {
                call_user_func($options['fields'], $builder);
            } elseif (is_array($options['fields'])) {
                foreach ($options['fields'] as $field) {
					$builder->add($field['name'], SymfonyForm::getFieldClass( $field['type'] ), $field['options']);
                }
            }
        }
    }
	
    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (false !== $options['legend']) {
            $view->vars['legend'] = $options['legend'];
        }
    }
	
    /**
     * @return string
     */
    public function getName()
    {
        return 'fieldset';
    }
}