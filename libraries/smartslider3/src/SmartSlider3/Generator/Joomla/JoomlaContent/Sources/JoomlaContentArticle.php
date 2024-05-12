<?php

namespace Nextend\SmartSlider3\Generator\Joomla\JoomlaContent\Sources;

use Joomla\Component\Content\Site\Helper\RouteHelper;
use DateTime;
use DateTimeZone;
use ContentHelperRoute;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Nextend\Framework\Database\Database;
use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\MixedField\GeneratorOrder;
use Nextend\Framework\Form\Element\OnOff;
use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\Form\Element\Select\Filter;
use Nextend\Framework\Form\Element\Text;
use Nextend\Framework\Form\Element\Text\Number;
use Nextend\Framework\Form\Element\Textarea;
use Nextend\Framework\Parser\Common;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\Framework\Url\Url;
use Nextend\SmartSlider3\Generator\AbstractGenerator;
use Nextend\SmartSlider3\Generator\Joomla\JoomlaContent\Elements\JoomlaContentAccessLevels;
use Nextend\SmartSlider3\Generator\Joomla\JoomlaContent\Elements\JoomlaContentCategories;
use Nextend\SmartSlider3\Generator\Joomla\JoomlaContent\Elements\JoomlaContentTags;
use Nextend\SmartSlider3\Platform\Joomla\ImageFallback;
use Nextend\SmartSlider3\Platform\Joomla\JoomlaShim;
use Nextend\SmartSlider3\Slider\Slider;
use stdClass;


JoomlaShim::loadComContentRoute();


class JoomlaContentArticle extends AbstractGenerator {

    protected $layout = 'article';

    public function getDescription() {
        return n2_('Creates slides from your Joomla articles in the selected categories.');
    }

    public function renderFields($container) {
        parent::renderFields($container);

        $filterGroup = new ContainerTable($container, 'filter', n2_('Filter'));

        $source = $filterGroup->createRow('source-row');
        new JoomlaContentCategories($source, 'sourcecategories', n2_('Category'), 0, array(
            'isMultiple' => true
        ));
        new JoomlaContentTags($source, 'sourcetags', n2_('Tags'), 0, array(
            'isMultiple' => true
        ));
        new JoomlaContentAccessLevels($source, 'sourceaccesslevels', n2_('Access level'), 0, array(
            'isMultiple' => true
        ));

        $limit = $filterGroup->createRow('limit-row');

        new Filter($limit, 'sourcefeatured', n2_('Featured'), 0);
        new Number($limit, 'sourceuserid', n2_('User ID'), '', array(
            'tipLabel'       => n2_('Created by'),
            'tipDescription' => n2_('The ID number of the article\'s author. Only one number is accepted.'),
        ));
        new Text($limit, 'sourcearticleids', n2_('Included article IDs'), '', array(
            'tipLabel'       => n2_('Included article IDs'),
            'tipDescription' => n2_('Write down article ID numbers separated by commas, to include them in the result. For example: 1,12,25'),
        ));
        new Text($limit, 'sourcearticleidsexcluded', n2_('Excluded article IDs'), '', array(
            'tipLabel'       => n2_('Excluded article IDs'),
            'tipDescription' => n2_('Write down article ID numbers separated by commas, to exclude them from the result. For example: 2,14,27'),
        ));
        new Text($limit, 'sourcelanguage', n2_('Language'), '*', array(
            'tipLabel'       => n2_('Language'),
            'tipDescription' => n2_('The language code of your articles. Multiple language codes should be separated by commas, for example: en-GB,hu-HU,es-ES'),
            'tipLink'        => 'https://smartslider.helpscoutdocs.com/article/1879-language-filters'
        ));

        $variables = $filterGroup->createRow('variables-row');

        new OnOff($variables, 'sourcefields', n2_('Fields'), 0, array(
            'tipLabel'       => n2_('Extra variables'),
            'tipDescription' => n2_('Turn on these options to generate more variables for the slides.'),
            'tipLink'        => 'https://smartslider.helpscoutdocs.com/article/1864-joomla-articles-generator#fields'
        ));
        new OnOff($variables, 'sourcetagvariables', n2_('Tags'), 0, array(
            'tipLabel'       => n2_('Extra variables'),
            'tipDescription' => n2_('Turn on these options to generate more variables for the slides.'),
            'tipLink'        => 'https://smartslider.helpscoutdocs.com/article/1864-joomla-articles-generator#tags-19'
        ));
        new Select($variables, 'removeshortcodes', n2_('Remove shortcodes'), '0', array(
            'isMultiple'     => true,
            'size'           => 5,
            'options'        => array(
                '0' => n2_('All'),
                '1' => '{shortcode}example{/shortcode}',
                '2' => '{shortcode}',
                '3' => '[shortcode]example[/shortcode]',
                '4' => '[shortcode]'
            ),
            'tipLabel'       => n2_('Remove shortcodes'),
            'tipDescription' => n2_('Remove shortcodes from article description with the following patterns.')
        ));

        $date = $filterGroup->createRow('date-row');
        new Text($date, 'sourcedateformat', n2_('Date format'), 'm-d-Y');
        new Text($date, 'sourcetimeformat', n2_('Time format'), 'G:i');
        new Textarea($date, 'sourcetranslatedate', n2_('Translate date and time'), 'January->January||February->February||March->March', array(
            'width'  => 300,
            'height' => 100

        ));

        $orderGroup = new ContainerTable($container, 'order-group', n2_('Order'));
        $order      = $orderGroup->createRow('order-row');
        new GeneratorOrder($order, 'joomlaorder', 'con.created|*|desc', array(
            'options' => array(
                ''                 => n2_('None'),
                'con.title'        => n2_('Title'),
                'cat_title'        => n2_('Category'),
                'created_by_alias' => n2_('User name'),
                'con.featured'     => n2_('Featured'),
                'con.ordering'     => n2_('Ordering'),
                'con.hits'         => n2_('Hits'),
                'con.created'      => n2_('Creation time'),
                'con.modified'     => n2_('Modification time'),
                'con.publish_up'   => n2_('Publish time'),
                'cf.ordering'      => n2_('Featured article ordering')
            )
        ));
    }

    public function datify($date, $format) {
        if (empty($date) || $date == '0000-00-00 00:00:00') {
            return '';
        } else {
            $config   = Factory::getConfig();
            $timezone = new DateTimeZone($config->get('offset'));
            $offset   = $timezone->getOffset(new DateTime);

            $result = date($format, strtotime($date) + $offset);

            return $result;
        }
    }

    private function translate($from, $translate) {
        if (!empty($translate) && !empty($from)) {
            foreach ($translate as $key => $value) {
                $from = str_replace($key, $value, $from);
            }
        }

        return $from;
    }

    private function removeShortcodes($content) {
        $selection = $this->data->get('removeshortcodes', 1);
        if ($selection !== '') {
            $shortcodes = explode('||', $selection);

            if (in_array(0, $shortcodes) || in_array(1, $shortcodes)) {
                $content = preg_replace('/{[^{}]*?}[^{}]*?{\/.*?}/', '', $content);
            }
            if (in_array(0, $shortcodes) || in_array(2, $shortcodes)) {
                $content = preg_replace('/{.*?}/', '', $content);
            }
            if (in_array(0, $shortcodes) || in_array(3, $shortcodes)) {
                $content = preg_replace('/\[[^\[\]]*?][^\[\]]*?\[\/.*?]/', '', $content);
            }
            if (in_array(0, $shortcodes) || in_array(4, $shortcodes)) {
                $content = preg_replace('/\[.*?]/', '', $content);
            }
        }

        return $content;
    }

    protected function _getData($count, $startIndex) {
        $categories = array_map('intval', explode('||', $this->data->get('sourcecategories', '')));
        $tags       = array_map('intval', explode('||', $this->data->get('sourcetags', '0')));

        $query = 'SELECT ';
        $query .= 'con.id, ';
        $query .= 'con.title, ';
        $query .= 'con.alias, ';
        $query .= 'con.introtext, ';
        $query .= 'con.fulltext, ';
        $query .= 'con.created, ';
        $query .= 'con.catid, ';
        $query .= 'cat.title AS cat_title, ';
        $query .= 'cat.alias AS cat_alias, ';
        $query .= 'con.created_by, con.state, con.metadata, ';
        $query .= 'con.created_by_alias AS con_created_by_alias, ';
        $query .= 'usr.name AS created_by_alias, ';
        $query .= 'con.images, ';
        $query .= 'con.publish_up, ';
        $query .= 'con.publish_down, ';
        $query .= 'con.urls, ';
        $query .= 'con.attribs ';

        $query .= 'FROM #__content AS con ';

        $query .= 'LEFT JOIN #__users AS usr ON usr.id = con.created_by ';

        $query .= 'LEFT JOIN #__categories AS cat ON cat.id = con.catid ';

        $query .= 'LEFT JOIN #__content_frontpage AS cf ON cf.content_id = con.id ';

        $jNow  = Factory::getDate();
        $now   = $jNow->toSql();
        $where = array(
            'con.state = 1 ',
            "(con.publish_up IS NULL OR con.publish_up = '0000-00-00 00:00:00' OR con.publish_up < '" . $now . "') AND (con.publish_down IS NULL OR con.publish_down = '0000-00-00 00:00:00' OR con.publish_down > '" . $now . "') "
        );

        if (!in_array(0, $categories)) {
            $where[] = 'con.catid IN (' . implode(',', $categories) . ') ';
        }

        if (!in_array(0, $tags)) {
            $where[] = 'con.id IN (SELECT content_item_id FROM #__contentitem_tag_map WHERE type_alias = \'com_content.article\' AND tag_id IN (' . implode(',', $tags) . ')) ';
        }

        $sourceUserID = intval($this->data->get('sourceuserid', ''));
        if ($sourceUserID) {
            $where[] = 'con.created_by = ' . $sourceUserID . ' ';
        }

        switch ($this->data->get('sourcefeatured', 0)) {
            case 1:
                $where[] = 'con.featured = 1 ';
                break;
            case -1:
                $where[] = 'con.featured = 0 ';
                break;
        }
        $language = explode(",", $this->data->get('sourcelanguage', '*'));
        if (!empty($language[0]) && $language[0] != '*') {
            $where[] = 'con.language IN (' . implode(",", Database::quote($language)) . ') ';
        }

        $articleIds = $this->data->get('sourcearticleids', '');
        if (!empty($articleIds)) {
            $where[] = 'con.id IN (' . preg_replace("/[^0-9,]/", "", $articleIds) . ') ';
        }

        $articleIdsExcluded = $this->data->get('sourcearticleidsexcluded', '');
        if (!empty($articleIdsExcluded)) {
            $where[] = 'con.id NOT IN (' . preg_replace("/[^0-9,]/", "", $articleIdsExcluded) . ') ';
        }

        $accessLevels = explode('||', $this->data->get('sourceaccesslevels', '*'));
        if (!in_array(0, $accessLevels)) {
            $where[] = 'con.access IN (' . implode(",", $accessLevels) . ')';
        }

        if (count($where) > 0) {
            $query .= 'WHERE ' . implode(' AND ', $where) . ' ';
        }

        $order = Common::parse($this->data->get('joomlaorder', 'con.created|*|desc'));
        if ($order[0]) {
            $query .= 'ORDER BY ' . $order[0] . ' ' . $order[1] . ' ';
        }

        $query .= 'LIMIT ' . $startIndex . ', ' . $count;

        $result = Database::queryAll($query);

        if (empty($result)) {
            return null;
        }

        $sourceTranslate = $this->data->get('sourcetranslatedate', '');
        $translateValue  = explode('||', $sourceTranslate);
        $translate       = array();
        if ($sourceTranslate != 'January->January||February->February||March->March' && !empty($translateValue)) {
            foreach ($translateValue as $tv) {
                $translateArray = explode('->', $tv);
                if (!empty($translateArray) && count($translateArray) == 2) {
                    $translate[$translateArray[0]] = $translateArray[1];
                }
            }
        }

        PluginHelper::importPlugin('content');
        $uri = Url::getBaseUri();

        $data    = array();
        $idArray = array();
        for ($i = 0; $i < count($result); $i++) {
            $idArray[$i] = $result[$i]['id'];
            $r           = array(
                'title' => $result[$i]['title']
            );

            $article       = new stdClass();
            $article->text = $this->removeShortcodes(Slider::removeShortcode($result[$i]['introtext']));
            $_p            = array();

            JoomlaShim::triggerOnContentPrepare(array(
                'com_smartslider3',
                &$article,
                &$_p,
                0
            ));
            if (!empty($article->text)) {
                $r['description'] = $article->text;
            }

            $article->text = $result[$i]['fulltext'];
            $_p            = array();
            JoomlaShim::triggerOnContentPrepare(array(
                'com_smartslider3',
                &$article,
                &$_p,
                0
            ));
            if (!empty($article->text)) {
                $result[$i]['fulltext'] = $article->text;
                if (!isset($r['description'])) {
                    $r['description'] = $result[$i]['fulltext'];
                } else {
                    $r['fulltext'] = $result[$i]['fulltext'];
                }
            }

            $images = (array)json_decode($result[$i]['images'], true);

            $r['image'] = $r['thumbnail'] = ImageFallback::fallback(array(
                @$images['image_intro'],
                @$images['image_fulltext']
            ), array(
                @$r['description']
            ));

            $r += array(
                'url'               => JoomlaShim::$isJoomla4 ? RouteHelper::getArticleRoute($result[$i]['id'] . ':' . $result[$i]['alias'], $result[$i]['catid'] . ':' . $result[$i]['cat_alias']) : ContentHelperRoute::getArticleRoute($result[$i]['id'] . ':' . $result[$i]['alias'], $result[$i]['catid'] . ':' . $result[$i]['cat_alias']),
                'url_label'         => n2_('View article'),
                'category_list_url' => 'index.php?option=com_content&view=category&id=' . $result[$i]['catid'],
                'category_blog_url' => 'index.php?option=com_content&view=category&layout=blog&id=' . $result[$i]['catid'],
                'fulltext_image'    => ImageFallback::fallback(array(@$images['image_fulltext'])),
                'category_title'    => $result[$i]['cat_title'],
                'created_by'        => $result[$i]['created_by_alias'],
                'con_created_by'    => $result[$i]['con_created_by_alias'],
                'id'                => $result[$i]['id'],
                'created_date'      => $this->translate($this->datify($result[$i]['created'], $this->data->get('sourcedateformat', 'm-d-Y')), $translate),
                'created_time'      => $this->translate($this->datify($result[$i]['created'], $this->data->get('sourcetimeformat', 'G:i')), $translate),
                'publish_up_date'   => $this->translate($this->datify($result[$i]['publish_up'], $this->data->get('sourcedateformat', 'm-d-Y')), $translate),
                'publish_up_time'   => $this->translate($this->datify($result[$i]['publish_up'], $this->data->get('sourcetimeformat', 'G:i')), $translate),
                'publish_down_date' => $this->translate($this->datify($result[$i]['publish_down'], $this->data->get('sourcedateformat', 'm-d-Y')), $translate),
                'publish_down_time' => $this->translate($this->datify($result[$i]['publish_down'], $this->data->get('sourcetimeformat', 'G:i')), $translate),
            );

            if (!empty($images)) {
                foreach ($images as $name => $value) {
                    if (!empty($value)) {
                        $image = ImageFallback::fallback(array($value));
                        if (!empty($image)) {
                            $r[$name] = $image;
                        } else {
                            $r[$name] = $value;
                        }
                    }
                }
            }

            $urls = json_decode($result[$i]['urls'], true);
            if (!empty($urls['urla'])) {
                $r['urla']     = $urls['urla'];
                $r['urlatext'] = $urls['urlatext'];
            }
            if (!empty($urls['urlb'])) {
                $r['urlb']     = $urls['urlb'];
                $r['urlbtext'] = $urls['urlbtext'];
            }
            if (!empty($urls['urlc'])) {
                $r['urlc']     = $urls['urlc'];
                $r['urlctext'] = $urls['urlctext'];
            }

            $metadata = json_decode($result[$i]['metadata']);
            foreach ($metadata as $metakey => $metavalue) {
                $r[$metakey] = $metavalue;
            }

            $attribs = (array)json_decode($result[$i]['attribs'], true);
            foreach ($attribs as $attrib => $value) {
                if (!empty($value) && is_string($value)) {
                    $r[$attrib] = $value;
                }
            }

            if (isset($r['helix_ultimate_image'])) {
                $r['spfeatured_image'] = $r['helix_ultimate_image'] = '$/' . $r['helix_ultimate_image'];
            }

            if (isset($r['helix_ultimate_gallery'])) {
                $gallery = (array)json_decode($r['helix_ultimate_gallery'], true);
                for ($j = 0; $j < count($gallery["helix_ultimate_gallery_images"]); $j++) {
                    $r['helix_ultimate_gallery_images_' . $j] = $r['spgallery_' . $j] = '$/' . $gallery["helix_ultimate_gallery_images"][$j];
                }

            }

            $data[] = $r;
        }

        if (!empty($idArray)) {
            if ($this->data->get('sourcetagvariables', 0)) {
                $query  = 'SELECT t.title, c.content_item_id  FROM #__tags AS t
				  LEFT JOIN #__contentitem_tag_map AS c ON t.id = c.tag_id
				  WHERE t.id IN (SELECT tag_id FROM #__contentitem_tag_map WHERE type_alias = \'com_content.article\' AND content_item_id IN (' . implode(',', $idArray) . '))';
                $result = Database::queryAll($query);

                if (!empty($result)) {
                    $tags     = array();
                    $articles = array();
                    foreach ($result as $r) {
                        $tags[$r['content_item_id']][] = $r['title'];
                        $articles[]                    = $r['content_item_id'];

                    }
                    for ($i = 0; $i < count($data); $i++) {
                        if (in_array($data[$i]['id'], $articles)) {
                            $j = 1;
                            foreach ($tags[$data[$i]['id']] as $tag) {
                                $data[$i]['tag' . $j] = $tag;
                                $j++;
                            }
                        }
                    }
                }
            }

            if ($this->data->get('sourcefields', 0)) {
                $query  = "SELECT fv.value, fv.item_id, f.name, f.type FROM #__fields_values AS fv LEFT JOIN #__fields AS f ON fv.field_id = f.id WHERE fv.item_id IN (" . implode(',', $idArray) . ")";
                $result = Database::queryAll($query);
                if (!empty($result)) {
                    $AllResult = array();
                    foreach ($result as $r) {
                        if ($r['type'] == 'media') {
                            $valueParts = json_decode($r['value']);
                            if (isset($valueParts->imagefile)) {
                                $r['value']                                         = ImageFallback::fallback(array($valueParts->imagefile));
                                $AllResult[$r['item_id']][$r['name'] . '_alt_text'] = $valueParts->alt_text;
                            } else {
                                $r['value'] = ResourceTranslator::urlToResource($uri . "/" . $r["value"]);
                            }
                        }

                        $AllResult[$r['item_id']][$r['name']] = $r['value'];
                    }

                    for ($i = 0; $i < count($data); $i++) {
                        if (isset($AllResult[$data[$i]['id']])) {
                            foreach ($AllResult[$data[$i]['id']] as $key => $value) {
                                $key            = preg_replace('/[^a-zA-Z0-9_\x7f-\xff]*/', '', $key);
                                $data[$i][$key] = $value;
                            }
                        }
                    }
                }
            }
        }

        return $data;
    }

}