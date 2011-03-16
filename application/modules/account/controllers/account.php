<?php
/*
 * Account MX Controller
 */
class Account extends MX_Controller {

    function __construct()
    {
        parent::__construct();

		// Load the necessary stuff...
		$this->load->config('account');
        $this->load->helper(array('language', 'ssl', 'url'));
        $this->load->library(array('authentication'));
		$this->load->model(array('account_model'));
        $this->load->language(array('general'));

        // Adjust Callbacks
        $this->authentication->CI =& $this;
	}

    function index()
    {
        if ($this->authentication->is_signed_in())
        {
            redirect('account/account_profile');
        }
        else
        {
            redirect('account/sign_in');
        }
    }


    function sign_in()
    {
        $this->load->library(array('recaptcha', 'form_validation'));
        $this->load->language(array('sign_in', 'connect_third_party'));

        $this->form_validation->CI =& $this;

        // Enable SSL?
		maintain_ssl($this->config->item("ssl_enabled"));

		// Redirect signed in users to profile
		if ($this->authentication->is_signed_in()) redirect('account/account_profile');

        // Set default recaptcha pass
		$recaptcha_pass = ($this->session->userdata('sign_in_failed_attempts') < $this->config->item('sign_in_recaptcha_offset')) ? TRUE : FALSE;

		// Check recaptcha
		$recaptcha_result = $this->recaptcha->check();

		// Setup form validation
		$this->form_validation->set_error_delimiters('<span class="field_error">', '</span>');
		$this->form_validation->set_rules(array(
			array('field'=>'sign_in_username_email', 'label'=>'lang:sign_in_username_email', 'rules'=>'trim|required'),
			array('field'=>'sign_in_password', 'label'=>'lang:sign_in_password', 'rules'=>'trim|required')
		));

		// Run form validation
		if ($this->form_validation->run() === TRUE)
		{
            // Get user by username / email
			if ( ! $user = $this->account_model->get_by_username_email($this->input->post('sign_in_username_email')))
			{
				// Username / email doesn't exist
				$data['sign_in_username_email_error'] = lang('sign_in_username_email_does_not_exist');
			}
			else
			{
				// Either don't need to pass recaptcha or just passed recaptcha
				if ( ! ($recaptcha_pass === TRUE || $recaptcha_result === TRUE) && $this->config->item("sign_in_recaptcha_enabled") === TRUE)
				{
					$data['sign_in_recaptcha_error'] = $this->input->post('recaptcha_response_field') ? lang('sign_in_recaptcha_incorrect') : lang('sign_in_recaptcha_required');
				}
				else
				{
					// Check password
					if ( ! $this->authentication->check_password($user->password, $this->input->post('sign_in_password')))
					{
						// Increment sign in failed attempts
						$this->session->set_userdata('sign_in_failed_attempts', (int)$this->session->userdata('sign_in_failed_attempts')+1);

						$data['sign_in_error'] = lang('sign_in_combination_incorrect');
					}
					else
					{
						// Clear sign in fail counter
						$this->session->unset_userdata('sign_in_failed_attempts');

						// Run sign in routine
						$this->authentication->sign_in($user->id, $this->input->post('sign_in_remember'));
					}
				}
			}
		}

		// Load recaptcha code
		if ($this->config->item("sign_in_recaptcha_enabled") === TRUE)
			if ($this->config->item('sign_in_recaptcha_offset') <= $this->session->userdata('sign_in_failed_attempts'))
				$data['recaptcha'] = $this->recaptcha->load($recaptcha_result, $this->config->item("ssl_enabled"));

		// Load sign in view
		$this->load->view('sign_in', isset($data) ? $data : NULL);
    }

    function sign_out()
    {
        $this->load->language(array('sign_out'));

        if ( ! $this->authentication->is_signed_in()) redirect('');

        // Run sign out routine
		$this->authentication->sign_out();

		// Redirect to homepage
		if ( ! $this->config->item("sign_out_view_enabled")) redirect('');

		// Load sign out view
		$this->load->view('sign_out');
    }

    /**
	 * Account sign up
	 *
	 * @access public
	 * @return void
	 */
	function sign_up()
	{
		$this->load->library(array('recaptcha', 'form_validation'));
		$this->load->model(array('account_details_model'));
        $this->load->language(array('sign_up', 'connect_third_party'));

        $this->form_validation->CI =& $this;

		// Enable SSL?
		maintain_ssl($this->config->item("ssl_enabled"));

		// Redirect signed in users to homepage
		if ($this->authentication->is_signed_in()) redirect('');

		// Check recaptcha
		$recaptcha_result = $this->recaptcha->check();

		// Store recaptcha pass in session so that users only needs to complete captcha once
		if ($recaptcha_result === TRUE) $this->session->set_userdata('sign_up_recaptcha_pass', TRUE);

		// Setup form validation
		$this->form_validation->set_error_delimiters('<span class="field_error">', '</span>');
		$this->form_validation->set_rules(array(
			array('field'=>'sign_up_username', 'label'=>'lang:sign_up_username', 'rules'=>'trim|required|alpha_dash|min_length[2]|max_length[24]'),
			array('field'=>'sign_up_password', 'label'=>'lang:sign_up_password', 'rules'=>'trim|required|min_length[6]'),
			array('field'=>'sign_up_email', 'label'=>'lang:sign_up_email', 'rules'=>'trim|required|valid_email|max_length[160]')
		));

		// Run form validation
		if ($this->form_validation->run() === TRUE)
		{
			// Check if user name is taken
			if ($this->username_check($this->input->post('sign_up_username')) === TRUE)
			{
				$data['sign_up_username_error'] = lang('sign_up_username_taken');
			}
			// Check if email already exist
			elseif ($this->email_check($this->input->post('sign_up_email')) === TRUE)
			{
				$data['sign_up_email_error'] = lang('sign_up_email_exist');
			}
			// Either already pass recaptcha or just passed recaptcha
			elseif ( ! ($this->session->userdata('sign_up_recaptcha_pass') == TRUE || $recaptcha_result === TRUE) && $this->config->item("sign_up_recaptcha_enabled") === TRUE)
			{
				$data['sign_up_recaptcha_error'] = $this->input->post('recaptcha_response_field') ? lang('sign_up_recaptcha_incorrect') : lang('sign_up_recaptcha_required');
			}
			else
			{
				// Remove recaptcha pass
				$this->session->unset_userdata('sign_up_recaptcha_pass');

				// Create user
				$user_id = $this->account_model->create($this->input->post('sign_up_username'), $this->input->post('sign_up_email'), $this->input->post('sign_up_password'));

				// Add user details (auto detected country, language, timezone)
				$this->account_details_model->update($user_id);

				// Auto sign in?
				if ($this->config->item("sign_up_auto_sign_in"))
				{
					// Run sign in routine
					$this->authentication->sign_in($user_id);
				}
				redirect('account/sign_in');
			}
		}

		// Load recaptcha code
		if ($this->config->item("sign_up_recaptcha_enabled") === TRUE)
			if ($this->session->userdata('sign_up_recaptcha_pass') != TRUE)
				$data['recaptcha'] = $this->recaptcha->load($recaptcha_result, $this->config->item("ssl_enabled"));

		// Load sign up view
		$this->load->view('sign_up', isset($data) ? $data : NULL);
	}

    /**
	 * Account profile
	 */
	function account_profile($action = NULL)
	{
	    $this->load->library(array('form_validation'));
	    $this->load->model(array('account_details_model'));
		$this->load->language(array('account_profile'));

        $this->form_validation->CI =& $this;

		// Enable SSL?
		maintain_ssl($this->config->item("ssl_enabled"));

		// Redirect unauthenticated users to signin page
		if ( ! $this->authentication->is_signed_in())
		{
			redirect('account/sign_in/?continue='.urlencode(base_url().'account/account_profile'));
		}

		// Retrieve sign in user
		$data['account'] = $this->account_model->get_by_id($this->session->userdata('account_id'));
		$data['account_details'] = $this->account_details_model->get_by_account_id($this->session->userdata('account_id'));

		// Delete profile picture
		if ($action == 'delete')
		{
			$this->account_details_model->update($data['account']->id, array('picture' => NULL));
			redirect('account/account_profile');
		}

		// Setup form validation
		$this->form_validation->set_error_delimiters('<span class="field_error">', '</span>');
		$this->form_validation->set_rules(array(
			array('field'=>'profile_username', 'label'=>'lang:profile_username', 'rules'=>'trim|required|alpha_dash|min_length[2]|max_length[24]')
		));

		// Run form validation
		if ($this->form_validation->run())
		{
			// If user is changing username and new username is already taken
			if (strtolower($this->input->post('profile_username')) != strtolower($data['account']->username) && $this->username_check($this->input->post('profile_username')) === TRUE)
			{
				$data['profile_username_error'] = lang('profile_username_taken');
				$error = TRUE;
			}
			else
			{
				$data['account']->username = $this->input->post('profile_username');
				$this->account_model->update_username($data['account']->id, $this->input->post('profile_username'));
			}

			// If user has uploaded a file
			if ($_FILES['account_picture_upload']['error'] != 4)
			{
				// Load file uploading library - http://codeigniter.com/user_guide/libraries/file_uploading.html
				$this->load->library('upload', array(
					'file_name' => md5($data['account']->id).'.jpg',
					'overwrite' => true,
					'upload_path' => FCPATH.'resource/user/profile',
					'allowed_types' => 'jpg|png|gif',
					'max_size' => '800' // kilobytes
				));

				/// Try to upload the file
				if ( ! $this->upload->do_upload('account_picture_upload'))
				{
					$data['profile_picture_error'] = $this->upload->display_errors('', '');
					$error = TRUE;
				}
				else
				{
					// Get uploaded picture data
					$picture = $this->upload->data();

					// Create picture thumbnail - http://codeigniter.com/user_guide/libraries/image_lib.html
					$this->load->library('image_lib');
					$this->image_lib->clear();
					$this->image_lib->initialize(array(
						'image_library' => 'gd2',
						'source_image' => FCPATH.'resource/user/profile/'.$picture['file_name'],
						'new_image' => FCPATH.'resource/user/profile/pic_'.$picture['raw_name'].'.jpg',
						'maintain_ratio' => FALSE,
						'quality' => '100%',
						'width' => 100,
						'height' => 100
					));

					// Try resizing the picture
					if ( ! $this->image_lib->resize())
					{
						$data['profile_picture_error'] = $this->image_lib->display_errors();
						$error = TRUE;
					}
					else
					{
						$data['account_details']->picture = 'pic_'.$picture['raw_name'].'.jpg';
						$this->account_details_model->update($data['account']->id, array('picture' => $data['account_details']->picture));
					}

					// Delete original uploaded file
					unlink(FCPATH.'resource/user/profile/'.$picture['file_name']);
				}
			}

			if ( ! isset($error)) $data['profile_info'] = lang('profile_updated');
		}

		$this->load->view('account/account_profile', $data);
	}

	/**
	 * Forgot password
	 */
	function forgot_password()
	{
        $this->load->library(array('recaptcha', 'form_validation'));
		$this->load->language(array('forgot_password'));

		$this->form_validation->CI =& $this;


		// Enable SSL?
		maintain_ssl($this->config->item("ssl_enabled"));

		// Redirect signed in users to homepage
		if ($this->authentication->is_signed_in()) redirect('');

		// Check recaptcha
		$recaptcha_result = $this->recaptcha->check();

		// Store recaptcha pass in session so that users only needs to complete captcha once
		if ($recaptcha_result === TRUE) $this->session->set_userdata('forget_password_recaptcha_pass', TRUE);

		// Setup form validation
		$this->form_validation->set_error_delimiters('<span class="field_error">', '</span>');
		$this->form_validation->set_rules(array(
			array('field'=>'forgot_password_username_email', 'label'=>'lang:forgot_password_username_email', 'rules'=>'trim|required')
		));

		// Run form validation
		if ($this->form_validation->run())
		{
			// User has neither already passed recaptcha nor just passed recaptcha
			if ($this->session->userdata('forget_password_recaptcha_pass') != TRUE && $recaptcha_result !== TRUE)
			{
				$data['forgot_password_recaptcha_error'] = $this->input->post('recaptcha_response_field') ? lang('forgot_password_recaptcha_incorrect') : lang('forgot_password_recaptcha_required');
			}
			else
			{
				// Remove recaptcha pass
				$this->session->unset_userdata('forget_password_recaptcha_pass');

				// Username does not exist
				if ( ! $account = $this->account_model->get_by_username_email($this->input->post('forgot_password_username_email')))
				{
					$data['forgot_password_username_email_error'] = lang('forgot_password_username_email_does_not_exist');
				}
				// Does not manage password
				elseif ( ! $account->password)
				{
					$data['forgot_password_username_email_error'] = lang('forgot_password_does_not_manage_password');
				}
				else
				{
					// Set reset datetime
					$time = $this->account_model->update_reset_sent_datetime($account->id);

					// Load email library
					$this->load->library('email');

					// Generate reset password url
					$password_reset_url = site_url('account/reset_password?id='.$account->id.'&token='.sha1($account->id.$time.$this->config->item('password_reset_secret')));

					// Send reset password email
					$this->email->from($this->config->item('password_reset_email'), lang('reset_password_email_sender'));
					$this->email->to($account->email);
					$this->email->subject(lang('reset_password_email_subject'));
					$this->email->message($this->load->view('reset_password_email', array('username' => $account->username, 'password_reset_url' => anchor($password_reset_url, $password_reset_url)), TRUE));
					echo $this->load->view('reset_password_email', array('username' => $account->username, 'password_reset_url' => anchor($password_reset_url, $password_reset_url)), TRUE);
					@$this->email->send();

					// Load reset password sent view
					$this->load->view('reset_password_sent', isset($data) ? $data : NULL);
					return;
				}
			}
		}

		// Load recaptcha code
		if ($this->session->userdata('forget_password_recaptcha_pass') != TRUE)
			$data['recaptcha'] = $this->recaptcha->load($recaptcha_result, $this->config->item("ssl_enabled"));

		// Load forgot password view
		$this->load->view('forgot_password', isset($data) ? $data : NULL);
	}


	/**
	 * Reset password
	 */
	function reset_password()
	{
	    $this->load->helper(array('date'));
	    $this->load->library(array('recaptcha', 'form_validation'));
	    $this->load->language(array('reset_password'));

	    $this->form_validation->CI =& $this;

		// Enable SSL?
		maintain_ssl($this->config->item("ssl_enabled"));

		// Redirect signed in users to homepage
		if ($this->authentication->is_signed_in()) redirect('account/account_profile');

		// Check recaptcha
		$recaptcha_result = $this->recaptcha->check();

		// User has not passed recaptcha
		if ($recaptcha_result !== TRUE)
		{
			if ($this->input->post('recaptcha_challenge_field'))
			{
				$data['reset_password_recaptcha_error'] = $recaptcha_result ? lang('reset_password_recaptcha_incorrect') : lang('reset_password_recaptcha_required');
			}

			// Load recaptcha code
			$data['recaptcha'] = $this->recaptcha->load($recaptcha_result, $this->config->item("ssl_enabled"));

			// Load reset password captcha view
			$this->load->view('reset_password_captcha', isset($data) ? $data : NULL);
			return;
		}

		// Get account by email
		if ($account = $this->account_model->get_by_id($this->input->get('id')))
		{
			// Check if reset password has expired
			if (now() < (strtotime($account->resetsenton) + $this->config->item("password_reset_expiration")))
			{
				// Check if token is valid
				if ($this->input->get('token') == sha1($account->id.strtotime($account->resetsenton).$this->config->item('password_reset_secret')))
				{
					// Remove reset sent on datetime
					$this->account_model->remove_reset_sent_datetime($account->id);

					// Upon sign in, redirect to change password page
					$this->session->set_userdata('sign_in_redirect', 'account/change_password');

					// Run sign in routine
					$this->authentication->sign_in($account->id);
				}
			}
		}

		// Load reset password unsuccessful view
		$this->load->view('reset_password_unsuccessful', isset($data) ? $data : NULL);
	}

	/**
	 * Linked accounts
	 */
	function linked_accounts()
	{
	    $this->load->library(array( 'form_validation'));
		$this->load->model(array('account_facebook_model', 'account_twitter_model', 'account_openid_model'));
		$this->load->language(array('account_linked', 'connect_third_party'));

		$this->form_validation->CI =& $this;

		// Enable SSL?
		maintain_ssl($this->config->item("ssl_enabled"));

		// Redirect unauthenticated users to signin page
		if ( ! $this->authentication->is_signed_in())
		{
			redirect('account/sign_in/?continue='.urlencode(base_url().'account/account_linked'));
		}

		// Retrieve sign in user
		$data['account'] = $this->account_model->get_by_id($this->session->userdata('account_id'));

		// Delete a linked account
		if ($this->input->post('facebook_id') || $this->input->post('twitter_id') || $this->input->post('openid'))
		{
			if ($this->input->post('facebook_id')) $this->account_facebook_model->delete($this->input->post('facebook_id'));
			elseif ($this->input->post('twitter_id')) $this->account_twitter_model->delete($this->input->post('twitter_id'));
			elseif ($this->input->post('openid')) $this->account_openid_model->delete($this->input->post('openid'));
			$this->session->set_flashdata('linked_info', lang('linked_linked_account_deleted'));
			redirect('account/account_linked');
		}

		// Check for linked accounts
		$data['num_of_linked_accounts'] = 0;

		// Get Facebook accounts
		if ($data['facebook_links'] = $this->account_facebook_model->get_by_account_id($this->session->userdata('account_id')))
		{
			foreach ($data['facebook_links'] as $index => $facebook_link)
			{
				$data['num_of_linked_accounts']++;
			}
		}

		// Get Twitter accounts
		if ($data['twitter_links'] = $this->account_twitter_model->get_by_account_id($this->session->userdata('account_id')))
		{
			$this->load->config('twitter');
			$this->load->helper('twitter');
			foreach ($data['twitter_links'] as $index => $twitter_link)
			{
				$data['num_of_linked_accounts']++;
				$epiTwitter = new EpiTwitter($this->config->item('twitter_consumer_key'), $this->config->item('twitter_consumer_secret'), $twitter_link->oauth_token, $twitter_link->oauth_token_secret);
				$data['twitter_links'][$index]->twitter = $epiTwitter->get_usersShow(array('user_id' => $twitter_link->twitter_id));
			}
		}

		// Get OpenID accounts
		if ($data['openid_links'] = $this->account_openid_model->get_by_account_id($this->session->userdata('account_id')))
		{
			foreach ($data['openid_links'] as $index => $openid_link)
			{
				if (strpos($openid_link->openid, 'google.com')) $data['openid_links'][$index]->provider = 'google';
				elseif (strpos($openid_link->openid, 'yahoo.com')) $data['openid_links'][$index]->provider = 'yahoo';
				elseif (strpos($openid_link->openid, 'myspace.com')) $data['openid_links'][$index]->provider = 'myspace';
				elseif (strpos($openid_link->openid, 'aol.com')) $data['openid_links'][$index]->provider = 'aol';
				else $data['openid_links'][$index]->provider = 'openid';

				$data['num_of_linked_accounts']++;
			}
		}

		$this->load->view('account/account_linked', $data);
	}

	/**
	 * Account password
	 */
	function change_password()
	{
	    $this->load->helper(array('date'));
        $this->load->library(array('form_validation'));
		$this->load->language(array('account_password'));

	    $this->form_validation->CI =& $this;

		// Enable SSL?
		maintain_ssl($this->config->item("ssl_enabled"));

		// Redirect unauthenticated users to signin page
		if ( ! $this->authentication->is_signed_in())
		{
			redirect('account/sign_in/?continue='.urlencode(base_url().'account/change_password'));
		}

		// Retrieve sign in user
		$data['account'] = $this->account_model->get_by_id($this->session->userdata('account_id'));

		// No access to users without a password
		if ( ! $data['account']->password) redirect('');

		### Setup form validation
		$this->form_validation->set_error_delimiters('<span class="field_error">', '</span>');
		$this->form_validation->set_rules(array(
			array('field'=>'password_new_password', 'label'=>'lang:password_new_password', 'rules'=>'trim|required|min_length[6]'),
			array('field'=>'password_retype_new_password', 'label'=>'lang:password_retype_new_password', 'rules'=>'trim|required|matches[password_new_password]')
		));

		### Run form validation
		if ($this->form_validation->run())
		{
			// Change user's password
			$this->account_model->update_password($data['account']->id, $this->input->post('password_new_password'));
			$this->session->set_flashdata('password_info', lang('password_password_has_been_changed'));
			redirect('account/change_password');
		}

		$this->load->view('account/account_password', $data);
	}

	/**
	 * Account settings
	 */
	function settings()
	{
		$this->load->helper(array('date'));
        $this->load->library(array('form_validation'));
		$this->load->model(array('account_details_model', 'ref_country_model', 'ref_language_model', 'ref_zoneinfo_model'));
		$this->load->language(array('account_settings'));

		// Enable SSL?
		maintain_ssl($this->config->item("ssl_enabled"));

		// Redirect unauthenticated users to signin page
		if ( ! $this->authentication->is_signed_in())
		{
			redirect('account/sign_in/?continue='.urlencode(base_url().'account/settings'));
		}

		// Retrieve sign in user
		$data['account'] = $this->account_model->get_by_id($this->session->userdata('account_id'));
		$data['account_details'] = $this->account_details_model->get_by_account_id($this->session->userdata('account_id'));

		// Retrieve countries, languages and timezones
		$data['countries'] = $this->ref_country_model->get_all();
		$data['languages'] = $this->ref_language_model->get_all();
		$data['zoneinfos'] = $this->ref_zoneinfo_model->get_all();

		// Split date of birth into month, day and year
		if ($data['account_details'] && $data['account_details']->dateofbirth)
		{
			$dateofbirth = strtotime($data['account_details']->dateofbirth);
			$data['account_details']->dob_month = mdate('%m', $dateofbirth);
			$data['account_details']->dob_day = mdate('%d', $dateofbirth);
			$data['account_details']->dob_year = mdate('%Y', $dateofbirth);
		}

		// Setup form validation
		$this->form_validation->set_error_delimiters('<div class="field_error">', '</div>');
		$this->form_validation->set_rules(array(
			array('field'=>'settings_email', 'label'=>'lang:settings_email', 'rules'=>'trim|required|valid_email|max_length[160]'),
			array('field'=>'settings_fullname', 'label'=>'lang:settings_fullname', 'rules'=>'trim|max_length[160]'),
			array('field'=>'settings_firstname', 'label'=>'lang:settings_firstname', 'rules'=>'trim|max_length[80]'),
			array('field'=>'settings_lastname', 'label'=>'lang:settings_lastname', 'rules'=>'trim|max_length[80]'),
			array('field'=>'settings_postalcode', 'label'=>'lang:settings_postalcode', 'rules'=>'trim|max_length[40]')
		));

		// Run form validation
		if ($this->form_validation->run())
		{
			// If user is changing email and new email is already taken
			if (strtolower($this->input->post('settings_email')) != strtolower($data['account']->email) && $this->email_check($this->input->post('settings_email')) === TRUE)
			{
				$data['settings_email_error'] = lang('settings_email_exist');
			}
			// Detect incomplete birthday dropdowns
			elseif ( ! (($this->input->post('settings_dob_month') && $this->input->post('settings_dob_day') && $this->input->post('settings_dob_year')) ||
					( ! $this->input->post('settings_dob_month') && ! $this->input->post('settings_dob_day') && ! $this->input->post('settings_dob_year'))) )
			{
				$data['settings_dob_error'] = lang('settings_dateofbirth_incomplete');
			}
			else
			{
				// Update account email
				$this->account_model->update_email($data['account']->id, $this->input->post('settings_email') ? $this->input->post('settings_email') : NULL);

				// Update account details
				if ($this->input->post('settings_dob_month') && $this->input->post('settings_dob_day') && $this->input->post('settings_dob_year'))
					$attributes['dateofbirth'] = mdate('%Y-%m-%d', strtotime($this->input->post('settings_dob_day').'-'.$this->input->post('settings_dob_month').'-'.$this->input->post('settings_dob_year')));
				$attributes['fullname'] = $this->input->post('settings_fullname') ? $this->input->post('settings_fullname') : NULL;
				$attributes['firstname'] = $this->input->post('settings_firstname') ? $this->input->post('settings_firstname') : NULL;
				$attributes['lastname'] = $this->input->post('settings_lastname') ? $this->input->post('settings_lastname') : NULL;
				$attributes['gender'] = $this->input->post('settings_gender') ? $this->input->post('settings_gender') : NULL;
				$attributes['postalcode'] = $this->input->post('settings_postalcode') ? $this->input->post('settings_postalcode') : NULL;
				$attributes['country'] = $this->input->post('settings_country') ? $this->input->post('settings_country') : NULL;
				$attributes['language'] = $this->input->post('settings_language') ? $this->input->post('settings_language') : NULL;
				$attributes['timezone'] = $this->input->post('settings_timezone') ? $this->input->post('settings_timezone') : NULL;
				$this->account_details_model->update($data['account']->id, $attributes);

				$data['settings_info'] = lang('settings_details_updated');
			}
		}

		$this->load->view('account/account_settings', $data);
	}


	/**
	 * Check if a username exist
	 *
	 * @access public
	 * @param string
	 * @return bool
	 */
	function username_check($username)
	{
		return $this->account_model->get_by_username($username) ? TRUE : FALSE;
	}

	/**
	 * Check if an email exist
	 *
	 * @access public
	 * @param string
	 * @return bool
	 */
	function email_check($email)
	{
		return $this->account_model->get_by_email($email) ? TRUE : FALSE;
	}

}

