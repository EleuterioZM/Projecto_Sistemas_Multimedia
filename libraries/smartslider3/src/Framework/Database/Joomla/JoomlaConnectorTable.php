<?php


namespace Nextend\Framework\Database\Joomla;


use Exception;
use Joomla\Database\DatabaseDriver;
use Joomla\CMS\Uri\Uri;
use Nextend\Framework\Database\AbstractPlatformConnector;
use Nextend\Framework\Database\AbstractPlatformConnectorTable;
use Nextend\Framework\Notification\Notification;
use stdClass;

class JoomlaConnectorTable extends AbstractPlatformConnectorTable {

    /** @var DatabaseDriver */
    protected static $db;

    /**
     * @param AbstractPlatformConnector $connector
     * @param DatabaseDriver           $db
     */
    public static function init($connector, $db) {
        self::$connector = $connector;
        self::$db        = $db;
    }

    public function findByPk($primaryKey) {
        $query = self::$db->getQuery(true);

        $query->select('*');
        $query->from(self::$connector->quoteName($this->tableName));
        $query->where(self::$connector->quoteName($this->primaryKeyColumn) . ' = ' . (is_numeric($primaryKey) ? $primaryKey : self::$db->quote($primaryKey)));

        return $this->setQuery($query, 'loadAssoc');
    }


    public function findByAttributes(array $attributes, $fields = false, $order = false) {
        $query = self::$db->getQuery(true);
        if ($fields) {
            $query->select(self::$connector->quoteName($fields));
        } else {
            $query->select(array('*'));
        }
        $query->from(self::$connector->quoteName($this->tableName));
        foreach ($attributes as $key => $val) {
            $query->where(self::$connector->quoteName($key) . ' = ' . (is_numeric($val) ? $val : self::$connector->quote($val)));
        }

        if ($order) {
            $query->order($order);
        }

        return $this->setQuery($query, 'loadAssoc');
    }

    public function findAll($order = false) {
        $query = self::$db->getQuery(true);
        $query->select('*');
        $query->from(self::$db->quoteName($this->tableName));

        if ($order) {
            $query->order($order);
        }

        return $this->setQuery($query, 'loadAssocList');
    }

    public function findAllByAttributes(array $attributes, $fields = false, $order = false) {
        $query = self::$db->getQuery(true);
        if ($fields) {
            $query->select(self::$connector->quoteName($fields));
        } else {
            $query->select('*');
        }
        $query->from(self::$db->quoteName($this->tableName));
        foreach ($attributes as $key => $val) {
            $query->where(self::$db->quoteName($key) . ' = ' . (is_numeric($val) ? $val : self::$db->quote($val)));
        }

        if ($order) {
            $query->order($order);
        }

        return $this->setQuery($query, 'loadAssocList');
    }

    public function insert(array $attributes) {
        $object = new stdClass();
        foreach ($attributes as $key => $value) {
            $object->$key = $value;
        }

        // Insert the object into the user profile table.
        try {
            return self::$db->insertObject($this->tableName, $object);
        } catch (Exception $e) {
            return false;
        }
    }

    public function insertId() {
        return self::$db->insertid();
    }

    public function update(array $attributes, array $conditions) {
        $query = self::$db->getQuery(true);

        $fields = array();

        foreach ($attributes as $akey => $avalue) {
            $fields[] = self::$connector->quoteName($akey) . ' = ' . (is_numeric($avalue) ? intval($avalue) : self::$connector->quote($avalue));
        }

        $where = array();
        foreach ($conditions as $ckey => $cvalue) {
            $where[] = self::$connector->quoteName($ckey) . ' = ' . (is_numeric($cvalue) ? intval($cvalue) : self::$connector->quote($cvalue));
        }

        $query->update(self::$connector->quoteName($this->tableName))
              ->set($fields)
              ->where($where);

        return $this->setQuery($query);
    }

    public function updateByPk($primaryKey, array $attributes) {
        $query = self::$db->getQuery(true);

        $fields = array();

        foreach ($attributes as $akey => $avalue) {
            $fields[] = self::$connector->quoteName($akey) . ' = ' . (is_numeric($avalue) ? intval($avalue) : self::$connector->quote($avalue));
        }

        $conditions = self::$connector->quoteName($this->primaryKeyColumn) . ' = ' . (is_numeric($primaryKey) ? $primaryKey : self::$connector->quote($primaryKey));

        $query->update(self::$connector->quoteName($this->tableName))
              ->set($fields)
              ->where($conditions);

        return $this->setQuery($query);
    }

    public function deleteByPk($primaryKey) {
        $query = self::$db->getQuery(true);

        $conditions = array(self::$connector->quoteName($this->primaryKeyColumn) . ' = ' . (is_numeric($primaryKey) ? $primaryKey : self::$connector->quote($primaryKey)));

        $query->delete(self::$connector->quoteName($this->tableName));
        $query->where($conditions);

        return $this->setQuery($query);
    }

    public function deleteByAttributes(array $conditions) {
        $query = self::$db->getQuery(true);

        $where = array();
        foreach ($conditions as $ckey => $cvalue) {
            $where[] = self::$connector->quoteName($ckey) . ' = ' . (is_numeric($cvalue) ? intval($cvalue) : self::$connector->quote($cvalue));
        }

        $query->delete(self::$connector->quoteName($this->tableName));
        $query->where($where);

        return $this->setQuery($query);
    }

    public function setQuery($query, $return = 'execute') {
        try {
            self::$db->setQuery($query);

            switch ($return) {
                case 'execute':
                    return self::$db->execute();
                case 'loadAssocList':
                    return self::$db->loadAssocList();
                case 'loadAssoc':
                    return self::$db->loadAssoc();
                default:
                    return '';
            }

        } catch (Exception $e) {
            $currentUrl = Uri::getInstance();
            $currentUrl->setVar('repairss3', 1);

            $message = array(
                'Unexpected database error.',
                '',
                '<a href="' . $currentUrl . '" class="n2_button n2_button--big n2_button--blue">' . 'Try to repair database' . '</a>',
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