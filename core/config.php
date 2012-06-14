<?php

$securitySalt = 'QualityCrowd2-7078e430';

return array(

'securitySalt' => $securitySalt,

'adminUsers' => array(
	'admin' => sha1('admin' . $securitySalt)
),

);
