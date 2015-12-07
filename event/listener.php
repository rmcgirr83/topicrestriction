<?php
/**
*
* Topic Restriction extension for the phpBB Forum Software package.
*
* @copyright (c) 2015 Rich McGirr (RMcGirr83)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace rmcgirr83\topicrestriction\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var string phpBB root path */
	protected $root_path;

	/** @var string phpEx */
	protected $php_ext;

	public function __construct(\phpbb\auth\auth $auth, \phpbb\template\template $template, \phpbb\user $user, $root_path, $php_ext)
	{
		$this->auth = $auth;
		$this->template = $template;
		$this->user = $user;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
	}

	/**
	* Assign functions defined in this class to event listeners in the core
	*
	* @return array
	* @static
	* @access public
	*/
	static public function getSubscribedEvents()
	{
		return array(
			'core.permissions'						=> 'add_permission',
			'core.viewtopic_before_f_read_check'	=> 'forum_id_check',
			'core.viewforum_get_topic_data'			=> 'modify_template_vars',
			'core.search_modify_param_before'		=> 'search_modify_param_before',
			'core.ucp_pm_compose_quotepost_query_after'	=> 'ucp_pm_compose_quotepost_query_after',
			'core.modify_posting_auth'				=> 'forum_id_check',
		);
	}

	/**
	* Add administrative permissions to manage forums
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function add_permission($event)
	{
		$permissions = $event['permissions'];
		$permissions['f_topic_view'] = array('lang' => 'ACL_F_TOPIC_VIEW', 'cat' => 'misc');
		$event['permissions'] = $permissions;
	}

	/**
	* Check for permission to view topics
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function forum_id_check($event)
	{
		$forum_id = $event['forum_id'];
		$this->check_auth($forum_id);
	}

	/**
	* Show a message if can't view topics
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function modify_template_vars($event)
	{
		if (!$this->auth->acl_get('f_topic_view', $event['forum_id']))
		{
			$this->user->add_lang_ext('rmcgirr83/topicrestriction', 'common');
			$this->template->assign_var('S_CAN_VIEW_TOPICS', true);
		}
	}

	/**
	* Modify search params to exclude forum ids
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function search_modify_param_before($event)
	{
		$ex_fid_ary = $event['ex_fid_ary'];

		$forum_ids = $this->auth->acl_getf('!f_topic_view', true);
		if (sizeof($forum_ids))
		{
			$ex_fid_ary = array_unique(array_merge(array_keys($forum_ids), $ex_fid_ary));
		}
		$event['ex_fid_ary'] = $ex_fid_ary;
	}

	/**
	* Check for permission to quote posts in a PM
	*
	* @param object $event The event object
	* @return null
	* @access public
	*/
	public function ucp_pm_compose_quotepost_query_after($event)
	{
		$forum_id = $event['post']['forum_id'];
		$this->check_auth($forum_id);
	}

	/**
	* User/group can view topics in forum
	*
	* @param object $forum_id The id of the forum
	* @return message
	* @access private
	*/
	private function check_auth($forum_id)
	{
		if (!$this->auth->acl_get('f_topic_view', $forum_id))
		{
			$this->user->add_lang_ext('rmcgirr83/topicrestriction', 'common');

			$link = append_sid("{$this->root_path}viewforum.$this->php_ext", "f=$forum_id");
			meta_refresh(3, $link);
			trigger_error('TOPIC_VIEW_NOTICE');
		}
	}

}
