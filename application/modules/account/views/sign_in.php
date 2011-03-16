<!-- Begin Sign in !-->
<section id="sign-in">
    <header>
        <h2><?php echo anchor(uri_string().($this->input->get('continue')?'/?continue='.urlencode($this->input->get('continue')):''), lang('sign_in_page_name')); ?></h2>
    </header>
    <div>
        <?php echo form_open(uri_string().($this->input->get('continue')?'/?continue='.urlencode($this->input->get('continue')):'')); ?>

        <?php echo form_fieldset(); ?>
        <h3><?php echo lang('sign_in_heading'); ?></h3>


        <?php echo form_label(lang('sign_in_username_email'), 'sign_in_username_email'); ?>
        <?php echo form_input(array(
                'name' => 'sign_in_username_email',
                'id' => 'sign_in_username_email',
                'value' => set_value('sign_in_username_email'),
                'maxlength' => '24'
            )); ?>
        <?php echo form_error('sign_in_username_email'); ?>
        <?php if (isset($sign_in_username_email_error)) : ?>
            <span class="field_error"><?php echo $sign_in_username_email_error; ?></span>
        <?php endif; ?>

        <?php echo form_label(lang('sign_in_password'), 'sign_in_password'); ?>
        <?php echo form_password(array(
                        'name' => 'sign_in_password',
                        'id' => 'sign_in_password',
                        'value' => set_value('sign_in_password')
                    )); ?>
        <?php echo form_error('sign_in_password'); ?>
        <?php if (isset($sign_in_error)) : ?>
            <span class="field_error"><?php echo $sign_in_error; ?></span>
        <?php endif; ?>

        <?php if (isset($recaptcha)) : ?>
            <?php echo $recaptcha; ?>
            <?php if (isset($sign_in_recaptcha_error)) : ?>
                <span class="field_error"><?php echo $sign_in_recaptcha_error; ?></span>
            <?php endif; ?>
        <?php endif; ?>

        <span>
        <?php echo form_button(array(
            'type' => 'submit',
            'class' => 'button',
            'content' => lang('sign_in_sign_in')
        )); ?>
        <?php echo form_checkbox(array(
            'name' => 'sign_in_remember',
            'id' => 'sign_in_remember',
            'value' => 'checked',
            'checked' => $this->input->post('sign_in_remember'),
            'class' => 'checkbox'
        )); ?>
        <?php echo form_label(lang('sign_in_remember_me'), 'sign_in_remember'); ?>
        </span>

        <?php echo form_fieldset_close(); ?>
        <?php echo form_close(); ?>
    </div>
    <aside>
        <h3><?php echo sprintf(lang('sign_in_third_party_heading')); ?></h3>
        <ul>
            <?php foreach($this->config->item('third_party_auth_providers') as $provider) : ?>
            <li class="third_party <?php echo $provider; ?>"><?php echo anchor('account/connect_'.$provider, lang('connect_'.$provider),
                array('title'=>sprintf(lang('sign_in_with'), lang('connect_'.$provider)))); ?></li>
            <?php endforeach; ?>
        </ul>
    </aside>
    <footer>
        <p><?php echo anchor('account/forgot_password', lang('sign_in_forgot_your_password')); ?><br />
            <?php echo sprintf(lang('sign_in_dont_have_account'), anchor('account/sign_up', lang('sign_in_sign_up_now'))); ?></p>
    </footer>
</section>
<!-- End Sign in !-->

