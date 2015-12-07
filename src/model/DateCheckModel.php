<?php
use Silex\Application;

class DateCheckModel
{

	protected $app;

	const DEFAULT_ZONE = 'Australia/Adelaide';

	function __construct( Silex\Application $application )
	{
		
		$this->app = $application;
	}

	public function createForm($dateFrom, $dateFromZone = DEFAULT_ZONE, $dateTo, $dateToZone = DEFAULT_ZONE) 
	{
		return $this->app['form.factory']->createBuilder('form')
	        ->add('timeFrom', 'datetime', array(
	            'attr'=> array(
	                'class'=>'form-control datetimepicker'),
	            'widget' => 'single_text',
	            'label' => 'Time From:',
	        ))
	        ->add('timeFromZone', 'timezone', array(
	             'label' => 'Timezone:',
	             'attr'=> array('class'=>'form-control'),
	             'data' => $dateFromZone,
	        ))
	        ->add('timeTo', 'datetime', array(
	            'attr'=> array('class'=>'form-control datetimepicker'),
	            'widget' => 'single_text',
	            'label' => 'Time To:',
	        ))
	        ->add('timeToZone', 'timezone', array(
	             'label' => 'Timezone:',
	             'attr'=> array('class'=>'form-control'),
	             'data' => $dateToZone
	        ))
	        ->getForm(); 
	}
}