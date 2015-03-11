<?php

/**
 * WolfNet Exception
 *
 * This exception class is used to convey any exceptional state that may occur during the execution
 * of code specific to this plugin. Having this custom exception class makes handling WolfNet
 * specific exceptions much simpler.
 *
 * @package Wolfnet\Api
 * @copyright 2015 WolfNet Technologies, LLC.
 * @license GPLv2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 *
 */
class Wolfnet_Exception extends Exception
{


    /* PROPERTIES ******************************************************************************* */

    /**
     * This property is used to hold details about the exception which we do not want to share as
     * openly as the standard exception message.
     *
     * @var string
     *
     */
    protected $details = '';

    /**
     * This property holds any data that is related to the exception and may be useful for debugging.
     * @var mixed
     */
    protected $data = null;

    /**
     * This property serves as a back fill for older versions of PHP which do not include the
     * previous exception support. This property holds a reference to any previous exception that
     * was thrown before the current exception.
     * @var Exception
     */
    protected $previous = null;


    /* CONSTRUCTOR ****************************************************************************** */

    /**
     * This constructor argument collects our unique exception data as well as standard exception
     * data. This constructor calls the parent constructor function to utilize it's infrastructure
     * where appropriate.
     *
     * @param string     $message   A basic message string.
     * @param string     $details   A more detailed exception string (not as public, but not private).
     * @param mixed      $data      Arbitrary data that may be useful for debugging.
     * @param Exception  $previous  An exception that was throw before the current instance.
     * @param mixed      $code      An arbitrary code to make error handling easier.
     */
    public function __construct($message, $details='', $data=null, Exception $previous=null, $code=0)
    {
        parent::__construct($message, $code);

        $this->details = $details;
        $this->data = $data;
        $this->previous = $previous;

    }


    /* PUBLIC METHODS *************************************************************************** */

    /**
     * This method is used to retrieve the details string.
     * @return string
     */
    public function getDetails()
    {
        return $this->details;
    }


    /**
     * This method retrieves any data that was included with the exception. If no data was found in
     * the current exception it attempts to retrieve any data included with the previous exception
     * if one exists.
     *
     * @return mixed
     *
     */
    public function getData()
    {
        $previous = $this->getPrevious();

        if ($this->data === null && $previous !== null) {
            return $previous->getData();
        } else {
            return $this->data;
        }

    }


    /**
     * This method gets the previous exception included with the current instance.
     *
     * NOTE: This method is getPrev rather that getPrevious because in newer version of PHP
     * getPrevious is a "final" signature method.
     *
     * @return Exception
     *
     */
    public function getPrev()
    {
        return $this->previous;
    }


    /**
     * This method can be used to append a string to the exception message.
     *
     * @param  string  $message  A message string
     *
     * @return Wolfnet_Exception  Current instance (for method chaining)
     *
     */
    public function append($message)
    {
        $this->message .= ' ' . $message;

        return $this;

    }


    /**
     * This method can be used to append a string to the exception details.
     *
     * @param  string  $details  A details string
     *
     * @return Wolfnet_Exception  Current instance (for method chaining)
     *
     */
    public function appendDetails($details)
    {
        $this->details .= ' ' . $details;

        return $this;

    }


}
