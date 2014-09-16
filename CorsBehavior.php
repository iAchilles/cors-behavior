<?php

/*
 * CorsBehavior class file
 * 
 * @author Igor Manturov, Jr. <im@youtu.me>
 * @link https://github.com/iAchilles/cors-behavior
 */

/**
 * CorsBehavior Automatically adds the Access-Control-Allow-Origin response 
 * header for specific routes.
 * 
 * @version 1.0
 *
 */
class CorsBehavior extends CBehavior
{
    
    private $_allowOrigin;
    
    private $_route = array();
    
    
    public function events()
    {
        return array_merge(parent::events(), 
                array('onBeginRequest' => 'onBeginRequestHandler'));
    }
    
    
    public function onBeginRequestHandler($event)
    {
        if (is_null($this->_allowOrigin))
        {
            return;
        }
        
        if ($this->checkAllowedRoute())
        {
            
            $origin = $this->parseHeaders();
            
            if ($origin !== false)
            {
                $this->setAllowOriginHeader($origin);
            }
        }
    }
    
    
    /**
     * Sets list of routes for CORS-requests.
     * @param mixed $route An array of routes (controllerID/actionID). If you 
     * want to allow CORS-requests for any routes, the value of the parameter
     * must be a string that contains the "*". 
     * @throws CException
     */
    public function setRoute($route)
    {
        if (!is_array($route) && $route !== '*')
        {
            throw new CException('The value of the "route" property must be an '
                    . 'array or a string that contains the "*".');
        }
        
        $this->_route = $route;
    }
    
    
    /**
     * Sets the allowed origin.
     * @param string $origin The origin that is allowed to access the resource.
     * A "*" can be specified to enable access to resource from any origin. 
     * A wildcard can be used to specify list of allowed origins, 
     * e.g. "*.yourdomain.com" (sub.yourdomain.com, yourdomain.com, 
     * sub.sub.yourdomain.com will be allowed origins in that case)
     * @throws CExecption
     */
    public function setAllowOrigin($origin)
    {
        if (!is_string($origin))
        {
            throw new CExecption('The value of the "allowOrigin" property must be '
                    . 'a string.');
        }
        
        $this->_allowOrigin = $origin;
    }
    
    
    /**
     * Parses headers and returns the value of the Origin request header.
     * @return mixed The origin that is allowed to access the resource. 
     * (the value of the Origin request header), otherwise false. 
     */
    protected function parseHeaders()
    {
        if (!function_exists('getallheaders'))
        {
            $headers = $this->getAllHeaders();
        }
        else
        {
            $headers = getallheaders();
        }
        
        if ($headers === false)
        {
            return false;
        }
        
        $headers = array_change_key_case($headers, CASE_LOWER);
        
        if (!array_key_exists('origin', $headers))
        {
            return false;
        }
        
        $origin = $headers['origin'];
        $origin = parse_url($origin, PHP_URL_HOST);
        
        if (is_null($origin))
        {
            return false;
        }
        
        if(strlen($this->_allowOrigin) === 1)
        {
            return $headers['origin'];
        }
        
        if (stripos($this->_allowOrigin, '*') === false)
        {
            return $origin === $this->_allowOrigin ? $headers['origin'] : false;
        }
        
        $pattern = '/' . substr($this->_allowOrigin, 1) . '$/';
        
        if (substr($this->_allowOrigin, 2) === $origin
                || preg_match($pattern, $origin) === 1)
        {
            return $headers['origin'];
        }
        
    }
    
    
    /**
     * Checks if CORS-request is allowed for the current route.
     * @return boolean Whether CORS-request is allowed for the current route.
     */
    protected function checkAllowedRoute()
    {
        if ($this->_route === '*')
        {
            return true;
        }
        
        $route = Yii::app()->getUrlManager()
                ->parseUrl(Yii::app()->getRequest());
        
        $wildcardRoute = preg_replace('#([^/]*)$#', '*', $route, 1);
        
        return in_array($route, $this->_route) || in_array($wildcardRoute, $this->_route);
    }
    
    
    /**
     * Sets Access-Control-Allow-Origin response header.
     * @param string $origin the value of the Access-Control-Allow-Origin response 
     * header.
     */
    protected function setAllowOriginHeader($origin)
    {
        header('Access-Control-Allow-Origin: ' . $origin);
    }
    
    
    /**
     * This method is used to get HTTP headers when PHP runs as FastCGI.
     * @return array An associative array of all the HTTP headers in the current request.
     */
    protected function getAllHeaders()
    {
       $headers = ''; 
       
       foreach ($_SERVER as $name => $value) 
       { 
           if (substr($name, 0, 5) == 'HTTP_') 
           { 
               $headers[str_replace(' ', '-', ucwords(strtolower
                       (str_replace('_', ' ', substr($name, 5)))))] = $value; 
           } 
       } 
       
       return $headers; 
    }
   
}
