<?php 
defined( '_JEXEC' ) or die( 'Restricted index access' );

$social_style    = $this->params->get('social_style');
$social_rss = $this->params->get('social_rss');
$social_twitter = $this->params->get('social_twitter');
$social_facebook = $this->params->get('social_facebook');
$social_googleplus = $this->params->get('social_googleplus');
$social_youtube = $this->params->get('social_youtube');
$social_pinterest = $this->params->get('social_pinterest');
$social_instagram = $this->params->get('social_instagram');
$social_dribbble = $this->params->get('social_dribbble');
$social_flickr = $this->params->get('social_flickr');
$social_skype = $this->params->get('social_skype');
$social_linkedin = $this->params->get('social_linkedin');
$social_vimeo = $this->params->get('social_vimeo');
$social_yahoo = $this->params->get('social_yahoo');
$social_tumblr = $this->params->get('social_tumblr');
$social_deviantart = $this->params->get('social_deviantart');
$social_delicious = $this->params->get('social_delicious');
$social_custom = $this->params->get('social_custom');
$document->addStyleDeclaration('
#socialmedia ul li a [class^="fa-"]::before, 
#socialmedia ul li a [class*=" fa-"]::before {
    color: '.$social_style.';
}');
?>
<div id="socialmedia">   
    <ul id="navigation">
        <?php if($social_rss != "") : ?><li class="social-rss"><a href="<?php echo $social_rss; ?>" target="_blank" title="RSS"><i class="fa fa-rss"></i><span>RSS</span></a></li><?php endif; ?>   
        <?php if($social_twitter != "") : ?><li class="social-twitter"><a href="<?php echo $social_twitter; ?>" target="_blank" title="Twitter"><i class="fa fa-twitter"></i><span>Twitter</span></a></li><?php endif; ?> 
        <?php if($social_facebook != "") : ?><li class="social-facebook"><a href="<?php echo $social_facebook; ?>" target="_blank" title="Facebook"><i class="fa fa-facebook"></i><span>Facebook</span></a></li><?php endif; ?> 
        <?php if($social_googleplus != "") : ?><li class="social-googleplus"><a href="<?php echo $social_googleplus; ?>" target="_blank" title="GooglePlus"><i class="fa fa-google-plus"></i><span>GooglePlus</span></a></li><?php endif; ?> 
        <?php if($social_youtube != "") : ?><li class="social-youtube"><a href="<?php echo $social_youtube; ?>" target="_blank" title="Youtube"><i class="fa fa-youtube"></i><span>Youtube</span></a></li><?php endif; ?> 
        <?php if($social_pinterest != "") : ?><li class="social-pinterest"><a href="<?php echo $social_pinterest; ?>" target="_blank" title="Pinterest"><i class="fa fa-pinterest"></i><span>Pinterest</span></a></li><?php endif; ?> 
        <?php if($social_instagram != "") : ?><li class="social-instagram"><a href="<?php echo $social_instagram; ?>" target="_blank" title="Instagram"><i class="fa fa-instagram"></i><span>Instagram</span></a></li><?php endif; ?> 
        <?php if($social_dribbble != "") : ?><li class="social-dribbble"><a href="<?php echo $social_dribbble; ?>" target="_blank" title="Dribbble"><i class="fa fa-dribbble"></i><span>Dribbble</span></a></li><?php endif; ?> 
        <?php if($social_flickr != "") : ?><li class="social-flickr"><a href="<?php echo $social_flickr; ?>" target="_blank" title="Flickr"><i class="fa fa-flickr"></i><span>Flickr</span></a></li><?php endif; ?> 
        <?php if($social_skype != "") : ?><li class="social-skype"><a href="<?php echo $social_skype; ?>" target="_blank" title="Skype"><i class="fa fa-skype"></i><span>Skype</span></a></li><?php endif; ?> 
        <?php if($social_linkedin != "") : ?><li class="social-linkedin"><a href="<?php echo $social_linkedin; ?>" target="_blank" title="LinkedIn"><i class="fa fa-linkedin"></i><span>LinkedIn</span></a></li><?php endif; ?> 
        <?php if($social_vimeo != "") : ?><li class="social-vimeo"><a href="<?php echo $social_vimeo; ?>" target="_blank" title="Vimeo"><i class="fa fa-vimeo"></i><span>Vimeo</span></a></li><?php endif; ?> 
        <?php if($social_yahoo != "") : ?><li class="social-yahoo"><a href="<?php echo $social_yahoo; ?>" target="_blank" title="Yahoo"><i class="fa fa-yahoo"></i><span>Yahoo</span></a></li><?php endif; ?> 
        <?php if($social_tumblr != "") : ?><li class="social-tumblr"><a href="<?php echo $social_tumblr; ?>" target="_blank" title="Tumblr"><i class="fa fa-tumblr"></i><span>Tumblr</span></a></li><?php endif; ?> 
        <?php if($social_deviantart != "") : ?><li class="social-deviantart"><a href="<?php echo $social_deviantart; ?>" target="_blank" title="DeviantArt"><i class="fa fa-deviantart"></i><span>DeviantArt</span></a></li><?php endif; ?> 
        <?php if($social_delicious != "") : ?><li class="social-delicious"><a href="<?php echo $social_delicious; ?>" target="_blank" title="Delicious"><i class="fa fa-delicious"><span>Delicious</span></a></li><?php endif; ?> 
        <?php $social_custom_arr = (array)$social_custom;
        if (!empty($social_custom_arr)) {
            foreach ($social_custom as $item) { 
            $document->addStyleDeclaration('.social-'.str_replace(' ', '', ($item->social_custom_name)).':hover {background-color: '.$item->social_custom_hover.';}');
            ?>
            <li class="social-<?php echo str_replace(' ', '', ($item->social_custom_name)); ?>">
                <a href="<?php echo $item->social_custom_url; ?>" target="_blank" title="<?php echo $item->social_custom_name; ?>">
                    <i class="fa <?php echo $item->social_custom_icon; ?>"></i><span><?php echo $item->social_custom_name; ?></span>
                </a>
            </li>
        <?php } 
        } ?>
    </ul>
</div>  