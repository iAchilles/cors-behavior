CORS Behavior
=============
	
Implementing Cross-origin resource sharing support in your Yii Framework application.

####Requirements

- Yii 1.0 or above
	
####Installation
	
- Use Composer or just extract the release file under protected/extensions
	
####Configuration
	
Add the following code to your config file (protected/config/main.php): 
	
```php
	'behaviors' => array(
	        array('class' => 'application.extensions.CorsBehavior',
	            'route' => array('controller/actionA', 'controller/actionB', 'controllerC/*'),
	            'allowOrigin' => '*.domain.com'
	            ),
	    ),
```

- **route** list of routes for CORS-requests. If you want to allow CORS-request for any routes, the value of the option must be a string that contains the "\*". To allow CORS-requests for any actions of the specific controller you can also specify "controllerName/\*".
- **allowOrigin** the origin that is allowed to access the resource. A "\*" can be specified to enable access to resource from any origin. A wildcard can be used to specify list of allowed origins, e.g. "*.yourdomain.com" (sub.yourdomain.com, yourdomain.com, sub.sub.yourdomain.com will be allowed origins in that case)

