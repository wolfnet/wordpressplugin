<?php

/**
 * WolfNet Agent Pages module
 *
 * This module represents the agent pages feature and its related assets and functions.
 *
 * @package Wolfnet
 * @copyright 2015 WolfNet Technologies, LLC.
 * @license GPLv2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
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
        if (is_admin() || !$this->showAgentFeature()) {
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
            'agentsort'      => 'name',
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
			'facebook_url'             => 'https://www.facebook.com',
			'fax_number'               => '',
			'first_name'               => 'Carlisle',
			'form_email_address'       => 'tester@test.com',
			'google_plus_url'          => 'https://www.google.com',
			'home_phone_number'        => '123-456-7892',
			'image_url'                => '//common.wolfnet.com/wordpress/sample-agent.jpg',
			'last_name'                => 'Pike',
			'linkedin_url'             => 'https://www.linkedin.com',
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
			'youtube_url'              => 'https://www.youtube.com',
			'zip_code'                 => '78701',
		));

	}


	public function getOfficeDefaults()
	{

		return array(
			'address_1'             => '',
			'address_2'             => '',
			'city'                  => '',
			'email'                 => '',
			'fax_number'            => '',
			'medium_url'            => '',
			'name'                  => '',
			'office_id'             => '',
			'phone_number'          => '',
			'photo_url'             => '',
			'postal_code'           => '',
			'search_solution_url'   => '',
			'state'                 => '',
			'thumb_url'             => '',
			'toll_free_number'      => '',
		);
	}


	public function getSampleOffice()
	{

		return array_merge($this->getAgentDefaults(), array(
			'address_1'             => '1234 Green Hills Rd',
			'city'                  => 'Austin',
			'email'                 => 'tester@test.com',
			'fax_number'            => '',
			'medium_url'            => '//common.wolfnet.com/wordpress/sample-office.jpg',
			'name'                  => 'Falcon Enterprise Realty',
			'office_id'             => '123456',
			'phone_number'          => '123-456-7890',
			'photo_url'             => '//common.wolfnet.com/wordpress/sample-office.jpg',
			'postal_code'           => '78701',
			'search_solution_url'   => '',
			'state'                 => 'TX',
			'thumb_url'             => '//common.wolfnet.com/wordpress/sample-office.jpg',
			'toll_free_number'      => '',
		));

	}


}

?>
