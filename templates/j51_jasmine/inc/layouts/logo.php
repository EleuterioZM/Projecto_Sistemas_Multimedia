<?php 
defined( '_JEXEC' ) or die( 'Restricted index access' );

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

$sitename = htmlspecialchars($app->get('sitename'), ENT_QUOTES, 'UTF-8');
$logo = HTMLHelper::_('image', 'logo.png', $sitename, ['class' => 'logo-image primary-logo-image'], true, 0);

?>

<div id="logo" class="logo">
  <a href="<?php echo $this->baseurl ?>" title="<?php echo $sitename; ?>">
  <?php if($this->params->get('logoImage') == '1') : ?>    
    <?php if($this->params->get('logoimagefile') == '') : ?>
      <?php echo $logo; ?>
    <?php elseif($this->params->get('logoimagefile') != '') : ?>
      <img class="logo-image primary-logo-image" src="<?php echo Uri::root(true); ?>/<?php echo $logoimagefile; ?>" alt="Logo" />
    <?php endif; ?>
    <?php if($this->params->get('mobilelogoimagefile') != '')  { ?>
      <img class="logo-image mobile-logo-image" src="<?php echo Uri::root(true); ?>/<?php echo $this->params->get('mobilelogoimagefile'); ?>" alt="Mobile Logo" />
    <?php } ?>
    <?php else : ?>
    <div class="logo-text"><?php echo $this->params->get('logoText'); ?></div>
    <div class="logo-slogan"><?php echo $this->params->get('sloganText'); ?></div>
  <?php endif; ?> 
  </a> 
</div>
