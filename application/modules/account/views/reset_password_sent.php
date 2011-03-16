<section>
    <header>
        <h3><?php echo anchor(current_url(), lang('reset_password_sent_header')); ?></h3>
    </header>
    <div>
        <?php echo sprintf(lang('reset_password_sent_instructions'), anchor('account/forgot_password', lang('reset_password_resend_the_instructions'))); ?>
    </div>
</section>

