<?php

/**
 * @package         Convert Forms
 * @version         3.2.8 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

class ConvertFormsModelForms extends JModelList
{
    /**
     *  File extension used for exported files
     *
     *  @var  string
     */
    private $fileExtension = ".cnvf";

    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     *
     * @see      JController
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'state', 'a.state',
                'created', 'a.created',
                'ordering', 'a.ordering',
                'search',
                'name','a.name',
                'leads', 'issues'
            );
        }

        parent::__construct($config);
    }

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return      string  An SQL query
     */
    protected function getListQuery()
    {
        // Create a new query object.           
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        // Select some fields from the item table
        $query
            ->select('a.*')
            ->from('#__convertforms a');
        
        // Filter State
        $filter = $this->getState('filter.state');
        if (is_numeric($filter))
        {
            $query->where('a.state = ' . ( int ) $filter);
        }
        else if (is_array($filter))
        {
            $query->where('a.state IN (' . implode(',', $filter) . ')');
        }
        else if ($filter == '')
        {
            $query->where('(a.state IN (0,1,2))');
        }

        // Filter the list over the search string if set.
        $search = $this->getState('filter.search');
        if (!empty($search))
        {
            if (stripos($search, 'id:') === 0)
            {
                $query->where('a.id = ' . ( int ) substr($search, 3));
            }
            else
            {
                $search = $db->quote('%' . $db->escape($search, true) . '%');
                $query->where(
                    '( `name` LIKE ' . $search . ' )'
                );
            }
        }

        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering', 'a.id');
        $orderDirn = $this->state->get('list.direction', 'desc');
        
        if ($orderCol == 'leads')
        {
            $query->select('(select count(submissions.id) from #__convertforms_conversions as submissions where submissions.form_id = a.id and submissions.state IN (1,2)) as leads');
        }

        $query->order($db->escape($orderCol . ' ' . $orderDirn));

        return $query;
    }

    /**
     * Import Method
     * Import the selected items specified by id
     * and set Redirection to the list of items
     */
    public function import($model)
    {
		$app = JFactory::getApplication();

        $file = $app->input->files->get("file");

        if (!is_array($file) || !isset($file['name']))
        {
            $app->enqueueMessage(JText::_('NR_PLEASE_CHOOSE_A_VALID_FILE'));
            $app->redirect('index.php?option=com_convertforms&view=forms&layout=import');
        }

        $ext = explode(".", $file['name']);

        if ($ext[count($ext) - 1] != substr($this->fileExtension, 1))
        {
            $app->enqueueMessage(JText::_('NR_PLEASE_CHOOSE_A_VALID_FILE'));
            $app->redirect('index.php?option=com_convertforms&view=forms&layout=import');
        }

        jimport('joomla.filesystem.file');
        $publish_all = $app->input->getInt('publish_all', 0);

        $data = file_get_contents($file['tmp_name']);

        if (empty($data))
        {
            $app->enqueueMessage(JText::_('File is empty!'));
            $app->redirect('index.php?option=com_convertforms&view=forms');
            return;
        }
        
        $items = json_decode($data, true);
        if (is_null($items))
        {
            $items = array();
        }

        $msg = JText::_('Items saved');

        foreach ($items as $item)
        {
            $item['id'] = 0;
            if ($publish_all == 0)
            {
                unset($item['published']);
            }
            else if ($publish_all == 1)
            {
                $item['published'] = 1;
            }
            $items[] = $item;

            $saved = $model->save($item);

            if ($saved != 1)
            {
                $msg = JText::_('Error Saving Item') . ' ( ' . $saved . ' )';
            }
        }

        $app->enqueueMessage($msg);
        $app->redirect('index.php?option=com_convertforms&view=forms');
    }

    /**
     * Export Method
     * Export the selected items specified by id
     */
    public function export($ids)
    {
        $db    = $this->getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__convertforms')
            ->where('id IN ( ' . implode(', ', $ids) . ' )');
        $db->setQuery($query);
        $rows = $db->loadObjectList();

        $string = json_encode($rows);

        $filename = JText::_("COM_CONVERTFORMS") . ' Items';
        if (count($rows) == 1)
        {
            $name = Joomla\String\StringHelper::strtolower(html_entity_decode($rows['0']->name));
            $name = preg_replace('#[^a-z0-9_-]#', '_', $name);
            $name = trim(preg_replace('#__+#', '_', $name), '_-');

            $filename = JText::_("COM_CONVERTFORMS") .  ' Item (' . $name . ')';
        }

        // SET DOCUMENT HEADER
        if (preg_match('#Opera(/| )([0-9].[0-9]{1,2})#', $_SERVER['HTTP_USER_AGENT']))
        {
            $UserBrowser = "Opera";
        }
        elseif (preg_match('#MSIE ([0-9].[0-9]{1,2})#', $_SERVER['HTTP_USER_AGENT']))
        {
            $UserBrowser = "IE";
        }
        else
        {
            $UserBrowser = '';
        }
        $mime_type = ($UserBrowser == 'IE' || $UserBrowser == 'Opera') ? 'application/octetstream' : 'application/octet-stream';
        @ob_end_clean();
        ob_start();

        header('Content-Type: ' . $mime_type);
        header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');

        if ($UserBrowser == 'IE')
        {
            header('Content-Disposition: inline; filename="' . $filename . $this->fileExtension . '"');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
        }
        else
        {
            header('Content-Disposition: attachment; filename="' . $filename . $this->fileExtension . '"');
            header('Pragma: no-cache');
        }

        // PRINT STRING
        echo $string;
        die;
    }

    /**
     *  Returns Items Object and transforms params JSON field to object
     *
     *  @return  object
     */
    public function getItems()
    {
        $items = parent::getItems();

        foreach ($items as $key => $item)
        {
            $params = json_decode($item->params);
            $items[$key] = (object) array_merge((array) $item, (array) $params);
            unset($items[$key]->params);
        }

        return $items;
    }
}