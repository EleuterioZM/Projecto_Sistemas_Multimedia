<?php

namespace Nextend\Framework\Session\Joomla;

use Joomla\CMS\Factory;
use Nextend\Framework\Session\AbstractStorage;

class JoomlaStorage extends AbstractStorage {

    public function __construct() {

        parent::__construct(Factory::getUser()->id);
    }

    /**
     * Load the whole session
     */
    protected function load() {
        $stored = Factory::getSession()
                          ->get($this->hash);

        if (!is_array($stored)) {
            $stored = array();
        }
        $this->storage = $stored;
    }

    /**
     * Store the whole session
     */
    protected function store() {
        $session = Factory::getSession();
        if (count($this->storage) > 0) {
            $session->set($this->hash, $this->storage);
        } else {
            $session->set($this->hash, null);
        }
    }
}