<section>
    <header>
        <h2><?php echo anchor(current_url(), lang('linked_page_name')); ?></h2>
    </header>
    <div>
        <h3><?php echo lang('linked_currently_linked_accounts'); ?></h3>
        <?php if ($this->session->flashdata('linked_info')) : ?>
            <p><?php echo $this->session->flashdata('linked_info'); ?></p>
        <?php endif; ?>
        <?php if ($num_of_linked_accounts == 0) : ?>
            <p><?php echo lang('linked_no_linked_accounts'); ?></p>
        <?php else :?>
            <!-- Begin Authorized Accounts !-->

            <?php if ($facebook_links) : ?>
                <!-- Begin Facebook !-->
                <?php foreach ($facebook_links as $facebook_link) : ?>
                    <img src="resource/img/auth_icons/facebook.png" alt="<?php echo lang('connect_facebook'); ?>" title="<?php echo lang('connect_facebook'); ?>" width="40" />
                    <?php echo lang('connect_facebook'); ?><br />
                    <?php echo anchor('http://facebook.com/profile.php?id='.$facebook_link->facebook_id, substr('http://facebook.com/profile.php?id='.$facebook_link->facebook_id, 0, 30).(strlen('http://facebook.com/profile.php?id='.$facebook_link->facebook_id) > 30 ? '...' : ''), array('target' => '_blank', 'title' => 'http://facebook.com/profile.php?id='.$facebook_link->facebook_id)); ?>
                    <?php if ($num_of_linked_accounts != 1) : ?>
                        <?php echo form_open(uri_string()); ?>
                        <?php echo form_fieldset(); ?>
                        <?php echo form_hidden('facebook_id', $facebook_link->facebook_id); ?>
                        <?php echo form_button(array(
                            'type' => 'submit',
                            'class' => 'button',
                            'content' => lang('linked_remove')
                        )); ?>
                        <?php echo form_fieldset_close(); ?>
                        <?php echo form_close(); ?>
                    <?php endif; ?>
                <?php endforeach; ?>
                <!-- End Facebook !-->
            <?php endif; ?>

            <?php if ($twitter_links) : ?>
                <!-- Begin Twitter !-->
                <?php foreach ($twitter_links as $twitter_link) : ?>
                    <img src="resource/img/auth_icons/twitter.png" alt="<?php echo lang('connect_twitter'); ?>" title="<?php echo lang('connect_twitter'); ?>" width="40" />
                    <?php echo lang('connect_twitter'); ?><br />
                    <?php echo anchor('http://twitter.com/'.$twitter_link->twitter->screen_name, substr('http://twitter.com/'.$twitter_link->twitter->screen_name, 0, 30).(strlen('http://twitter.com/'.$twitter_link->twitter->screen_name) > 30 ? '...' : ''), array('target' => '_blank', 'title' => 'http://twitter.com/'.$twitter_link->twitter->screen_name)); ?>
                    <?php if ($num_of_linked_accounts != 1) : ?>
                        <?php echo form_open(uri_string()); ?>
                        <?php echo form_fieldset(); ?>
                        <?php echo form_hidden('twitter_id', $twitter_link->twitter_id); ?>
                        <?php echo form_button(array(
                            'type' => 'submit',
                            'class' => 'button',
                            'content' => lang('linked_remove')
                        )); ?>
                        <?php echo form_fieldset_close(); ?>
                        <?php echo form_close(); ?>
                    <?php endif; ?>
                <?php endforeach; ?>
                <!-- End Twitter !-->
            <?php endif; ?>

            <?php if ($openid_links) : ?>
                <!-- Begin OpenID !-->
                <?php foreach ($openid_links as $openid_link) : ?>
                    <img src="resource/img/auth_icons/<?php echo $openid_link->provider; ?>.png" alt="<?php echo lang('connect_'.$openid_link->provider); ?>" width="40" />
                    <?php echo lang('connect_'.$openid_link->provider); ?><br />
                    <?php echo anchor($openid_link->openid, substr($openid_link->openid, 0, 30).(strlen($openid_link->openid) > 30 ? '...' : ''), array('target' => '_blank', 'title' => $openid_link->openid)); ?>
                     <?php if ($num_of_linked_accounts != 1) : ?>
                        <?php echo form_open(uri_string()); ?>
                        <?php echo form_fieldset(); ?>
                        <?php echo form_hidden('openid', $openid_link->openid); ?>
                        <?php echo form_button(array(
                            'type' => 'submit',
                            'class' => 'button',
                            'content' => lang('linked_remove')
                        )); ?>
                        <?php echo form_fieldset_close(); ?>
                        <?php echo form_close(); ?>
                    <?php endif; ?>
                <?php endforeach; ?>
                <!-- End OpenID !-->
            <?php endif; ?>

        <!-- End Authorized Accounts !-->
        <?php endif; ?>
    </div>
    <aside>
        <header>
            <h3><?php echo lang('linked_link_with_your_account_from'); ?></h3>
        </header>
        <?php if ($this->session->flashdata('linked_error')) : ?>
            <p><?php echo $this->session->flashdata('linked_error'); ?><p>
        <?php endif; ?>
        <ul class="third_party">
            <?php foreach($this->config->item('third_party_auth_providers') as $provider) : ?>
            <li class="third_party <?php echo $provider; ?>">
                <?php echo anchor('account/connect_'.$provider, lang('connect_'.$provider),
                array('title'=>sprintf(lang('connect_with_x'), lang('connect_'.$provider)))); ?>
            </li>
            <?php endforeach; ?>
        </ul>
    </aside>
</section>

