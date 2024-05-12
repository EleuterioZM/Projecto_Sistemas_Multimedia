<?php 
defined( '_JEXEC' ) or die( 'Restricted index access' );

$sidecola_width = $this->params->get('sidecola_width');
$sidecolb_width = $this->params->get('sidecolb_width');

$contenttop_auto = $this->params->get('contenttop_auto');
$contenttop_a_manual = $this->params->get('contenttop_a_manual');
$contenttop_b_manual = $this->params->get('contenttop_b_manual');
$contenttop_c_manual = $this->params->get('contenttop_c_manual');

$contentbottom_auto = $this->params->get('contentbottom_auto');
$contentbottom_a_manual = $this->params->get('contentbottom_a_manual');
$contentbottom_b_manual = $this->params->get('contentbottom_b_manual');
$contentbottom_c_manual = $this->params->get('contentbottom_c_manual');

$sidecola = false;
$sidecolb = false;
if ($this->countModules( 'sidecol-a') >= 1 || $this->countModules( 'sidecol-1a') >= 1 || $this->countModules( 'sidecol-1b') >= 1 || $this->countModules( 'sidecol-1c') >= 1) {
    $sidecola = true;
}
if ($this->countModules('sidecol-b') >= 1 || $this->countModules('sidecol-2a') >= 1 || $this->countModules('sidecol-2b') >= 1 || $this->countModules('sidecol-2c') >= 1) {
    $sidecolb = true;
}

if ($editing) {
    $sidecola = false;
    $sidecolb = false;
}

if ($sidecola || $sidecolb) {
    $contentwidth = '_remainder';
} else {
    $contentwidth = '_full';
}

if ($sidecola && $sidecolb) {
    $document->addStyleDeclaration('#content_remainder {width:'.(100 - ($sidecola_width + $sidecolb_width)).'% }');
} elseif ($sidecola) {
    $document->addStyleDeclaration('#content_remainder {width:'.(100 - ($sidecola_width)).'% }');
} else if ($sidecolb) {
    $document->addStyleDeclaration('#content_remainder {width:'.(100 - ($sidecolb_width)).'% }');
}

if ($this->params->get('column_layout') == 'SCOLA-SCOLB-COM') {
    $document->addStyleDeclaration('
        .sidecol_b {order: 0;}
        .content_remainder::after {display: none;}
        .sidecol_b::after {display: block;}
    ');
} elseif ($this->params->get('column_layout') == 'COM-SCOLA-SCOLB') {
    $document->addStyleDeclaration('
        .sidecol_a, .sidecol_b {order: 1;}
    ');
}

?>
<div id="main">
    <?php if (!$editing) { ?>
        <?php if ($this->countModules( 'sidecol-a' ) || $this->countModules( 'sidecol-1a' ) || $this->countModules( 'sidecol-1b' ) || $this->countModules( 'sidecol-1c' )) : ?>
        <div id="sidecol_a" class="side_margins sidecol_a">
            <div class="sidecol_block">
                <?php if ($this->countModules('sidecol-a')) { ?> 
                <div class="sidecol-a"><jdoc:include type="modules" name="sidecol-a" style="mod_standard" /></div>
                <?php } ?>
                <?php if ($this->countModules('sidecol-1a')) { ?> 
                <div class="sidecol-a sidecol-1a"><jdoc:include type="modules" name="sidecol-1a" style="mod_standard" /></div>
                <?php } ?>
                <?php if ($this->countModules('sidecol-1b')) { ?> 
                <div class="sidecol-a sidecol-1b"><jdoc:include type="modules" name="sidecol-1b" style="mod_standard" /></div>
                <?php } ?>
                <?php if ($this->countModules('sidecol-1c')) { ?> 
                <div class="sidecol-a sidecol-1c"><jdoc:include type="modules" name="sidecol-1c" style="mod_standard" /></div>
                <?php } ?>
            </div>
        </div>
        <?php endif; ?>
    <?php } ?>
       
    <?php if (!$editing) { ?>
        <?php if ($this->countModules( 'sidecol-b' ) || $this->countModules( 'sidecol-2a' ) || $this->countModules( 'sidecol-2b' ) || $this->countModules( 'sidecol-2c' )) : ?>    
        <div id="sidecol_b" class="side_margins sidecol_b">
            <div class="sidecol_block">
                <?php if ($this->countModules('sidecol-b')) { ?> 
                <div class="sidecol-b"><jdoc:include type="modules" name="sidecol-b" style="mod_standard" /></div>
                <?php } ?>
                <?php if ($this->countModules('sidecol-2a')) { ?> 
                <div class="sidecol-b sidecol-2a"><jdoc:include type="modules" name="sidecol-2a" style="mod_standard" /></div>
                <?php } ?>
                <?php if ($this->countModules('sidecol-2b')) { ?> 
                <div class="sidecol-b sidecol-2b"><jdoc:include type="modules" name="sidecol-2b" style="mod_standard" /></div>
                <?php } ?>
                <?php if ($this->countModules('sidecol-2c')) { ?> 
                <div class="sidecol-b sidecol-2c"><jdoc:include type="modules" name="sidecol-2c" style="mod_standard" /></div>
                <?php } ?>
            </div>
        </div>
        <?php endif; ?>
    <?php } ?>

    <div id="content<?php echo $contentwidth; ?>" class="side_margins content<?php echo $contentwidth; ?>">

        <?php if($this->params->get('contenttop_auto') != '1') : ?>
        <?php if ($this->countModules('contenttop-a') || $this->countModules('contenttop-b') || $this->countModules('contenttop-c')) { ?>
        <div class="wrapper_contenttop">
            <?php if ($this->countModules('contenttop-a')) { ?>
            <div class="contenttop" style="width:<?php echo $contenttop_width ?>;"><jdoc:include type="modules" name="contenttop-a" style="mod_standard"/></div><?php } ?>
            <?php if ($this->countModules('contenttop-b')) { ?>
            <div class="contenttop" style="width:<?php echo $contenttop_width ?>;"><jdoc:include type="modules" name="contenttop-b" style="mod_standard"/></div><?php } ?>
            <?php if ($this->countModules('contenttop-c')) { ?>
            <div class="contenttop" style="width:<?php echo $contenttop_width ?>;"><jdoc:include type="modules" name="contenttop-c" style="mod_standard"/></div><?php } ?>
            <div class="clear"></div>
            </div>
        <?php }?>
                    
        <?php else : ?>
                    
        <?php if ($this->countModules('contenttop-a') || $this->countModules('contenttop-b') || $this->countModules('contenttop-c')) { ?>
        <div class="wrapper_contenttop">
            <?php if ($this->countModules('contenttop-a')) { ?>
            <div class="contenttop" style="width:<?php echo $contenttop_a_manual ?>%;"><jdoc:include type="modules" name="contenttop-a" style="mod_standard"/></div><?php } ?>
            <?php if ($this->countModules('contenttop-b')) { ?>
            <div class="contenttop" style="width:<?php echo $contenttop_b_manual ?>%;"><jdoc:include type="modules" name="contenttop-b" style="mod_standard"/></div><?php } ?>
            <?php if ($this->countModules('contenttop-c')) { ?>
            <div class="contenttop" style="width:<?php echo $contenttop_c_manual ?>%;"><jdoc:include type="modules" name="contenttop-c" style="mod_standard"/></div><?php } ?>
        <div class="clear"></div>
        </div>
            <?php }?>
        <?php endif; ?>

            <div class="maincontent">
                    <div class="message">
                        <jdoc:include type="message" />
                    </div>
                <jdoc:include type="component" /> <div class="clear"></div>
            </div>

        <?php if($this->params->get('contentbottom_auto') != '1') : ?>
        <?php if ($this->countModules('contentbottom-a') || $this->countModules('contentbottom-b') || $this->countModules('contentbottom-c')) { ?>
        <div class="wrapper_contentbottom">
            <?php if ($this->countModules('contentbottom-a')) { ?>
            <div class="contentbottom" style="width:<?php echo $contentbottom_width ?>;"><jdoc:include type="modules" name="contentbottom-a" style="mod_standard"/></div><?php } ?>
            <?php if ($this->countModules('contentbottom-b')) { ?>
            <div class="contentbottom" style="width:<?php echo $contentbottom_width ?>;"><jdoc:include type="modules" name="contentbottom-b" style="mod_standard"/></div><?php } ?>
            <?php if ($this->countModules('contentbottom-c')) { ?>
            <div class="contentbottom" style="width:<?php echo $contentbottom_width ?>;"><jdoc:include type="modules" name="contentbottom-c" style="mod_standard"/></div><?php } ?>
            <div class="clear"></div>
            </div>
        <?php }?>
                    
        <?php else : ?>
                    
        <?php if ($this->countModules('contentbottom-a') || $this->countModules('contentbottom-b') || $this->countModules('contentbottom-c')) { ?>
        <div class="wrapper_contentbottom">
            <?php if ($this->countModules('contentbottom-a')) { ?>
            <div class="contentbottom" style="width:<?php echo $contentbottom_a_manual ?>%;"><jdoc:include type="modules" name="contentbottom-a" style="mod_standard"/></div><?php } ?>
            <?php if ($this->countModules('contentbottom-b')) { ?>
            <div class="contentbottom" style="width:<?php echo $contentbottom_b_manual ?>%;"><jdoc:include type="modules" name="contentbottom-b" style="mod_standard"/></div><?php } ?>
            <?php if ($this->countModules('contentbottom-c')) { ?>
            <div class="contentbottom" style="width:<?php echo $contentbottom_c_manual ?>%;"><jdoc:include type="modules" name="contentbottom-c" style="mod_standard"/></div><?php } ?>
        <div class="clear"></div>
        </div>
            <?php }?>
        <?php endif; ?>

    </div>
</div>
