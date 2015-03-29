<?php

// Modifications to the services in the LOCAL env.
return array(
	'mypackage.service.name' => array(
		'calls' => array(
			// Note that the 'calls' lists will be merged.
			array('magic', array('arg1', 'arg2')),
		),
	),
);
