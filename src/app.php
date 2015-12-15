<?php

$app = require __DIR__.'/bootstrap.php';

use Symfony\Component\HttpFoundation\Request;

$app->match('/', function (Request $request) use ($app) {

    $templateData = array(
        'page' => 'datecheck');

    $form = $app['form.factory']->createBuilder('form')
        ->add('timeFrom', 'datetime', array(
            'attr'=> array(
                'class'=>'form-control datetimepicker'),
            'widget' => 'single_text',
            'label' => 'Time From:',
        ))
        ->add('timeFromZone', 'timezone', array(
             'label' => 'Timezone:',
             'attr'=> array('class'=>'form-control'),
             'data' => 'Australia/Adelaide',
        ))
        ->add('timeTo', 'datetime', array(
            'attr'=> array('class'=>'form-control datetimepicker'),
            'widget' => 'single_text',
            'label' => 'Time To:',
        ))
        ->add('timeToZone', 'timezone', array(
             'label' => 'Timezone:',
             'attr'=> array('class'=>'form-control'),
             'data' => 'Australia/Adelaide'
        ))
        ->add('difference', 'choice', array(
            'attr'=> array('class'=>'form-control'),
            'label' => 'Difference:',
            'choices' => array(
                'seconds' => 'Seconds', 
                'minutes' => 'Minutes',
                'hours' => 'Hours',
                'days' => 'Days',
                'weeks' => 'Weeks',
                'compWeeks' => 'Complete Weeks',
                'weekdays' => 'Week Days',
                'months' => 'Months',
                'years' => 'Years'

            ),
            'expanded' => false,
        ))
        ->getForm(); 

    if ('POST' == $request->getMethod()) {
        $templateData['showResponse'] = TRUE;
        $form->bind($request);
        if ($form->isValid()) 
        {
            $data = $form->getData();

            $response = print_r($data, true);

            try {
                $checker = new aligent\DateChecker($data);
            } catch  (Exception $e) {
                $templateData['dateCheckError'] = $e->getMessage();   
            }

            $templateData['fromDateTime'] = $data['timeFrom']->format('Y-m-d H:i:s');
            $templateData['fromTimeZone'] = $data['timeFromZone'];
            $templateData['toDateTime'] = $data['timeTo']->format('Y-m-d H:i:s');
            $templateData['toTimeZone'] = $data['timeToZone']; 

            if ($checker != NULL) {
                try {
                    $difference = $checker->timeDifference($data['difference']);
                } catch  (Exception $e) {
                    $templateData['dateCheckError'] = $e->getMessage();   
                }
                
                if ( isset($difference) ) $templateData['dateCheckResponse'] = $difference;
            }


        }
    }        

    $templateData['dateForm'] = $form->createView();

    return $app['twig']->render('dateCheck.twig', $templateData);
})
->bind('home');

$app->get('/about', function () use ($app) {
    return $app['twig']->render('about.twig', array(
        'page' => 'about'));
});


return $app;