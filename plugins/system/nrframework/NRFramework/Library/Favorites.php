<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2022 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace NRFramework\Library;

defined('_JEXEC') or die;

class Favorites
{
	/**
	 * Library
	 * 
	 * @var   Library
	 */
	protected $library = [];

    public function __construct($library = [])
    {
        $this->library = $library;
    }
    
	/**
	 * Handles AJAX Library favorite toggle
	 * 
	 * @return  string
	 */
	public function tf_library_ajax_favorites_toggle()
	{
		$template_id = $this->library->getLibrarySetting('template_id');

		if (empty($template_id))
		{
			return false;
		}
		
		$this->addOrRemoveFavorite($template_id);
		
		return $this->getFavorites();
	}
	
    /**
     * Add or remove favorites
     * 
     * @param   int  $template_id
     * 
     * @return  void
     */
    private function addOrRemoveFavorite($template_id)
    {
        $favorites = $this->getFavorites();

        if (array_key_exists($template_id, $favorites))
        {  
            $this->removeFromFavorites($template_id);
            return;
        }
        
        $this->addToFavorites($template_id);
    }

    /**
     * Add to favorites
     * 
     * @param   int  $template_id
     * 
     * @return  void
     */
    private function addToFavorites($template_id)
    {
        $favorites = $this->getFavorites();

        if (array_key_exists($template_id, $favorites))
        {  
            return;
        }

        $favorites[$template_id] = true;

        $this->saveFavorites($favorites);
    }

    /**
     * Save favorites to file
     * 
     * @param   string  $content
     * 
     * @return  void
     */
    private function saveFavorites($content)
    {
        // Create directory if not exist
        if (!is_dir($this->library->getTemplatesPath()))
        {
            \NRFramework\File::createDirs($this->library->getTemplatesPath());
        }
        
        $file = $this->library->getTemplatesPath() . 'favorites.json';

        return file_put_contents($file, json_encode($content));
    }

    /**
     * Remove from favorites
     * 
     * @param   int  $template_id
     * 
     * @return  void
     */
    private function removeFromFavorites($template_id)
    {
        $favorites = $this->getFavorites();
        unset($favorites[$template_id]);
        $this->saveFavorites($favorites);
    }

    /**
     * Get favorites
     *  
     * @return  array
     */
    public function getFavorites()
    {
        $file = $this->library->getTemplatesPath() . 'favorites.json';

        if (!file_exists($file))
        {
            return [];
        }
        
        return (array) json_decode(file_get_contents($file), true);
    }
}