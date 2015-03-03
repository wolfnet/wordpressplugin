<?php

class Wolfnet_Exception extends Exception
{


    /* PROPERTIES ******************************************************************************* */

    protected $details = '';

    protected $data = null;

    protected $previous = null;


    /* CONSTRUCTOR ****************************************************************************** */

    public function __construct($message, $details='', $data=null, Exception $previous=null, $code=0)
    {
        parent::__construct($message, $code);

        $this->details = $details;
        $this->data = $data;
        $this->previous = $previous;

    }


    /* PUBLIC METHODS *************************************************************************** */

    public function getDetails()
    {
        return $this->details;
    }


    public function getData()
    {
        $previous = $this->getPrevious();

        if ($this->data === null && $previous !== null) {
            return $previous->getData();
        } else {
            return $this->data;
        }

    }


    public function getPrev()
    {
        return $this->previous;
    }


    public function append($message)
    {
        $this->message .= ' ' . $message;

        return $this;

    }


    public function appendDetails($details)
    {
        $this->details .= ' ' . $details;

        return $this;

    }


    public function __toString()
    {
        return parent::__toString() . "\n" . json_encode($this->getData());
    }


}
