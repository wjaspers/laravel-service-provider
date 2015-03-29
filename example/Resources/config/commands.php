<?php

return array(
	// We can map the alias to the services.php alias.
	'mypackage.command.something' => 'mypackage.command.something',
	// Or define them menually here.
	'mypackage.command.something_else' => 'MyPackage\Commands\DoSomethingElse',
);
