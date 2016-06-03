<?php

/**
 * WolfNet Agent Pages module
 *
 * This module represents the agent pages feature and its related assets and functions.
 *
 * @package Wolfnet
 * @copyright 2015 WolfNet Technologies, LLC.
 * @license GPLv2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 *
 */
class Wolfnet_Module_AgentPages
{
	/**
    * This property holds the current instance of the Wolfnet_Plugin.
    * @var Wolfnet_Plugin
    */
    protected $plugin = null;


    /**
    * This property holds an instance of the Wolfnet_AgentPagesHandler.
    * @var Wolfnet_AgentPagesHandler
    */
    protected $handler = null;


    public function __construct($plugin, $handler) {
        $this->plugin = $plugin;
        $this->handler = $handler;
    }


    public function scAgentPages($attrs)
    {
        if(!$this->showAgentFeature()) {
            return '';
        }

        try {
            $defaultAttributes = $this->getDefaults();

            $criteria = array_merge($defaultAttributes, (is_array($attrs)) ? $attrs : array());

            $this->plugin->decodeCriteria($criteria);

            $out = $this->agentPageHandler($criteria);

        } catch (Wolfnet_Exception $e) {
            $out = $this->plugin->displayException($e);
        }

        return $out;
    }


    public function showAgentFeature()
    {
        try {
            $data = $this->plugin->api->sendRequest(
                $this->plugin->keyService->getDefault(),
                '/settings',
                'GET'
            );
        } catch (Wolfnet_Exception $e) {
            return $this->plugin->displayException($e);
        }

        $leadsEnabled = $data['responseData']['data']['site']['my_agents_leads'];

        return $leadsEnabled;
    }


    public function getDefaults()
    {

        return array(
            'officetitle'    => '',
            'agenttitle'     => '',
            'detailtitle'    => '',
            'showoffices'    => true,
            'activelistings' => true,
            'soldlistings'   => false,
            'excludeoffices' => '',
            'numperpage'     => 10,
        );

    }


    public function getOptions($instance = null)
    {
        $options = $this->plugin->getOptions($this->getDefaults(), $instance);

        return $options;

    }


    public function agentPageHandler(array $criteria = array())
    {
        $key = $this->plugin->keyService->getFromCriteria($criteria);

        if (!$this->plugin->keyService->isSaved($key)) {
            return false;
        }

        $vars = array(
            'instance_id' => str_replace('.', '', 'wolfnet_agentPages_' . $this->plugin->createUUID()),
            'criteria'    => $criteria,
        );

        $args = $this->plugin->convertDataType(array_merge($criteria, $vars));

        $this->handler->setKey($key);
        $this->handler->setArgs($args);

        return $this->handler->handleRequest();
    }


	public function getAgentDefaults()
	{

		return array(
			'address_1'                => '',
			'address_2'                => '',
			//'agent_detail'             => '',
			'agent_id'                 => '',
			'areas_served'             => '',
			'awards'                   => '',
			//'bcc_address'              => '',
			'bio'                      => '',
			//'broker_name'              => '',
			'business_name'            => '',
			//'bweb_url'                 => '',
			//'cc_address'               => '',
			'city'                     => '',
			//'date_created'             => '',
			//'date_last_modified'       => '',
			'designations'             => '',
			'display_agent'            => 0,
			'education'                => '',
			'email_address'            => '',
			//'email_signature'          => '',
			'experience'               => '',
			'facebook_url'             => '',
			'fax_number'               => '',
			'first_name'               => '',
			//'form_email_address'       => '',
			//'generic_id'               => '',
			'google_plus_url'          => '',
			//'hidden_url'               => '',
			'home_phone_number'        => '',
			//'image_name'               => '',
			//'image_name2'              => '',
			'image_url'                => '',
			'instagram_url'            => '',
			//'is_luxury'                => '',
			'last_name'                => '',
			//'lat'                      => '',
			//'license'                  => '',
			//'license_country'          => '',
			//'license_state'            => '',
			'linkedin_url'             => '',
			//'lng'                      => '',
			//'market'                   => '',
			'medium_url'               => '',
			'mls_agent_id'             => '',
			'mobile_phone'             => '',
			'motto_quote'              => '',
			'office_id'                => '',
			'office_name'              => '',
			'office_phone_number'      => '',
			'optional_field_label'     => '',
			'optional_field_value'     => '',
			'pager_number'             => '',
			'pinterest_url'            => '',
			'primary_contact_phone'    => '',
			//'send_email_address'       => '',
			'services_available'       => '',
			//'signed_up_via_br'         => '',
			//'signed_up_via_my_agents'  => '',
			//'site_guid'                => '',
			'specialty'                => '',
			'state'                    => '',
			'thumbnail_url'            => '',
			//'timezone_offset'          => '',
			'title'                    => '',
			'toll_free_phone_number'   => '',
			'twitter_url'              => '',
			//'user_type'                => '',
			'web_url'                  => '' ,
			//'wnt'                      => false,
			'youtube_url'              => '',
			'zip_code'                 => '',
		);
	}


	public function getSampleAgent()
	{

		return array_merge($this->getAgentDefaults(), array(
			'address_1'                => '1234 Green Hills Rd',
			'agent_id'                 => '6FC778CB-119D-48F9-A6BD-A6452FAD4367',
			'bio'                      => 'Vestibulum eleifend, mi eu elementum sodales, felis justo facilisis dui, sit amet elementum diam lorem vel lectus. Cras nec lobortis justo, ac volutpat est. Duis tellus est, feugiat id ullamcorper.',
			'city'                     => 'Austin',
			'display_agent'            => 1,
			'email_address'            => 'tester@test.com',
			'experience'               => 'In tincidunt lorem sed nisl venenatis imperdiet. Aenean quis diam odio. Morbi cursus cursus risus. Fusce semper nulla eu neque consectetur, sit amet ornare arcu aliquet. Morbi euismod nulla in sem aliquet.',
			'facebook_url'             => 'http://www.facebook.com',
			'fax_number'               => '',
			'first_name'               => 'Carlisle',
			'form_email_address'       => 'tester@test.com',
			'google_plus_url'          => 'http://www.google.com',
			'home_phone_number'        => '123-456-7892',
			'image_url'                => '//common.wolfnet.com/wordpress/sample-agent.jpg',
			'last_name'                => 'Pike',
			'linkedin_url'             => 'http://www.linkedin.com',
			'medium_url'               => '//common.wolfnet.com/wordpress/sample-agent.jpg',
			'mls_agent_id'             => '123456789',
			'mobile_phone'             => '123-456-7891',
			'office_id'                => '1234',
			'office_phone_number'      => '123-456-7890 Ext 321',
			'optional_field_label'     => 'Hobbies',
			'optional_field_value'     => 'Aliquam vel augue egestas, gravida quam ac, ultricies lorem. Etiam et sem hendrerit, efficitur mi quis, gravida dolor. Etiam eu hendrerit felis.',
			'specialty'                => 'Residential Real Estate',
			'state'                    => 'TX',
			'thumbnail_url'            => '//common.wolfnet.com/wordpress/sample-agent.jpg',
			'title'                    => 'Realtor',
			'web_url'                  => '#' ,
			'youtube_url'              => 'http://www.youtube.com',
			'zip_code'                 => '78701',
		));

	}


}

?>
