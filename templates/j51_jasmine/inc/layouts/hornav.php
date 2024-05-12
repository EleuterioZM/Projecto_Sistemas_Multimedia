<?php 
defined( '_JEXEC' ) or die( 'Restricted index access' );
 ?>

<?php if($this->params->get('hornavPosition') == '1') : ?>
    <div class="hornav">
        <jdoc:include type="modules" name="hornav" />
    </div>
<?php else : ?>
    <div class="hornav">
        <?php echo $hornav; ?>

    </div>
<?php endif; ?>
