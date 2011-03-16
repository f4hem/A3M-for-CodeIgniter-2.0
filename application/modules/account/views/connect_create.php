<section>
    <header>
        <h2><?php echo anchor(current_url(), lang('connect_create_account')); ?></h2>
    </header>
    <div>
        <h3><?php echo lang('connect_create_heading'); ?></h3>
        <?php echo form_open(uri_string()); ?>
        <?php echo form_fieldset(); ?>

        <?php if (isset($connect_create_error)) : ?>
            <p><?php echo $connect_create_error; ?></p>
        <?php endif; ?>

        <?php echo form_label(lang('connect_create_username'), 'connect_create_username'); ?>
        <?php echo form_input(array(
            'name' => 'connect_create_username',
            'id' => 'connect_create_username',
            'value' => set_value('connect_create_username') ? set_value('connect_create_username') : (isset($connect_create[0]['username']) ? $connect_create[0]['username'] : ''),
            'maxlength' => '16'
        )); ?>
        <?php echo form_error('connect_create_username'); ?>
        <?php if (isset($connect_create_username_error)) : ?>
            <span class="field_error"><?php echo $connect_create_username_error; ?></span>
        <?php endif; ?>

        <?php echo form_label(lang('connect_create_email'), 'connect_create_email'); ?>
        <?php echo form_input(array(
            'name' => 'connect_create_email',
            'id' => 'connect_create_email',
            'value' => set_value('connect_create_email') ? set_value('connect_create_email') : (isset($connect_create[0]['email']) ? $connect_create[0]['email'] : ''),
            'maxlength' => '160'
        )); ?>
        <?php echo form_error('connect_create_email'); ?>
        <?php if (isset($connect_create_email_error)) : ?>
            <span class="field_error"><?php echo $connect_create_email_error; ?></span>
        <?php endif; ?>

        <?php echo form_button(array(
            'type' => 'submit',
            'class' => 'button',
            'content' => lang('connect_create_button')
        )); ?>

        <?php echo form_fieldset_close(); ?>
        <?php echo form_close(); ?>
    </div>
</section>

