<section>
    <header>
        <h2><?php echo anchor(current_url(), lang('reset_password_page_name')); ?></h2>
    </header>
    <div>
        <?php echo form_open(uri_string().(empty($_SERVER['QUERY_STRING'])?'':'?'.$_SERVER['QUERY_STRING'])); ?>
        <?php echo form_fieldset(); ?>

        <p><?php echo lang('reset_password_captcha'); ?></p>

        <?php if (isset($recaptcha)) : ?>
            <?php echo $recaptcha; ?>
            <?php if (isset($reset_password_recaptcha_error)) : ?>
                <span class="field_error"><?php echo $reset_password_recaptcha_error; ?></span>
            <?php endif; ?>
        <?php endif; ?>

        <?php echo form_button(array(
            'type' => 'submit',
            'class' => 'button',
            'content' => lang('reset_password_captcha_submit')
        )); ?>

        <?php echo form_fieldset_close(); ?>
        <?php echo form_close(); ?>
    </div>
</section>

