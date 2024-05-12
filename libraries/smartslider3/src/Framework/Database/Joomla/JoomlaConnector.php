<?php

namespace Nextend\Framework\Database\Joomla;

use Exception;
use Joomla\Database\DatabaseDriver;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Nextend\Framework\Database\AbstractPlatformConnector;
use Nextend\Framework\Notification\Notification;

class JoomlaConnector extends AbstractPlatformConnector {

    /**
     * @var DatabaseDriver
     */
    private $db;


    public function __construct() {
        $this->db      = Factory::getDbo();
        $this->_prefix = $this->db->getPrefix();

        JoomlaConnectorTable::init($this, $this->db);
    }

    public function insertId() {
        return $this->db->insertid();
    }

    public function query($query, $attributes = false) {
        if ($attributes) {
            foreach ($attributes as $key => $value) {
                $replaceTo = is_numeric($value) ? $value : $this->db->quote($value);
                $query     = str_replace($key, $replaceTo, $query);
            }
        }

        return $this->setQuery($query, 'execute');
    }


    public function queryRow($query, $attributes = false) {
        if ($attributes) {
            foreach ($attributes as $key => $value) {
                $replaceTo = is_numeric($value) ? $value : $this->db->quote($value);
                $query     = str_replace($key, $replaceTo, $query);
            }
        }

        return $this->setQuery($query, 'loadAssoc');
    }

    public function queryAll($query, $attributes = false, $type = "assoc", $key = null) {
        if ($attributes) {
            foreach ($attributes as $key => $value) {
                $replaceTo = is_numeric($value) ? $value : $this->db->quote($value);
                $query     = str_replace($key, $replaceTo, $query);
            }
        }

        if ($type == "assoc") {
            return $this->setQuery($query, 'loadAssocList');
        } else {
            return $this->setQuery($query, 'loadObjectList');
        }
    }

    /**
     * @param string $text
     * @param bool   $escape
     *
     * @return string
     */
    public function quote($text, $escape = true) {
        return $this->db->quote($text, $escape);
    }

    /**
     * @param string $name
     * @param null   $as
     *
     * @return mixed
     */
    public function quoteName($name, $as = null) {
        return $this->db->quoteName($name, $as);
    }

    public function getCharsetCollate() {

        if ($this->db->hasUTF8mb4Support()) {

            return 'DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci';
        }

        return 'DEFAULT CHARSET=utf8 DEFAULT COLLATE=utf8_unicode_ci';
    }

    public function setQuery($query, $return) {
        try {
            $nextend = $this->db->setQuery($query);

            switch ($return) {
                case 'execute':
                    return $this->db->execute();
                case 'loadAssoc':
                    return $nextend->loadAssoc();
                case 'loadAssocList':
                    return $nextend->loadAssocList();
                case 'loadObjectList':
                    return $nextend->loadObjectList();
                default:
                    return '';
            }

        } catch (Exception $e) {
            $currentUrl = Uri::getInstance();
            $currentUrl->setVar('repairss3', 1);

            $message = array(
                n2_('Unexpected database error.'),
                '',
                '<a href="' . $currentUrl . '" class="n2_button n2_button--big n2_button--blue">' . n2_('Try to repair database') . '</a>',
                '',
                '<b>' . $e->getMessage() . '</b>',
                $query
            );
            Notification::error(implode('<br>', $message), array(
                'wide' => true
            ));

            return '';
        }
    }
}