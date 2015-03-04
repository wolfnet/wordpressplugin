<?php

/**
 * WolfNet API Exception
 *
 * This exception class is use by the API Client to express any exceptions that occur during
 * the process of communicating wit the WolfNet API.
 *
 * This exception does not currently have any state or functionality of its own but exists as an
 * extension of the Wolfnet_Exception class so that we can more easily catch exception of this
 * specific type.
 *
 * @package Wolfnet\Api
 * @copyright 2015 WolfNet Technologies, LLC.
 * @license GPLv2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 *
 */
class Wolfnet_Api_Exception extends Wolfnet_Exception
{

}
