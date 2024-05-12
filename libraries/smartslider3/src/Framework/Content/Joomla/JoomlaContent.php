<?php

namespace Nextend\Framework\Content\Joomla;

use Joomla\Component\Content\Site\Helper\RouteHelper;
use ContentModelArticles;
use ContentHelperRoute;
use Joomla\CMS\Factory;
use Joomla\Component\Content\Administrator\Model\ArticlesModel;
use Joomla\CMS\Uri\Uri;
use Nextend\Framework\Content\AbstractPlatformContent;
use Nextend\Framework\Data\Data;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\SmartSlider3\Platform\Joomla\JoomlaShim;

class JoomlaContent extends AbstractPlatformContent {

    public function searchContent($keyword = '') {

        $result = array();

        JoomlaShim::loadComContentRoute();

        if (JoomlaShim::$isJoomla4) {
            $a = new ArticlesModel();
        } else {
            require_once(JPATH_ADMINISTRATOR . '/components/com_content/models/articles.php');
            $a = new ContentModelArticles();
        }

        $db    = $a->getDbo();
        $query = $db->getQuery(true);
        // Select the required fields from the table.
        $query->select($a->getState('list.select', 'a.id, a.title, a.introtext, a.images, a.alias, a.catid, a.language'));
        $query->from('#__content AS a');

        // Join over the categories.
        $query->select('c.title AS category_title')
              ->join('LEFT', '#__categories AS c ON c.id = a.catid');

        if (!empty($keyword)) {
            if (stripos($keyword, 'id:') === 0) {
                $query->where('a.id = ' . (int)substr($keyword, 3));
            } elseif (stripos($keyword, 'author:') === 0) {
                $keyword2 = $db->quote('%' . $db->escape(substr($keyword, 7), true) . '%');
                $query->where('(ua.name LIKE ' . $keyword2 . ' OR ua.username LIKE ' . $keyword2 . ')');
            } else {
                $keyword2 = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($keyword), true) . '%'));
                $query->where('(a.title LIKE ' . $keyword2 . ' OR a.alias LIKE ' . $keyword2 . ')');
            }
        }

        $db->setQuery($query, 0, 10);
        $articles = $db->loadAssocList();

        foreach ($articles as $article) {
            $images = new Data($article['images'], true);
            $image  = $images->get('image_fulltext', $images->get('image_intro', ''));
            if (!empty($image) && substr($image, 0, 2) != '//' && substr($image, 0, 4) != 'http') {
                $image = Uri::root(false) . $image;
            }

            $result[] = array(
                'title'       => $article['title'],
                'description' => $article['introtext'],
                'image'       => ResourceTranslator::urlToResource($image),
                'link'        => JoomlaShim::$isJoomla4 ? RouteHelper::getArticleRoute($article['id'], $article['catid'], $article['language']) : ContentHelperRoute::getArticleRoute($article['id'], $article['catid'], $article['language']),
                'info'        => $article['category_title']
            );
        }

        if (count($result) == 0 && !empty($keyword)) {
            /**
             * Try to show all post when no matches
             */
            return $this->searchContent();
        }

        return $result;
    }

    public function searchLink($keyword = '') {
        $result = array();

        JoomlaShim::loadComContentRoute();

        if (JoomlaShim::$isJoomla4) {
            $a = new ArticlesModel();
        } else {
            require_once(JPATH_ADMINISTRATOR . '/components/com_content/models/articles.php');
            $a = new ContentModelArticles();
        }

        $db    = $a->getDbo();
        $query = $db->getQuery(true);
        // Select the required fields from the table.
        $query->select($a->getState('list.select', 'a.id, a.title, a.alias, a.catid, a.language'));
        $query->from('#__content AS a');

        // Join over the categories.
        $query->select('c.title AS category_title')
              ->join('LEFT', '#__categories AS c ON c.id = a.catid');

        if (!empty($keyword)) {
            if (stripos($keyword, 'id:') === 0) {
                $query->where('a.id = ' . (int)substr($keyword, 3));
            } elseif (stripos($keyword, 'author:') === 0) {
                $keyword2 = $db->quote('%' . $db->escape(substr($keyword, 7), true) . '%');
                $query->where('(ua.name LIKE ' . $keyword2 . ' OR ua.username LIKE ' . $keyword2 . ')');
            } else {
                $keyword2 = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($keyword), true) . '%'));
                $query->where('(a.title LIKE ' . $keyword2 . ' OR a.alias LIKE ' . $keyword2 . ')');
            }
        }


        $db->setQuery($query, 0, 10);
        $articles = $db->loadAssocList();

        foreach ($articles as $article) {
            $result[] = array(
                'title' => $article['title'],
                'link'  => JoomlaShim::$isJoomla4 ? RouteHelper::getArticleRoute($article['id'], $article['catid'], $article['language']) : ContentHelperRoute::getArticleRoute($article['id'], $article['catid'], $article['language']),
                'info'  => $article['category_title']
            );
        }


        $db = Factory::getDbo();
        $db->setQuery('SELECT * FROM #__menu WHERE title LIKE ' . $db->quote('%' . str_replace(' ', '%', $db->escape(trim($keyword), true) . '%')) . ' AND client_id = 0 AND menutype != "" LIMIT 0,10');
        $menuItems = $db->loadAssocList();

        foreach ($menuItems as $mi) {
            $link = $mi['link'];
            if ($link && strpos($link, 'index.php') === 0) {
                $link = 'index.php?Itemid=' . $mi['id'];

                if (isset($mi['language'])) {
                    $link .= self::getLangauge($mi['language']);
                }
            }


            $result[] = array(
                'title' => $mi['title'] . ' [' . $mi['menutype'] . ']',
                'link'  => $link,
                'info'  => n2_('Menu item')
            );
        }
        if (count($result) == 0 && !empty($keyword)) {
            return $this->searchLink();
        }

        return $result;
    }

    private function getLangauge($language) {
        $db    = Factory::getDBO();
        $query = $db->getQuery(true);

        $link = '';

        if (is_object($query)) {
            $query->select('a.sef AS sef');
            $query->select('a.lang_code AS lang_code');
            $query->from('#__languages AS a');
            $db->setQuery($query);
            $langs = $db->loadObjectList();

            foreach ($langs as $lang) {
                if ($language == $lang->lang_code) {
                    $language = $lang->sef;
                    $link     .= '&lang=' . $language;
                }
            }
        }

        return $link;
    }
}