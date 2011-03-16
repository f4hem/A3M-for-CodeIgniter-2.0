<section>
    <header>
        <h2><?php echo anchor(current_url(), lang('profile_page_name')); ?></h2>
    </header>
    <div>
        <?php echo form_open_multipart(uri_string()); ?>
        <?php echo form_fieldset(); ?>
        <?php if (isset($profile_info)) : ?>
            <p><?php echo $profile_info; ?></p>
        <?php endif; ?>

        <p><?php echo lang('profile_instructions'); ?></p>

        <?php echo form_label(lang('profile_username'), 'profile_username'); ?>
        <?php echo form_input(array(
                'name' => 'profile_username',
                'id' => 'profile_username',
                'value' => set_value('profile_username') ? set_value('profile_username') : (isset($account->username) ? $account->username : ''),
                'maxlength' => '24'
            )); ?>
        <?php echo form_error('profile_username'); ?>
        <?php if (isset($profile_username_error)) : ?>
            <span class="field_error"><?php echo $profile_username_error; ?></span>
        <?php endif; ?>

        <?php echo form_label(lang('profile_picture'), 'profile_picture'); ?>
        <p>
            <?php if (isset($account_details->picture)) : ?>
                <img src="/resource/user/profile/<?php echo $account_details->picture; ?>?t=<?php echo md5(time()); ?>" alt="" />
                <?php echo anchor('account/account_profile/delete', lang('profile_delete_picture')); ?>
            <?php else : ?>
                <img src="/resource/img/default-picture.gif" alt="" />
            <?php endif; ?>
        </p>
        <?php echo form_upload(array(
            'name' => 'account_picture_upload',
            'id' => 'account_picture_upload'
        )); ?>
        <p><?php echo lang('profile_picture_guidelines'); ?></p>
        <?php if (isset($profile_picture_error)) : ?>
            <span class="field_error"><?php echo $profile_picture_error; ?></span>
        <?php endif; ?>

        <?php echo form_button(array(
            'type' => 'submit',
            'class' => 'button',
            'content' => lang('profile_save')
        )); ?>

        <?php echo form_fieldset_close(); ?>
        <?php echo form_close(); ?>
    </div>
</section>

