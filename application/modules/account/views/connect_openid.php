<section>
    <header>
        <h2><?php echo anchor(current_url(), sprintf(lang('connect_with_x'), lang('connect_openid'))); ?></h2>
    </header>
    <div>
        <h3><?php echo sprintf(lang('connect_enter_your'), lang('connect_openid_url')); ?></h3>
        <span><?php echo anchor($this->config->item('openid_what_is_url'), lang('connect_start_what_is_openid'), array('target'=>'_blank')); ?></span></h3>
        <?php echo form_open(uri_string()); ?>
        <?php echo form_fieldset(); ?>
        <?php if (isset($connect_openid_error)) : ?>
                <p><?php echo $connect_openid_error; ?></p>
        <?php endif; ?>

        <?php echo form_input(array(
            'name' => 'connect_openid_url',
            'id' => 'connect_openid_url',
            'class' => 'openid',
            'value' => set_value('connect_openid_url')
        )); ?>
        <?php echo form_error('connect_openid_url'); ?>

        <?php echo form_button(array(
            'type' => 'submit',
            'class' => 'button',
            'content' => lang('connect_proceed')
        )); ?>

        <?php echo form_fieldset_close(); ?>
        <?php echo form_close(); ?>
    </div>
</section>

