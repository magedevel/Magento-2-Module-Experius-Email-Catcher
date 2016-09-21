<?php

/**
 * A Magento 2 module named Experius/EmailCatcher
 * Copyright (C) 2016 Derrick Heesbeen
 * 
 * This file included in Experius/EmailCatcher is licensed under OSL 3.0
 * 
 * http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * Please see LICENSE.txt for the full text of the OSL 3.0 license
 */


namespace Experius\EmailCatcher\Mail;

class Transport extends \Zend_Mail_Transport_Sendmail implements \Magento\Framework\Mail\TransportInterface
{

    protected $_message;
    
    protected $_emailCatcher;
    
    protected $_parameters;
    
    protected $_templateOptions;
    
    protected $_templateVars;

    public function __construct(\Magento\Framework\Mail\MessageInterface $message, \Experius\EmailCatcher\Model\Emailcatcher $emailCatcher, $parameters = null)
    {
        if (!$message instanceof \Zend_Mail) {
            throw new \InvalidArgumentException('The message should be an instance of \Zend_Mail');
        }
        parent::__construct($parameters);
        $this->_message = $message;
        $this->_emailCatcher = $emailCatcher;
        $this->_parameters = $parameters;
    }
    
    public function sendMessage()
    {
        
        $this->_emailCatcher->setBody($this->_message->getBodyHtml()->getRawContent());
        $this->_emailCatcher->setSubject($this->_message->getSubject());
        $this->_emailCatcher->setTo(implode(',',$this->_message->getRecipients()));
        $this->_emailCatcher->setFrom($this->_message->getFrom());
        $this->_emailCatcher->setCreatedAt(date('c'));
        $this->_emailCatcher->save();

        try {
            parent::send($this->_message);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\MailException(new \Magento\Framework\Phrase($e->getMessage()), $e);
        }
    }
}
