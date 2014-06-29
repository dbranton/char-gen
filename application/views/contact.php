<p>E-mail me any issues you find with the website</p>
<div id="contact-form">
    <?php echo form_open('email/send'); ?>
    <?php
        $email_data = array(
            'name' => 'email',
            'id' => 'email',
            'value' => set_value('name')
        );
    ?>
    <p><label for="email">Email: </label><?php echo form_input($email_data); ?></p>
    <p><label for="comments">Comments:</label>
        <input type="text" name="comments" id="comments" value="<?php echo set_value('email');?>"></p>
    <p><?php echo form_submit('submit', 'Submit'); ?></p>
    <?php echo form_close(); ?>
    <?php echo validation_errors('<p class="error">'); ?>
</div>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <fieldset>
        <label for="email">E-mail:</label>
        <input type="text" id="email" name="email" value="<?php if (!empty($email)) echo $email; ?>" />
        <label for="feedback">Comments:</label>
        <textarea id="feedback" name="feedback"><?php if (!empty($feedback)){ echo $feedback; }?></textarea>
    </fieldset>
    <input type="submit" value="Submit" name="submit" />
</form>

