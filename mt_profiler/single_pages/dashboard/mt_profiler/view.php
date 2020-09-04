<?php
/**
 * @var \Concrete\Core\Form\Service\Form $form
 */
?>

<form method="post">
    <div class="form-group">
        <?php echo $form->label('active', t('Activate MT Profiler?')); ?>
        <?php echo $form->checkbox('active', '1', $active); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label('active', t('Display PHP info?')); ?>
        <?php echo $form->checkbox('php_info', '1', $phpInfo); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label('messages', t('Track messages?')); ?>
        <?php echo $form->checkbox('messages', '1', $messages); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label('time', t('Track time?')); ?>
        <?php echo $form->checkbox('time', '1', $time); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label('memory', t('Track memory?')); ?>
        <?php echo $form->checkbox('memory', '1', $memory); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label('request', t('See current request info?')); ?>
        <?php echo $form->checkbox('request', '1', $request); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label('session', t('See current session info?')); ?>
        <?php echo $form->checkbox('session', '1', $session); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label('monolog', t('View monolog?')); ?>
        <?php echo $form->checkbox('monolog', '1', $monolog); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label('db', t('View DB queries?')); ?>
        <?php echo $form->checkbox('db', '1', $db); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label('logs', t('View c5 Logs?')); ?>
        <?php echo $form->checkbox('logs', '1', $logs); ?>
        <small><?php echo t('This will only show the last 24 hours'); ?></small>
    </div>

    <div class="form-group">
        <?php echo $form->label('env', t('View Environment Information?')); ?>
        <?php echo $form->checkbox('env', '1', $env); ?>
    </div

    <div class="form-group">
        <button type="submit" class="btn btn-success"><?php echo t('Save'); ?></button>
    </div>
</form>
