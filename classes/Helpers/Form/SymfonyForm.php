<?php
/**
 * Plugin Class File
 *
 * Created:   January 25, 2017
 *
 * @package:  Modern Framework for Wordpress
 * @author:   Kevin Carwile
 * @since:    1.1.4
 */
namespace Modern\Wordpress\Helpers\Form;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access denied.' );
}

use Modern\Wordpress\Framework;
use Modern\Wordpress\Symfony;
use Modern\Wordpress\Helpers\Form;

/**
 * Form Class
 */
class SymfonyForm extends Form
{	
	/**
	 * @var 	\Modern\Wordpress\Plugin		Provides access to the plugin instance
	 */
	protected $plugin;
	
	/**
	 * @var	string
	 */
	public $name;
	
	/**
	 * @var	string
	 */
	public $method = "POST";
	
	/**
	 * @var	string
	 */
	public $action = "";
	
	/**
	 * @var submitButton
	 */
	public $submitButton = "Save";
	
	/**
	 * @var	string		Output themes
	 */
	public $themes = array();
	
	/** 
	 * @var	mixed
	 */
	public $data;
	 
	/**
	 * @var	array
	 */
	public $options = array
	(
		'allow_extra_fields' => true,
		'empty_data' => array(),
	);
	
	/**
	 * Set template
	 *
	 * @param	string|array		$themes		The form themes (or themes) to pick templates from
	 * @return	this							Chainable
	 */
	public function setTheme( $themes )
	{
		$themes = (array) $themes;
		$this->themes = $themes;
		
		return $this;
	}
	
	/**
 	 * Get plugin
	 *
	 * @return	\Modern\Wordpress\Plugin
	 */
	public function getPlugin()
	{
		return $this->plugin;
	}
	
	/**
	 * Set plugin
	 *
	 * @return	this			Chainable
	 */
	public function setPlugin( \Modern\Wordpress\Plugin $plugin=NULL )
	{
		$this->plugin = $plugin;
		return $this;
	}
	
	/**
	 * Constructor
	 *
	 * @param	string						$name			The name of the form
	 * @param	Modern\Wordpress\Plugin		$plugin			The plugin to associate this class with, or NULL to auto-associate
	 * @param	object|array				$data			The initial form data state
	 * @param	array						$options		Set options for the form
	 * @return	void
	 */
	public function __construct( $name, \Modern\Wordpress\Plugin $plugin=NULL, $data=null, $options=array() )
	{
		$this->name = $name;
		$this->plugin = $plugin ?: Framework::instance();
		$this->data = $data;
		$this->options = $options;
		
		$engines = array();
		
		if ( isset( $plugin ) )
		{
			$engines[] = new \Modern\Wordpress\Symfony\TemplateEngine( $plugin );
		}
		
		$engines[] = new \Modern\Wordpress\Symfony\TemplateEngine( Framework::instance() );
		$templateEngine = new \Symfony\Component\Templating\DelegatingEngine( $engines );
		
		$this->setTemplateEngine( $templateEngine );
		$this->setEngines( $engines );
	}
	
	/**
	 * Enable or disable csrf protection
	 *
	 * @param	bool			$bool			Either true for ON or false for OFF
	 * @return	this							Chainable
	 */
	public function csrf( $bool )
	{
		$this->options[ 'csrf_protection' ] = $bool;
		
		return $this;
	}
	
	/**
	 * @var		EngineInterface
	 */
	protected $templateEngine;
	
	/**
	 * @var	FormRenderHelper
	 */
	public $renderHelper;
	
	/**
	 * @var	TranslatorHelper
	 */
	public $translatorHelper;
	
	/**
	 * Set the template rendering engine
	 *
	 * @param	EngineInterface			$templateEngine				The template rendering engine
	 * @return	void
	 */
	public function setTemplateEngine( \Symfony\Component\Templating\EngineInterface $templateEngine )
	{
		$this->templateEngine = $templateEngine;
	}
	
	/**
	 * Get template rendering engine
	 *
	 * @return	EngineInterface
	 */
	public function getTemplateEngine()
	{
		return $this->templateEngine;
	}
	
	/** 
	 * @var		FormBuilderInterface
	 */
	protected $formBuilder;
	
	/**
	 * Set the form builder
	 *
	 * @param	FormBuilderInterface		$formBuilder			The form builder
	 * @return	void
	 */
	public function setFormBuilder( \Symfony\Component\Form\FormBuilderInterface $formBuilder )
	{
		$this->formBuilder = $formBuilder;
	}
	
	/** 
	 * Get the form builder
	 *
	 * @return	\Symfony\Component\Form\FormBuilderInterface
	 */
	public function getFormBuilder()
	{
		if ( ! isset( $this->formBuilder ) ) {
			$this->setFormBuilder( Symfony::instance()->getFormFactory()->createNamedBuilder( $this->name, 'Symfony\Component\Form\Extension\Core\Type\FormType', $this->data, $this->options ) );
		}
		
		return $this->formBuilder;
	}
	
	/**
	 * @var	Form
	 */
	protected $handledForm;
	
	/**
	 * Get the form
	 *
	 * @return	Form
	 */
	public function getForm()
	{
		/* Return the most current version of the form until it's been handled */
		if ( $this->handledForm )
		{
			return $this->handledForm;
		}
		
		return $this->getFormBuilder()->getForm();
	}
	
	/**
	 * Set the form
	 *
	 * @param	Form		$form			The form
	 */
	public function setHandledForm( $form )
	{
		$this->handledForm = $form;
	}
	
	/**
	 * @var		array
	 */
	protected $engines = array();
	
	/**
	 * Set the template engines cache
	 *
	 * @param	array		$engines			The form view
	 * @return	void
	 */
	public function setEngines( $engines )
	{
		$this->engines = $engines;
	}
	
	/** 
	 * Get the template engines
	 *
	 * @return	array
	 */
	public function getEngines()
	{
		return $this->engines;
	}
	
	/**
	 * Get the plugin slug for use in hooks
	 *
	 *@return	string
	 */
	public function getPluginSlug()
	{
		return str_replace( '-', '_', $this->getPlugin()->pluginSlug() );
	}
	
	/**
	 * @var	array		Form field class shorthand map
	 */
	public static $formFieldClasses = array(
		'text'         => 'Symfony\Component\Form\Extension\Core\Type\TextType',
		'textarea'     => 'Symfony\Component\Form\Extension\Core\Type\TextareaType',
		'email'        => 'Symfony\Component\Form\Extension\Core\Type\EmailType',
		'integer'      => 'Symfony\Component\Form\Extension\Core\Type\IntegerType',
		'money'        => 'Symfony\Component\Form\Extension\Core\Type\MoneyType',
		'number'       => 'Symfony\Component\Form\Extension\Core\Type\NumberType',
		'password'     => 'Symfony\Component\Form\Extension\Core\Type\PasswordType',
		'percent'      => 'Symfony\Component\Form\Extension\Core\Type\PercentType',
		'search'       => 'Symfony\Component\Form\Extension\Core\Type\SearchType',
		'url'          => 'Symfony\Component\Form\Extension\Core\Type\UrlType',
		'range'        => 'Symfony\Component\Form\Extension\Core\Type\RangeType',
		'choice'       => 'Symfony\Component\Form\Extension\Core\Type\ChoiceType',
		'entity'       => 'Symfony\Component\Form\Extension\Core\Type\EntityType',
		'country'      => 'Symfony\Component\Form\Extension\Core\Type\CountryType',
		'language'     => 'Symfony\Component\Form\Extension\Core\Type\LanguageType',
		'locale'       => 'Symfony\Component\Form\Extension\Core\Type\LocaleType',
		'timezone'     => 'Symfony\Component\Form\Extension\Core\Type\TimezoneType',
		'currency'     => 'Symfony\Component\Form\Extension\Core\Type\CurrencyType',
		'date'         => 'Symfony\Component\Form\Extension\Core\Type\DateType',
		'dateinterval' => 'Symfony\Component\Form\Extension\Core\Type\DateintervalType',
		'datetime'     => 'Symfony\Component\Form\Extension\Core\Type\DatetimeType',
		'time'         => 'Symfony\Component\Form\Extension\Core\Type\TimeType',
		'birthday'     => 'Symfony\Component\Form\Extension\Core\Type\BirthdayType',
		'checkbox'     => 'Symfony\Component\Form\Extension\Core\Type\CheckboxType',
		'file'         => 'Symfony\Component\Form\Extension\Core\Type\FileType',
		'radio'        => 'Symfony\Component\Form\Extension\Core\Type\RadioType',
		'collection'   => 'Symfony\Component\Form\Extension\Core\Type\CollectionType',
		'repeated'     => 'Symfony\Component\Form\Extension\Core\Type\RepeatedType',
		'hidden'       => 'Symfony\Component\Form\Extension\Core\Type\HiddenType',
		'button'       => 'Symfony\Component\Form\Extension\Core\Type\ButtonType',
		'reset'        => 'Symfony\Component\Form\Extension\Core\Type\ResetType',
		'submit'       => 'Symfony\Component\Form\Extension\Core\Type\SubmitType',
		'fieldset'     => 'Modern\Wordpress\Helpers\Form\SymfonyForm\FieldsetType',
		'tab'          => 'Modern\Wordpress\Helpers\Form\SymfonyForm\TabType',
		'html'         => 'Modern\Wordpress\Helpers\Form\SymfonyForm\HtmlType',
	);
	
	/**
	 * @var	array		Added fields
	 */
	protected $fields = array();

	/**
	 * Add a field to the form
	 *
	 * @param	string		$name			The field name
	 * @param	string		$type			The field type (registered shorthand or a class name)
	 * @param	array		$options		The field options
	 * @param	string		$parent_name	The parent field name to add this field to
	 * @return	this						Chainable
	 */
	public function addField( $name, $type='text', $options=array(), $parent_name=NULL )
	{
		$builder = $this->getFormBuilder();
		if ( $parent_name ) {
			try {
				$builder = $builder->get( $parent_name );
			} catch( \InvalidArgumentException $e ) { }
		}
		
		$options = array_merge( array( 'translation_domain' => $this->getPlugin()->pluginSlug() ), $options );	
		$field = $this->applyFilters( 'field', array( 'name' => $name, 'type' => $type, 'options' => $options ) );
		
		if ( empty( $field ) ) {
			return $this;
		}
		
		$field[ 'type' ] = static::getFieldClass( $field['type'] );
		
		$builder->add( $field[ 'name' ], $field[ 'type' ], $field[ 'options' ] );
		$this->fields[ $field[ 'name' ] ] = $field;
		
		return $this;
	}
	
	/**
	 * Get a field type class
	 *
	 * @param	string			$type			Either a class or a shorthand key to lookup in the types array
	 * @return	string
	 */
	public static function getFieldClass( $type )
	{
		if ( isset( static::$formFieldClasses[ $type ] ) ) {
			return static::$formFieldClasses[ $type ];
		}
		
		return apply_filters( 'mwp_form_field_class', $type );
	}
	
	/**
	 * Get the added fields
	 *
	 * @return	array
	 */
	public function getFields()
	{
		return $this->fields;
	}
	
	/**
	 * @var	bool		Request handled
	 */
	public $requestHandled = false;
	
	/**
	 * Handle the request
	 *
	 * @param	Request		$request				The request to handle
	 * @return	void
	 */
	public function handleRequest( \Symfony\Component\HttpFoundation\Request $request=NULL )
	{
		if ( ! isset( $request ) )
		{
			$request = Framework::instance()->getRequest();
		}
		
		$form = $this->getFormBuilder()->getForm();
		$form->handleRequest( $request );
		$this->setHandledForm( $form );
		$this->requestHandled = true;
	}
	
	/**
	 * Check if form was submitted
	 *
	 * @return	bool
	 */
	public function isSubmitted()
	{
		if ( ! $this->requestHandled )
		{
			$this->handleRequest();
		}
		
		return $this->getForm()->isSubmitted();
	}
	
	/**
	 * Check for valid form submission
	 *
	 * @return	bool
	 */
	public function isValidSubmission()
	{	
		return $this->isSubmitted() and $this->getForm()->isValid();
	}
	
	/**
	 * Get the form submission data
	 *
	 * @return	array|false
	 */
	public function getSubmissionData()
	{
		if ( $this->isSubmitted() )
		{
			return $this->getForm()->getData();
		}
		
		return array();
	}
	
	/**
	 * Get submitted form values
	 *
	 * @return	array
	 */
	public function getValues()
	{
		$values = array();
		
		if ( $this->isValidSubmission() )
		{
			$values = $this->applyFilters( 'values', $this->getSubmissionData() ); 
		}
		
		return $values;
	}
	
	/**
	 * Get form submission errors
	 *
	 * @return	array			Fields that had errors
	 */
	public function getErrors()
	{
		$errors = array();
		
		if ( $this->isSubmitted() )
		{
			return $this->applyFilters( 'errors', $this->getForm()->getErrors() );
		}
		
		return $errors;
	}
	
	/**
	 * Get form output
	 *
	 * @return	string
	 */
	public function render()
	{
		$template_vars = $this->applyFilters( 'render', array( 
			'formWrapper' => $this,
			'form' => $this->getForm()->createView(),
		) );
		
		$this->renderHelper = new \Modern\Wordpress\Symfony\FormRenderHelper( 
			new \Symfony\Component\Form\FormRenderer( 
				new \Symfony\Component\Form\Extension\Templating\TemplatingRendererEngine(
					$this->getTemplateEngine(), array_merge( $this->themes, array( 'form/symfony' ) )
				)
			)
		);
		
		$this->translatorHelper = new \Modern\Wordpress\Symfony\TranslatorHelper();
		
		foreach( $this->engines as $engine ) {
			$engine->addHelpers( array( $this->renderHelper, $this->translatorHelper ) );
		}
		
		return $this->getPlugin()->getTemplateContent( 'form/symfony/wrapper', array( 'form' => $this, 'form_html' => $this->renderHelper->form( $template_vars[ 'form' ], $template_vars ) ) );
	}
	
}