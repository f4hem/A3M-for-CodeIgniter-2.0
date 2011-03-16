<section>
    <header>
        <h2><?php echo anchor(current_url(), lang('sign_up_page_name')); ?></h2>
    </hader>
    <div>
        <?php echo form_open(uri_string()); ?>
        <?php echo form_fieldset(); ?>

        <h3><?php echo lang('sign_up_heading'); ?></h3>

        <?php echo form_label(lang('sign_up_username'), 'sign_up_username'); ?>
        <?php echo form_input(array(
            'name' => 'sign_up_username',
            'id' => 'sign_up_username',
            'value' => set_value('sign_up_username'),
            'maxlength' => '24'
        )); ?>
        <?php echo form_error('sign_up_username'); ?>
        <?php if (isset($sign_up_username_error)) : ?>
            <span class="field_error"><?php echo $sign_up_username_error; ?></span>
        <?php endif; ?>

        <?php echo form_label(lang('sign_up_password'), 'sign_up_password'); ?>
        <?php echo form_password(array(
            'name' => 'sign_up_password',
            'id' => 'sign_up_password',
            'value' => set_value('sign_up_password')
        )); ?>
        <?php echo form_error('sign_up_password'); ?>

        <?php echo form_label(lang('sign_up_email'), 'sign_up_email'); ?>
        <?php echo form_input(array(
            'name' => 'sign_up_email',
            'id' => 'sign_up_email',
            'value' => set_value('sign_up_email'),
            'maxlength' => '160'
        )); ?>
        <?php echo form_error('sign_up_email'); ?>
        <?php if (isset($sign_up_email_error)) : ?>
            <span class="field_error"><?php echo $sign_up_email_error; ?></span>
        <?php endif; ?>

        <?php if (isset($recaptcha)) : ?>
            <?php echo $recaptcha; ?>
            <?php if (isset($sign_up_recaptcha_error)) : ?>
                <span class="field_error"><?php echo $sign_up_recaptcha_error; ?></span>
            <?php endif; ?>
        <?php endif; ?>

        <?php echo form_button(array(
            'type' => 'submit',
            'class' => 'button',
            'content' => lang('sign_up_create_my_account')
        )); ?>

        <?php echo form_fieldset_close(); ?>
            <?php echo form_close(); ?>

    </div>
    <aside>
        <header>
            <h3><?php echo sprintf(lang('sign_up_third_party_heading')); ?></h3>
        </header>
        <ul>
            <?php foreach($this->config->item('third_party_auth_providers') as $provider) : ?>
            <li class="third_party <?php echo $provider; ?>">
                <?php echo anchor('account/connect_'.$provider, lang('connect_'.$provider),
                array('title'=>sprintf(lang('sign_up_with'), lang('connect_'.$provider)))); ?>
            </li>
            <?php endforeach; ?>
        </ul>

    </aside>
    <footer>
        <p><?php echo lang('sign_up_already_have_account'); ?> <?php echo anchor('account/sign_in', lang('sign_up_sign_in_now')); ?></p>
    </footer>
</section>

