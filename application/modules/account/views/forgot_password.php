<section>
    <header>
        <h2><?php echo anchor(current_url(), lang('forgot_password_page_name')); ?></h2>
    </header>
    <div>
        <?php echo form_open(uri_string()); ?>
        <?php echo form_fieldset(); ?>

        <p><?php echo lang('forgot_password_instructions'); ?></p>

        <?php echo form_label(lang('forgot_password_username_email'), 'forgot_password_username_email'); ?>
        <?php echo form_input(array(
                'name' => 'forgot_password_username_email',
                'id' => 'forgot_password_username_email',
                'value' => set_value('forgot_password_username_email') ? set_value('forgot_password_username_email') : (isset($account) ? $account->username : ''),
                'maxlength' => '80'
            )); ?>
        <?php echo form_error('forgot_password_username_email'); ?>
        <?php if (isset($forgot_password_username_email_error)) : ?>
            <span class="field_error"><?php echo $forgot_password_username_email_error; ?></span>
        <?php endif; ?>

        <?php if (isset($recaptcha)) : ?>
            <?php echo $recaptcha; ?>
            <?php if (isset($forgot_password_recaptcha_error)) : ?>
                <span class="field_error"><?php echo $forgot_password_recaptcha_error; ?></span>
            <?php endif; ?>
        <?php endif; ?>

        <?php echo form_button(array(
            'type' => 'submit',
            'class' => 'button',
            'content' => lang('forgot_password_send_instructions')
        )); ?>

        <?php echo form_fieldset_close(); ?>
        <?php echo form_close(); ?>
    </div>
</section>

