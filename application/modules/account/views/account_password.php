<section>
    <header>
        <h2><?php echo anchor(current_url(), lang('password_page_name')); ?></h2>
    </header>
    <div>
        <?php echo form_open(uri_string()); ?>
        <?php echo form_fieldset(); ?>

        <?php if ($this->session->flashdata('password_info')) : ?>
            <p><?php echo $this->session->flashdata('password_info'); ?></p>
        <?php endif; ?>

        <?php echo lang('password_safe_guard_your_account'); ?>

        <?php echo form_label(lang('password_new_password'), 'password_new_password'); ?>
        <?php echo form_password(array(
            'name' => 'password_new_password',
            'id' => 'password_new_password',
            'value' => set_value('password_new_password'),
            'autocomplete' => 'off'
        )); ?>
        <?php echo form_error('password_new_password'); ?>

        <?php echo form_label(lang('password_retype_new_password'), 'password_retype_new_password'); ?>
        <?php echo form_password(array(
            'name' => 'password_retype_new_password',
            'id' => 'password_retype_new_password',
            'value' => set_value('password_retype_new_password'),
            'autocomplete' => 'off'
        )); ?>
        <?php echo form_error('password_retype_new_password'); ?>

        <?php echo form_button(array(
            'type' => 'submit',
            'class' => 'button',
            'content' => lang('password_change_my_password')
        )); ?>

        <?php echo form_fieldset_close(); ?>
        <?php echo form_close(); ?>
    </div>
</section>

