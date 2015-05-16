<?php
	use Aws\Sns\SnsClient;

	abstract class Service_Controller_Abstract extends Zend_Controller_Action
	{
		protected $_serviceName;
		
		protected $_notifyName;
		protected $_notifyActions = array();

		protected $_sns;

	    public function indexAction()
	    {
			//Docs
	    }

	    public function amfAction()
	    {
	    	if($this->_isLogged())
	    	{
				$this->_helper->viewRenderer->setNoRender();
				$this->_response->setHeader('Content-Type', 'application/x-amf', true);
	
				$server = $this->getAmfServer($this->_serviceName);
				$result = $server->handle();
				
				if(in_array($server->getRequest()->getMethod(), $this->_notifyActions))
					$this->_notify($server->getRequest()->getMethod(), Zend_Json::encode($server->getRequest()->getParams()), $server->getResponse()->getResult());
				
				echo $result;
				exit;
	    	}
	    }
	    
	    public function jsonAction()
	    {
	    	if($this->_isLogged())
	    	{
				$this->_helper->viewRenderer->setNoRender();
				$this->_response->setHeader('Content-Type', 'application/json', true);
	
				$server = $this->getJsonServer($this->_serviceName);
				$result = $server->handle();
				
				if(in_array($server->getRequest()->getMethod(), $this->_notifyActions))
					$this->_notify($server->getRequest()->getMethod(), Zend_Json::encode($server->getRequest()->getParams()), $server->getResponse()->getResult());
				
				echo $result;
				exit;
	    	}
	    }
	    
	    ////////////////////////////////////////////////////////////////////////////////////////
	    
	    protected function _isLogged()
	    {
			if($this->getRequest()->isOptions())
			{	
		    	header("HTTP/1.0 200 OK"); 
				exit;
		    	return false;
			}
			
			if( $this->getRequest()->getServer('HTTP_ORIGIN') && strpos($this->getRequest()->getServer('HTTP_ORIGIN'), 'file://') === false )
    			$host = Zend_Uri::factory( $this->getRequest()->getServer('HTTP_ORIGIN') )->getHost();
			else
				$host = "application";
    		Zend_Registry::set('host', $host);
    		
    		$sessionKey = $this->getRequest()->getHeader('Authorization');
			if(!$sessionKey) // BUG SUR CHROME
    			$sessionKey = $this->getRequest()->getHeader('authorization');

	    	if($sessionKey)
	    	{
    			if(Zend_Registry::get('cache')->test($sessionKey))
    			{ 
    				$apiConf = Zend_Registry::get('cache')->load($sessionKey);
    				if($apiConf['host'] == $host)
    				{
	    				Zend_Registry::set('sessionKey', $sessionKey);
	    				Zend_Registry::set('sessionID', $apiConf['sessionID']);
	    				Zend_Registry::set('userID', $apiConf['userID']);
	    				Zend_Registry::get('cache')->touch($sessionKey, Zend_Registry::get('config')->cache->frontend->options->lifetime);
	    				return true;
    				}
    				else
    				{
			    		header("HTTP/1.0 401 Unauthorized"); 
						exit;
			    		return false;
    				}
    			}
    			else
    			{
		    		header("HTTP/1.0 401 Unauthorized"); 
					exit;
		    		return false;
    			}
	    	}
	    	else
	    	{
    			if(
    				($this->getRequest()->getModuleName() == "services") &&
    				($this->getRequest()->getControllerName() == "connection")
    			)
    			{
    				return true;
    			}
    			else
    			{
		    		header("HTTP/1.0 401 Unauthorized"); 
					exit;
		    		return false;
    			}
	    	}
	    }		

		////////////////////////////////////////////////////////////////////////////////////////
		
		/**
		 * Notify the Chatanoo Notify Server
		 */
		protected function _notify($method, $params, $content) {
			if (!$this->_sns) {
				$this->_sns = SnsClient::factory(array(
					'region' => 'eu-west-1'
				));
			}

			$topic = Zend_Registry::get('config')->notify->topic;
			$message = array(
				'default' => json_encode(array(
					'name' => $this->_notifyName,
					'session' => Zend_Registry::get('sessionID'),
					'data' => array(
						'method' => $method,
						'params' => $params,
						'byUser' => Zend_Registry::get('userID'),
						'result' => $content
					)
				))
			);

			$this->_sns->publish(array(
				'TopicArn' => $topic,
				'MessageStructure' => 'json',
				'Message' => json_encode($message)
			));
		}
		
		
		////////////////////////////////////////////////////////////////////////////////////////
		
	    /**
	     * Get Amf Server
	     * 
	     * @return Maz_Amf_Server
	     */
	    protected function getAmfServer($service)
	    {
	    	$server = new Maz_Amf_Server();
			$server->setClass($service);

			foreach(Zend_Registry::get('config')->amfConfig->mapping as $mapping)
			{
				if(is_null($mapping->addField))
					$server->setClassMap($mapping->as, $mapping->php);
				else
					$server->setClassMap($mapping->as, $mapping->php, $mapping->addField->toArray());
			}

			$server->setProduction(APPLICATION_ENV == 'production');
			return $server;
	    }
	    

	    /**
	     * Get Json Server
	     * 
	     * @return Zend_Json_Server
	     */
	    protected function getJsonServer($service)
	    {
	    	Zend_Json::$useBuiltinEncoderDecoder = true;
	    	
	    	$server = new Zend_Json_Server();
	    	$server->setClass($service);
			$server->setProduction(APPLICATION_ENV == 'production');
			
			$params = $server->getRequest()->getParams();
			$params = $this->_jsonToTatVo($params);
        	if(!is_array($params))
        		$params = array_values($params);
        	
        	$server->getRequest()->setParams($params);	
        	
			return $server;
	    }
	    
	    protected function _jsonToTatVo($params)
	    {
	    	if(is_array($params) && array_key_exists('__className', $params))
	    	{
	    		$className = $params['__className'];
	    		$params = new $className($params);
	    	}
	    	else if(is_array($params))
	    	{
	    		foreach($params as $key=>$value)
	    			$params[$key] = $this->_jsonToTatVo($value);
	    	}
	    	
	    	return $params;
	    }

	}