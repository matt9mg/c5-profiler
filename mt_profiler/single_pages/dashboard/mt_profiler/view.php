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
        <button type="submit" class="btn btn-success"><?php echo t('Save'); ?></button>
    </div>
</form>
