<?php
/**
 * @package TinyPortal
 * @version 3.0.0
 * @author IchBin - http://www.tinyportal.net
 * @founder Bloc
 * @license MPL 2.0
 *
 * The contents of this file are subject to the Mozilla Public License Version 2.0
 * (the "License"); you may not use this package except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Copyright (C) - The TinyPortal Team
 *
 */
use \TinyPortal\Article as TPArticle;
use \TinyPortal\Util as TPUtil;


if (!defined('SMF')) {
	die('Hacking attempt...');
}

function TPcheckAdminAreas() {{{
	global $context;

	TPcollectPermissions();
	foreach($context['TPortal']['adminlist'] as $adm => $val) {
		if(allowedTo($adm) || !empty($context['TPortal']['show_download'])) {
			return true;
        }
	}
	return false;

}}}

function TPsetupAdminAreas() {{{
	global $context;

	$context['admin_tabs']['custom_modules'] = array();

    call_integration_hook('integrate_tp_admin_areas');

}}}

function TP_addPerms() {{{

	$admperms = array(
        'admin_forum',
        'manage_permissions',
        'moderate_forum',
        'manage_membergroups',
        'manage_bans',
        'send_mail',
        'edit_news',
        'manage_boards',
        'manage_smileys',
        'manage_attachments',
        'tp_articles',
        'tp_blocks',
        'tp_dlmanager',
        'tp_settings'
    );

    call_integration_hook('integrate_tp_admin_permissions', array(&$admperms));

	return $admperms;

}}}

function TPcollectPermissions() {{{
	global $context, $smcFunc;

	$context['TPortal']['permissonlist'] = array();
	// first, the built-in permissions
	$context['TPortal']['permissonlist'][] = array(
		'title' => 'tinyportal',
		'perms' => array(
			'tp_settings' => 0,
			'tp_blocks' => 0,
			'tp_articles' => 0
		)
	);
	$context['TPortal']['permissonlist'][] = array(
		'title' => 'tinyportal_dl',
		'perms' => array(
			'tp_dlmanager' => 0,
			'tp_dlupload' => 0
		)
	);
	$context['TPortal']['permissonlist'][] = array(
		'title' => 'tinyportal_submit',
		'perms' => array(
			'tp_submithtml' => 0,
			'tp_submitbbc' => 0,
			'tp_editownarticle' => 0,
			'tp_alwaysapproved' => 0
		)
	);

	// for SMF2
	$context['TPortal']['tppermissonlist'] = array(
		'tp_settings' => array(false, 'tinyportal', 'tinyportal'),
		'tp_blocks' => array(false, 'tinyportal', 'tinyportal'),
		'tp_articles' => array(false, 'tinyportal', 'tinyportal'),
		'tp_submithtml' => array(false, 'tinyportal', 'tinyportal'),
		'tp_submitbbc' => array(false, 'tinyportal', 'tinyportal'),
		'tp_editownarticle' => array(false, 'tinyportal', 'tinyportal'),
		'tp_alwaysapproved' => array(false, 'tinyportal', 'tinyportal'),
		'tp_dlmanager' => array(false, 'tinyportal', 'tinyportal'),
		'tp_dlupload' => array(false, 'tinyportal', 'tinyportal')
	);

	$context['TPortal']['adminlist'] = array(
		'tp_settings' => 1,
		'tp_blocks' => 1,
		'tp_articles' => 1,
		'tp_dlmanager' => 1,
		'tp_submithtml' => 1,
		'tp_submitbbc' => 1,
	);

}}}

function tp_getbuttons() {{{
	global $scripturl, $txt, $context;

	if(loadLanguage('TPortal') == false)
		loadLanguage('TPortal', 'english');

	$buts = array();

	if(!empty($context['TPortal']['show_download']))
		$buts['downloads'] = array(
			'title' => $txt['tp-downloads'],
			'href' => $scripturl . '?action=tportal;sa=download;dl',
			'show' => true,
			'active_button' => false,
			'sub_buttons' => array(),
		);

	if($context['user']['is_logged'] && (allowedTo('tp_submithtml') || allowedTo('tp_submitbbc') || allowedTo('tp_articles')))
		$buts['tpeditwonarticle'] = array(
			'title' => $txt['tp-myarticles'],
			'href' => $scripturl . '?action=tportal;sa=myarticles',
			'show' => true,
			'active_button' => false,
			'sub_buttons' => array(),
		);

	if(allowedTo('tp_submithtml') || allowedTo('tp_articles'))
		$buts['tpeditwonarticle']['sub_buttons']['submithtml'] = array(
			'title' => $txt['tp-submitarticle'],
			'href' => $scripturl . '?action=' . (allowedTo('tp_articles') ? 'tpadmin' : 'tportal') . ';sa=addarticle_html',
			'show' => true,
			'active_button' => false,
			'sub_buttons' => array(),
		);

	if(allowedTo('tp_submitbbc') || allowedTo('tp_articles'))
		$buts['tpeditwonarticle']['sub_buttons']['submitbbc'] = array(
			'title' => $txt['tp-submitarticlebbc'],
			'href' => $scripturl . '?action=' . (allowedTo('tp_articles') ? 'tpadmin' : 'tportal') . ';sa=addarticle_bbc',
			'show' => true,
			'active_button' => false,
			'sub_buttons' => array(),
		);

	// the admin functions - divider
	if(allowedTo('tp_settings') || allowedTo('tp_articles') || allowedTo('tp_blocks') || allowedTo('tp_dlmanager') || allowedTo('tp_shoutbox'))
		$buts['divde1'] = array(
			'title' => '<span class="tp_menu_horizontal21">&nbsp;</span>',
			'href' => '#button_tpadmin',
			'show' => true,
			'active_button' => false,
			'sub_buttons' => array(),
		);

	if(allowedTo('tp_settings'))
	{
		$buts['tpsettings'] = array(
			'title' => $txt['tp-adminheader1'],
			'href' => $scripturl . '?action=tpadmin;sa=settings',
			'show' => true,
			'active_button' => false,
			'sub_buttons' => array(),
		);
	}
	if(allowedTo('tp_articles'))
	{
		$buts['tparticles'] = array(
			'title' => $txt['tp_menuarticles'],
			'href' => $scripturl . '?action=tpadmin;sa=articles',
			'show' => true,
			'active_button' => false,
			'sub_buttons' => array(),
		);
	}
	if(allowedTo('tp_blocks'))
	{
		$buts['tpblocks'] = array(
			'title' => $txt['tp-adminpanels'],
			'href' => $scripturl . '?action=tpadmin;sa=blocks',
			'show' => true,
			'active_button' => false,
			'sub_buttons' => array(),
		);
	}
	if(allowedTo('tp_blocks'))
	{
		$buts['tpmenuman'] = array(
			'title' => $txt['tp-menumanager'],
			'href' => $scripturl . '?action=tpadmin;sa=menubox',
			'show' => true,
			'active_button' => false,
			'sub_buttons' => array(),
		);
	}
	if(allowedTo('tp_dlmanager'))
	{
		$buts['tpdlmanager'] = array(
			'title' => $txt['permissionname_tp_dlmanager'],
			'href' => $scripturl . '?action=tportal;sa=download;dl=admin',
			'show' => true,
			'active_button' => false,
			'sub_buttons' => array(
			),
		);
	}
	if(allowedTo('tp_shoutbox'))
	{
		$buts['tpshoutbox'] = array(
			'title' => $txt['permissionname_tp_can_admin_shout'],
			'href' => $scripturl . '?action=tpshout;shout=admin;settings',
			'show' => true,
			'active_button' => false,
			'sub_buttons' => array(),
		);
	}
	return $buts;
}}}

function TPcollectSnippets() {{{
	global $context;

	// fetch any blockcodes in blockcodes folder
	$codefiles = array();
	if ($handle = opendir($context['TPortal']['blockcode_upload_path'])) {
		while (false !== ($file = readdir($handle))) {
			if($file != '.' && $file != '..' && $file != '.htaccess' && substr($file, (strlen($file) - 10), 10) == '.blockcode') {
				$snippet = TPparseModfile(file_get_contents($context['TPortal']['blockcode_upload_path'] . $file), array('name', 'author', 'version', 'date', 'description'));
				$codefiles[] = array(
					'file' => substr($file, 0, strlen($file) - 10),
					'name' => isset($snippet['name']) ? $snippet['name'] : '',
					'author' => isset($snippet['author']) ? $snippet['author'] : '',
					'text' => isset($snippet['description']) ? $snippet['description'] : '',
				);
			}
		}
		sort($codefiles);
		closedir($handle);
	}
	return $codefiles;

}}}

function TPparseModfile($file , $returnarray) {{{
	$file = strtr($file, array("\r" => ''));
	$snippet = array();

	while (preg_match('~<(name|code|parameter|author|version|date|description)>\n(.*?)\n</\\1>~is', $file, $code_match) != 0)
	{
		// get the title of this snippet
		if ($code_match[1] == 'name' && in_array('name', $returnarray))
			$snippet['name'] = $code_match[2];
		elseif ($code_match[1] == 'code' && in_array('code', $returnarray))
			$snippet['code'] = $code_match[2];
		elseif ($code_match[1] == 'parameter' && in_array('name', $returnarray))
			$snippet['parameter'][] = $code_match[2];
		elseif ($code_match[1] == 'author' && in_array('author', $returnarray))
			$snippet['author'] = $code_match[2];
		elseif ($code_match[1] == 'version' && in_array('version', $returnarray))
			$snippet['version'] = $code_match[2];
		elseif ($code_match[1] == 'date' && in_array('date', $returnarray))
			$snippet['date'] = $code_match[2];
		elseif ($code_match[1] == 'description' && in_array('description', $returnarray))
			$snippet['description'] = $code_match[2];

		// Get rid of the old tag.
		$file = substr_replace($file, '', strpos($file, $code_match[0]), strlen($code_match[0]));
	}
	return $snippet;

}}}

function TP_article_categories($use_sorted = false) {{{
	global $smcFunc, $context, $txt;

	$context['TPortal']['catnames'] = array();
	$context['TPortal']['categories_shortname'] = array();

	//first : fetch all allowed categories
	$sorted = array();
	// for root category

	$sorted[9999] = array(
		'id' => 9999,
		'name' => '&laquo;' . $txt['tp-noname'] . '&raquo;',
		'parent' => '0',
		'access' => '-1, 0, 1',
		'indent' => 1,
	);
	$total = array();
	$request2 =  $smcFunc['db_query']('', '
		SELECT category, COUNT(*) as files
		FROM {db_prefix}tp_articles
		WHERE category > {int:category} GROUP BY category',
		array(
			'category' => 0
		)
	);
	if($smcFunc['db_num_rows']($request2) > 0)
	{
		while($row = $smcFunc['db_fetch_assoc']($request2))
		{
			$total[$row['category']] = $row['files'];
		}
		$smcFunc['db_free_result']($request2);
	}
	$total2 = array();
	$request2 =  $smcFunc['db_query']('', '
		SELECT value2, COUNT(*) as siblings
		FROM {db_prefix}tp_variables
		WHERE type = {string:type} GROUP BY value2',
		array(
			'type' => 'category'
		)
	);
	if($smcFunc['db_num_rows']($request2) > 0)
	{
		while($row = $smcFunc['db_fetch_assoc']($request2))
		{
			$total2[$row['value2']] = $row['siblings'];
		}
		$smcFunc['db_free_result']($request2);
	}

	$request =  $smcFunc['db_query']('', '
		SELECT cats.*
		FROM {db_prefix}tp_variables as cats
		WHERE cats.type = {string:type}
		ORDER BY cats.value1 ASC',
		array(
			'type' => 'category'
		)
	);

	if($smcFunc['db_num_rows']($request) > 0)
	{
		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			// set the options up
			$options = array(
				'layout' => '1',
				'width' => '100%',
				'cols' => '1',
				'sort' => 'date',
				'sortorder' => 'desc',
				'showchild' => '1',
				'articlecount' => '5',
				'catlayout' => '1',
				'leftpanel' => '0',
				'rightpanel' => '0',
				'toppanel' => '0' ,
				'bottompanel' => '0' ,
				'upperpanel' => '0' ,
				'lowerpanel' => '0',
			);
			$opts = explode('|' , $row['value7']);
			foreach($opts as $op => $val)
			{
				if(substr($val,0,7) == 'layout=')
					$options['layout'] = substr($val,7);
				elseif(substr($val,0,6) == 'width=')
					$options['width'] = substr($val,6);
				elseif(substr($val,0,5) == 'cols=')
					$options['cols'] = substr($val,5);
				elseif(substr($val,0,5) == 'sort=')
					$options['sort'] = substr($val,5);
				elseif(substr($val,0,10) == 'sortorder=')
					$options['sortorder'] = substr($val,10);
				elseif(substr($val,0,10) == 'showchild=')
					$options['showchild'] = substr($val,10);
				elseif(substr($val,0,13) == 'articlecount=')
					$options['articlecount'] = substr($val,13);
				elseif(substr($val,0,10) == 'catlayout=')
					$options['catlayout'] = substr($val,10);
				elseif(substr($val,0,10) == 'leftpanel=')
					$options['leftpanel'] = substr($val,10);
				elseif(substr($val,0,11) == 'rightpanel=')
					$options['rightpanel'] = substr($val,11);
				elseif(substr($val,0,9) == 'toppanel=')
					$options['toppanel'] = substr($val,9);
				elseif(substr($val,0,12) == 'bottompanel=')
					$options['bottompanel'] = substr($val,12);
				elseif(substr($val,0,11) == 'upperpanel=')
					$options['centerpanel'] = substr($val,11);
				elseif(substr($val,0,11) == 'lowerpanel=')
					$options['lowerpanel'] = substr($val,11);
			}

			// check the parent
			if($row['value2'] == $row['id'] || $row['value2'] == '' || $row['value2'] == '0')
				$row['value2'] = 9999;
			// check access
			$show = get_perm($row['value3']);
			if($show) {
				$sorted[$row['id']] = array(
					'id' => $row['id'],
					'shortname' => !empty($row['value8']) ? $row['value8'] : $row['id'],
					'name' => $row['value1'],
					'parent' => $row['value2'],
					'access' => $row['value3'],
					'icon' => $row['value4'],
					'totalfiles' => !empty($total[$row['id']][0]) ? $total[$row['id']][0] : 0,
					'children' => !empty($total2[$row['id']][0]) ? $total2[$row['id']][0] : 0,
					'options' => array(
						'layout' => $options['layout'],
						'catlayout' => $options['catlayout'],
						'width' => $options['width'],
						'cols' => $options['cols'],
						'sort' => $options['sort'],
						'sortorder' => $options['sortorder'],
						'showchild' => $options['showchild'],
						'articlecount' => $options['articlecount'],
						'leftpanel' => $options['leftpanel'],
						'rightpanel' => $options['rightpanel'],
						'toppanel' => $options['toppanel'],
						'bottompanel' => $options['bottompanel'],
						'upperpanel' => $options['upperpanel'],
						'lowerpanel' => $options['lowerpanel'],
					),
				);
				$context['TPortal']['catnames'][$row['id']]=$row['value1'];
				$context['TPortal']['categories_shortname'][$row['id']]=!empty($row['value8']) ? $row['value8'] : $row['id'];
			}
		}
		$smcFunc['db_free_result']($request);
	}
	$context['TPortal']['article_categories'] = array();
	if($use_sorted) {
		// sort them
		if(count($sorted)>1) {
			$context['TPortal']['article_categories'] = chain('id', 'parent', 'name', $sorted);
        }
		else {
			$context['TPortal']['article_categories'] = $sorted;
        }
		unset($context['TPortal']['article_categories'][0]);
	}
	else {
		$context['TPortal']['article_categories'] = $sorted;
		unset($context['TPortal']['article_categories'][0]);
	}
}}}

function chain($primary_field, $parent_field, $sort_field, $rows, $root_id = 0, $maxlevel = 25) {{{
   $c = new chain($primary_field, $parent_field, $sort_field, $rows, $root_id, $maxlevel);
   return $c->chain_table;
}}}

class chain
{
   var $table;
   var $rows;
   var $chain_table;
   var $primary_field;
   var $parent_field;
   var $sort_field;

   function __construct($primary_field, $parent_field, $sort_field, $rows, $root_id, $maxlevel)
   {
       $this->rows = $rows;
       $this->primary_field = $primary_field;
       $this->parent_field = $parent_field;
       $this->sort_field = $sort_field;
       $this->buildChain($root_id,$maxlevel);
   }

   function buildChain($rootcatid,$maxlevel)
   {
       foreach($this->rows as $row)
       {
           $this->table[$row[$this->parent_field]][ $row[$this->primary_field]] = $row;
       }
       $this->makeBranch($rootcatid, 0, $maxlevel);
   }

   function makeBranch($parent_id, $level, $maxlevel)
   {
       if(!is_array($this->table))
              $this->table = array();

       if(!array_key_exists($parent_id, $this->table))
              return;

       $rows = $this->table[$parent_id];
       foreach($rows as $key=>$value)
       {
           $rows[$key]['key'] = $this->sort_field;
       }

       usort($rows, 'chainCMP');
       foreach($rows as $item)
       {
           $item['indent'] = $level;
           $this->chain_table[] = $item;
           if((isset($this->table[$item[$this->primary_field]])) && (($maxlevel > $level + 1) || ($maxlevel == 0)))
           {
               $this->makeBranch($item[$this->primary_field], $level + 1, $maxlevel);
           }
       }
   }
}

function chainCMP($a, $b)
{
   if($a[$a['key']] == $b[$b['key']])
   {
       return 0;
   }
   return($a[$a['key']] < $b[$b['key']]) ? -1 : 1;
}

function tp_cleantitle($text)
{
	$tmp = strtr($text, 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÑÒÓÔÕÖØÙÚÛÜÝàáâãäåçèéêëìíîïñòóôõöøùúûüýÿ', 'SZszYAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy');
	$tmp = strtr($tmp, array('Þ' => 'TH', 'þ' => 'th', 'Ð' => 'DH', 'ð' => 'dh', 'ß' => 'ss', '' => 'OE', '' => 'oe', 'Æ' => 'AE', 'æ' => 'ae', 'µ' => 'u'));
	$cleaned = preg_replace(array('/\s/', '/[^\w_\.\-]/'), array('_', ''), $tmp);
	return $cleaned;
}

function TP_permaTheme($theme)
{
	global $context, $smcFunc;

	$me = $context['user']['id'];
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}members
		SET id_theme = {int:theme}
		WHERE id_member = {int:mem_id}',
		array(
			'theme' => $theme, 'mem_id' => $me,
		)
	);

	if(isset($context['TPortal']['querystring']))
		$tp_where = str_replace(array(';permanent'), array(''), $context['TPortal']['querystring']);
	else
		$tp_where = 'action=forum;';

	redirectexit($tp_where);
}

function TP_setThemeLayer($layer, $template, $subtemplate, $admin = false)
{
	global $context, $settings;

	if($admin)
	{
		loadtemplate($template);
		if(file_exists($settings['theme_dir']. '/'. $template. '.css'))
			$context['html_headers'] .= '<link rel="stylesheet" type="text/css" href="'. $settings['theme_url']. '/'. $template. '.css?fin160" />';
		else
			$context['html_headers'] .= '<link rel="stylesheet" type="text/css" href="'. $settings['default_theme_url']. '/'. $template. '.css?fin160" />';

		if( loadLanguage('TPortalAdmin') == false)
			loadlangauge('TPortalAdmin', 'english');
		if(loadLanguage($template) == false)
			loadLanguage($template, 'english');

		adminIndex('tportal');
		$context['template_layers'][] = $layer;
		$context['sub_template'] = $subtemplate;
	}
	else
	{
		loadtemplate($template);
		if(loadLanguage($template) == false)
			loadLanguage($template, 'english');

		if(file_exists($settings['theme_dir']. '/'. $template. '.css'))
			$context['html_headers'] .= '<link rel="stylesheet" type="text/css" href="'. $settings['theme_url']. '/'. $template. '.css?fin160" />';
		else
			$context['html_headers'] .= '<link rel="stylesheet" type="text/css" href="'. $settings['default_theme_url']. '/'. $template. '.css?fin160" />';

		$context['template_layers'][] = $layer;
		$context['sub_template'] = $subtemplate;
	}
}

function TP_notify($text)
{
	global $context;

	$context['TPortal']['tpnotify'] = $text;
	if($context['user']['is_admin'])
	{
		$context['template_layers'][] = 'tpnotify';
		$context['subtemplate'] = '';
	}
}

function TP_error($text)
{
	global $context;

	$context['TPortal']['tperror'] = $text;
	$context['template_layers'][] = 'tperror';
}

function tp_renderbbc($message)
{
	global $context, $txt;

	$descriptionEditorOptions = array(
		'id' => 'description',
		'value' => $context['theme']['description'],
		// We do XML preview here.
		'preview_type' => 0,
		// Specify the size
		'rows' => 7,
		'columns' => 120,
		'width' => '99%',
	);
	create_control_richedit($descriptionEditorOptions);

	// We do not yet support spell checking.
	$context['show_spellchecking'] = false;
	$context['can_post_team'] = siteAllowedTo('postAsTeam');
	$context['sub_template'] = 'themepost';
	$context['page_title'] = $context['editing'] ? $txt['ts_editing_theme'] . $context['theme']['name'] : $txt['ts_add_new_theme'];
	loadTemplate('Post');

	echo '
			<tr>
				<td class="windowbg2" colspan="2">';

		echo '
				</td>
			</tr>';
}

function get_snippets_xml()
{
	return;
}

if(!function_exists('htmlspecialchars_decode'))
{
    function htmlspecialchars_decode($string,$style = ENT_COMPAT)
    {
        $translation = array_flip(get_html_translation_table(HTML_SPECIALCHARS, $style));
        if($style === ENT_QUOTES)
			$translation['&#38;#38;#039;'] = '\'';
		return strtr($string,$translation);
    }
}

function TP_createtopic($title, $text, $icon, $board, $sticky = 0, $submitter = false)
{
	global $user_info, $board_info, $sourcedir;

	require_once($sourcedir.'/Subs-Post.php');

	$body = str_replace(array("<",">","\n","	"), array("&lt;","&gt;","<br>","&nbsp;"), $text);
	preparsecode($body);

	// Collect all parameters for the creation or modification of a post.
	$msgOptions = array(
		'id' => empty($_REQUEST['msg']) ? 0 : (int) $_REQUEST['msg'],
		'subject' => $title,
		'body' =>$body,
		'icon' => $icon,
		'smileys_enabled' => '1',
		'attachments' => array(),
	);
	$topicOptions = array(
		'id' => empty($topic) ? 0 : $topic,
		'board' => $board,
		'poll' => null,
		'lock_mode' => null,
		'sticky_mode' => $sticky,
		'mark_as_read' => true,
	);
	$posterOptions = array(
		'id' => $submitter,
		'name' => '',
		'email' => '',
		'update_post_count' => !$user_info['is_guest'] && !isset($_REQUEST['msg']) && isset($board_info['posts_count']),
	);

	if(createPost($msgOptions, $topicOptions, $posterOptions))
		$topi = $topicOptions['id'];
	else
		$topi = 0;

	return $topi;
}

function TPwysiwyg($textarea, $body, $upload = true, $uploadname = false, $use = 1, $showchoice = true)
{
	global $user_info, $boardurl, $settings, $boarddir, $context, $txt, $scripturl;

	ob_start();
	TP_bbcbox($textarea);
	$editor = ob_get_clean();

	echo '
		<div style="padding-top: 10px;">
		<div>' . $editor . '</div>';

	// only if you can edit your own articles
	if($upload && allowedTo('tp_editownarticle')) {
		// fetch all images you have uploaded
		$imgfiles = array();
		if ($handle = opendir($context['TPortal']['image_upload_path'].'thumbs')) {
			while (false !== ($file = readdir($handle))) {
				if($file != '.' && $file !='..' && $file !='.htaccess' && substr($file, 0, strlen($user_info['id']) + 9) == 'thumb_'.$user_info['id'].'uid') {
					$imgfiles[($context['TPortal']['image_upload_path'].'thumbs/'.$file)] = $file;
				}
			}
			closedir($handle);
			ksort($imgfiles);
			$imgs = $imgfiles;
		}

		echo '
		<br><div class="title_bar">
			<h3 class="titlebg">', $txt['tp-quicklist'], '</h3>
		</div>
		<div class="windowbg2 smalltext tp_pad">', $txt['tp-quicklist2'], '</div>
		<div class="windowbg tpquicklist">
			<div class="tpthumb">';

		if(isset($imgs)) {
			foreach($imgs as $im) {
				echo '
				<img src="'.$boardurl.'/tp-images/', substr($im,6) , '"  alt="'.substr($im,6).'" title="'.substr($im,6).'" />';
			}
		}

		echo '
			</div>
		</div>
		<div class="tp_pad">', $txt['tp-uploadfile'], '
			<input onchange="this.form.submit()" type="file" name="'.$uploadname.'">
		</div>
	</div>';
	}
}

function TP_getallmenus()
{
	global $smcFunc;

	$request = $smcFunc['db_query']('', '
		SELECT * FROM {db_prefix}tp_variables
		WHERE type = {string:type}
		ORDER BY value1 ASC',
		array(
			'type' => 'menus'
		)
	);
	$menus = array();
	$menus[0] = array(
		'id' => 0,
		'name' => 'Internal',
	);

	if($smcFunc['db_num_rows']($request) > 0)
	{
		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			$menus[$row['id']] = array(
				'id' => $row['id'],
				'name' => $row['value1'],
			);
		}
		$smcFunc['db_free_result']($request);
	}
	return $menus;
}

function TP_getmenu($menu_id)
{
	global $scripturl, $smcFunc;

	// get menubox items
	$menu = array();
	$request =  $smcFunc['db_query']('', '
		SELECT * FROM {db_prefix}tp_variables
		WHERE type = {string:type}
		AND subtype2 = {int:subtype}
		ORDER BY value5 ASC',
		array(
			'type' => 'menubox', 'subtype' => $menu_id,
		)
	);
	if($smcFunc['db_num_rows']($request) > 0)
	{
		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			if($row['value5'] != -1 && $row['value2'] != '-1')
			{
				$mtype = substr($row['value3'], 0, 4);
				$idtype = substr($row['value3'], 4);
				if($mtype != 'cats' && $mtype != 'arti' && $mtype != 'head' && $mtype != 'spac')
				{
					$mtype = 'link';
					$idtype = $row['value3'];
				}
				if($mtype == 'head')
				{
					$mtype = 'head';
					$idtype = $row['value1'];
				}
				$menupos = $row['value5'];

				switch($mtype)
				{
					case 'cats' :
						$href = '
				<a href="'. $scripturl. '?cat='.$idtype.'" ' .( $row['value2'] == '1' ? 'target="_blank"' : ''). '>'.$row['value1'].'</a>';
						break;
					case 'arti' :
						$href =  '
				<a href="'. $scripturl. '?page='.$idtype.'"' .($row['value2'] == '1' ? 'target="_blank"' : '') . '>'.$row['value1'].'</a>';
						break;
					case 'link' :
						$href =  '
				<a href="'.$idtype.'"' . ($row['value2'] == '1' ? 'target="_blank"' : '') . '>'.$row['value1'].'</a>';
						break;
					default :
						$href =  '
				<a href="'.$idtype.'"' . ($row['value2'] == '1' ? 'target="_blank"' : '') . '>'.$row['value1'].'</a>';
						break;
				}
				if(in_array($mtype, array('cats', 'arti', 'link')))
					$menu[] = array(
						'id' => $row['id'],
						'name' => $row['value1'],
						'pos' => $menupos,
						'sub' => $row['value4'],
						'link' => $href,
					);
			}
		}
		$smcFunc['db_free_result']($request);
	}
	return $menu;
}

function tp_fetchpermissions($perms)
{
	global $txt, $smcFunc;

	$perm = array();
	if(is_array($perms))
	{
		$request = $smcFunc['db_query']('', '
			SELECT p.permission, m.group_name AS group_name, p.id_group AS id_group
			FROM {db_prefix}permissions AS p
            INNER JOIN {db_prefix}membergroups AS m
			    ON p.id_group = m.id_group
			WHERE p.add_deny = {int:deny}
			AND p.permission IN ({array_string:tag})
			AND m.min_posts = {int:minpost}
			ORDER BY m.group_name ASC',
			array('deny' => 1, 'tag' => $perms, 'minpost' => -1)
		);
		if($smcFunc['db_num_rows']($request) > 0)
		{
			while ($row = $smcFunc['db_fetch_assoc']($request))
			{
				$perm[$row['permission']][$row['id_group']] = $row['id_group'];
			}
			$smcFunc['db_free_result']($request);
		}
		// special for members
		$request =  $smcFunc['db_query']('', '
			SELECT p.permission, p.id_group
			FROM {db_prefix}permissions as p
			WHERE p.add_deny = {int:deny}
			AND p.id_group IN (0, -1)
			AND p.permission IN ({array_string:tag})',
			array('deny' => 1, 'tag' => $perms)
		);
		if($smcFunc['db_num_rows']($request) > 0)
		{
			while ($row = $smcFunc['db_fetch_assoc']($request))
			{
				$perm[$row['permission']][$row['id_group']] = $row['id_group'];
			}
			$smcFunc['db_free_result']($request);
		}
		return $perm;
	}
	else
	{
		$names = array();
		$request = $smcFunc['db_query']('', '
			SELECT m.group_name as group_name, m.id_group as id_group
			FROM {db_prefix}membergroups as m
			WHERE m.min_posts = {int:minpost}
			ORDER BY m.group_name ASC',
			array('minpost' => -1)
		);
		if($smcFunc['db_num_rows']($request) > 0)
		{
			// set regaular members
			$names[0] = array(
				'id' => 0,
				'name' => $txt['members'],
			);
			while ($row = $smcFunc['db_fetch_assoc']($request))
			{
				$names[$row['id_group']] = array(
					'id' => $row['id_group'],
					'name' => $row['group_name'],
				);
			}
			$smcFunc['db_free_result']($request);
		}
		return $names;
	}
}

function tp_fetchboards()
{
	global $smcFunc;

	// get all boards for board-spesific news
	$request =  $smcFunc['db_query']('', '
		SELECT id_board, name, board_order
		FROM {db_prefix}boards
		WHERE  1=1
		ORDER BY board_order ASC',
		array()
	);
	$boards = array();
	if ($smcFunc['db_num_rows']($request) > 0)
	{
		while($row = $smcFunc['db_fetch_assoc']($request))
			$boards[] = array('id' => $row['id_board'], 'name' => $row['name']);

		$smcFunc['db_free_result']($request);
	}
	return $boards;
}

function tp_hidepanel($id, $inline = false, $string = false, $margin='')
{
	global $context, $settings;

	$what = '
	<a style="' . (!$inline ? 'float: right;' : '') . ' cursor: pointer;" onclick="togglepanel(\''.$id.'\')">
		<img id="toggle_' . $id . '" src="' . $settings['tp_images_url'] . '/TPupshrink' . (in_array($id, $context['tp_panels']) ? '2' : '') . '.png" ' . (!empty($margin) ? 'style="margin: '.$margin.';"' : '') . 'alt="*" />
	</a>';
	if($string)
		return $what;
	else
		echo $what;
}

function tp_hidepanel2($id, $id2, $alt)
{
	global $txt, $context, $settings;

	$what = '
	<a title="'.$txt[$alt].'" style="cursor: pointer;" onclick="togglepanel(\''.$id.'\');togglepanel(\''.$id2.'\')">
		<img id="toggle_' . $id . '" src="' . $settings['tp_images_url'] . '/TPupshrink' . (in_array($id, $context['tp_panels']) ? '2' : '') . '.png" alt="*" />
	</a>';

	return $what;
}

function tp_collectArticleAttached($art)
{
	global $context, $smcFunc;

	// get attached images
	$context['TPortal']['illustrations'] = array();
	$context['TPortal']['illustrations_align'] = array();
	$context['TPortal']['illustrations_text'] = array();

	if(is_array($art))
	{
		$tagquery = 'FIND_IN_SET(subtype2, "' . implode(',', $art) .'")';
		$request =  $smcFunc['db_query']('', '
			SELECT * FROM {db_prefix}tp_variables
			WHERE type = {string:type}
			AND value5 = {int:val5}
			AND {string:tag}
			ORDER BY subtype2 ASC',
			array(
				'type' => 'articleimage', 'val5' => 0, 'tag' => $tagquery,
			)
		);
	}
	else
		$request =  $smcFunc['db_query']('', '
			SELECT * FROM {db_prefix}tp_variables
			WHERE type = {string:type}
			AND subtype2 = {int:subtype}
			ORDER BY value5 ASC',
			array(
				'type' => 'articleimage', 'subtype' => $art,
			)
		);

	if($smcFunc['db_num_row']($request) > 0)
	{
		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			if(is_array($art))
			{
				$context['TPortal']['illustrations'][$row['subtype2']][$row['value5']] = $row['value1'];
				$context['TPortal']['illustrations_align'][$row['subtype2']][$row['value5']] = $row['value2'];
				$context['TPortal']['illustrations_text'][$row['subtype2']][$row['value5']] = $row['value3'];
			}
			else
			{
				$context['TPortal']['illustrations'][$art][$row['value5']] = $row['value1'];
				$context['TPortal']['illustrations_align'][$art][$row['value5']] = $row['value2'];
				$context['TPortal']['illustrations_text'][$art][$row['value5']] = $row['value3'];
			}
		}
		$smcFunc['db_free_result']($request);
	}
}


function TP_fetchprofile_areas() {{{
	global $smcFunc;

	$areas = array(
		'tp_summary' => array('name' => 'tp_summary', 'permission' => 'profile_view_any'),
		'tp_articles' => array('name' => 'tp_articles', 'permission' => 'tp_articles'),
		'tp_download' => array('name' => 'tp_download', 'permission' => 'tp_dlmanager'),
	);

    call_integration_hook('integrate_tp_profile_areas', array(&$areas));

	return $areas;
}}}

function TP_fetchprofile_areas2($member_id) {{{
	global $context, $scripturl, $txt, $user_info, $smcFunc;

	if (!$user_info['is_guest'] && (($context['user']['is_owner'] && allowedTo('profile_view_own')) || allowedTo(array('profile_view_any', 'moderate_forum', 'manage_permissions','tp_dlmanager','tp_blocks','tp_articles','tp_gallery','tp_linkmanager')))) {
		$context['profile_areas']['tinyportal'] = array(
			'title' => $txt['tp-profilesection'],
			'areas' => array()
		);

		$context['profile_areas']['tinyportal']['areas']['tp_summary'] = '<a href="' . $scripturl . '?action=profile;u=' . $member_id . ';sa=tp_summary">' . $txt['tpsummary'] . '</a>';
		if ($context['user']['is_owner'] || allowedTo('tp_articles')) {
			$context['profile_areas']['tinyportal']['areas']['tp_articles'] = '<a href="' . $scripturl . '?action=profile;u=' . $member_id . ';sa=tp_articles">' . $txt['articlesprofile'] . '</a>';
        }
		if(($context['user']['is_owner'] || allowedTo('tp_dlmanager')) && $context['TPortal']['show_download']) {
			$context['profile_areas']['tinyportal']['areas']['tp_download'] = '<a href="' . $scripturl . '?action=profile;u=' . $member_id . ';sa=tp_download">' . $txt['downloadsprofile'] . '</a>';
        }

        call_integration_hook('integrate_tp_profile', array(&$member_id));
	}

}}}

function get_perm($perm, $moderate = '')
{
	global $context, $user_info;

	$show = false;
	$acc = explode(',', $perm);
	foreach($acc as $grp => $val)
	{
		if(in_array($val, $user_info['groups']) && $val > -2)
			$show = true;
	}

	// admin sees all
	if($context['user']['is_admin'])
		$show = true;

	// permission holds true? allow them as well!
	if($moderate != '' && allowedTo($moderate))
		$show = true;

	return $show;
}

function tpsort($a, $b)
{
	return strnatcasecmp($b["timestamp"], $a["timestamp"]);
}

// add to the linktree
function TPadd_linktree($url,$name)
{
	global $context;

	$context['linktree'][] = array('url' => $url, 'name' => $name);
}

// strip the linktree
function TPstrip_linktree()
{
	global $context, $scripturl;

	$context['linktree'] = array();
	$context['linktree'][] = array('url' => $scripturl, 'name' => $context['forum_name']);
}


function normalizeNewline($text)
{
	str_replace("\r\n", "\n", $text);
	str_replace("\r", "\n", $text);

	return addslashes($text);
}

// Constructs a page list.
function TPageIndex($base_url, &$start, $max_value, $num_per_page)
{
	global $modSettings, $txt;

    $flexible_start = false;
	// Save whether $start was less than 0 or not.
	$start_invalid = $start < 0;

	// Make sure $start is a proper variable - not less than 0.
	if ($start_invalid)
		$start = 0;
	// Not greater than the upper bound.
	elseif ($start >= $max_value)
		$start = max(0, (int) $max_value - (((int) $max_value % (int) $num_per_page) == 0 ? $num_per_page : ((int) $max_value % (int) $num_per_page)));
	// And it has to be a multiple of $num_per_page!
	else
		$start = max(0, (int) $start - ((int) $start % (int) $num_per_page));

	// Wireless will need the protocol on the URL somewhere.
	if (defined('WIRELESS') && WIRELESS )
		$base_url .= ';' . WIRELESS_PROTOCOL;

	$base_link = '<a class="navPages" href="' . ($flexible_start ? $base_url : strtr($base_url, array('%' => '%%')) . ';p=%d') . '">%s</a> ';

	// Compact pages is off or on?
	if (empty($modSettings['compactTopicPagesEnable']))
	{
		// Show the left arrow.
		$pageindex = $start == 0 ? ' ' : sprintf($base_link, $start - $num_per_page, '&#171;');

		// Show all the pages.
		$display_page = 1;
		for ($counter = 0; $counter < $max_value; $counter += $num_per_page)
			$pageindex .= $start == $counter && !$start_invalid ? '<b>' . $display_page++ . '</b> ' : sprintf($base_link, $counter, $display_page++);

		// Show the right arrow.
		$display_page = ($start + $num_per_page) > $max_value ? $max_value : ($start + $num_per_page);
		if ($start != $counter - $max_value && !$start_invalid)
			$pageindex .= $display_page > $counter - $num_per_page ? ' ' : sprintf($base_link, $display_page, '&#187;');
	}
	else
	{
		// If they didn't enter an odd value, pretend they did.
		$PageContiguous = (int) ($modSettings['compactTopicPagesContiguous'] - ($modSettings['compactTopicPagesContiguous'] % 2)) / 2;

		// Show the first page. (>1< ... 6 7 [8] 9 10 ... 15)
		if ($start > $num_per_page * $PageContiguous)
			$pageindex = sprintf($base_link, 0, '1');
		else
			$pageindex = '';

		// Show the ... after the first page.  (1 >...< 6 7 [8] 9 10 ... 15)
		if ($start > $num_per_page * ($PageContiguous + 1))
			$pageindex .= '<b> ... </b>';

		// Show the pages before the current one. (1 ... >6 7< [8] 9 10 ... 15)
		for ($nCont = $PageContiguous; $nCont >= 1; $nCont--)
			if ($start >= $num_per_page * $nCont)
			{
				$tmpStart = $start - $num_per_page * $nCont;
				$pageindex .= sprintf($base_link, $tmpStart, $tmpStart / $num_per_page + 1);
			}

		// Show the current page. (1 ... 6 7 >[8]< 9 10 ... 15)
		if (!$start_invalid)
			$pageindex .= '[<b>' . ($start / $num_per_page + 1) . '</b>] ';
		else
			$pageindex .= sprintf($base_link, $start, $start / $num_per_page + 1);

		// Show the pages after the current one... (1 ... 6 7 [8] >9 10< ... 15)
		$tmpMaxPages = (int) (($max_value - 1) / $num_per_page) * $num_per_page;
		for ($nCont = 1; $nCont <= $PageContiguous; $nCont++)
			if ($start + $num_per_page * $nCont <= $tmpMaxPages)
			{
				$tmpStart = $start + $num_per_page * $nCont;
				$pageindex .= sprintf($base_link, $tmpStart, $tmpStart / $num_per_page + 1);
			}

		// Show the '...' part near the end. (1 ... 6 7 [8] 9 10 >...< 15)
		if ($start + $num_per_page * ($PageContiguous + 1) < $tmpMaxPages)
			$pageindex .= '<b> ... </b>';

		// Show the last number in the list. (1 ... 6 7 [8] 9 10 ... >15<)
		if ($start + $num_per_page * $PageContiguous < $tmpMaxPages)
			$pageindex .= sprintf($base_link, $tmpMaxPages, $tmpMaxPages / $num_per_page + 1);
	}
	$pageindex = $txt['pages']. ': ' . $pageindex;
	return $pageindex;
}

function tp_renderarticle($intro = '')
{
	global $context, $txt, $scripturl, $boarddir, $smcFunc;
	global $image_proxy_enabled, $image_proxy_secret, $boardurl;

    $data = '';

	// just return if data is missing
	if(!isset($context['TPortal']['article'])) {
		return;
    }

	$data .= '
	<div class="article_inner">';
	// use intro!
	if(($context['TPortal']['article']['useintro'] == '1' && !$context['TPortal']['single_article']) || !empty($intro)) {
		if($context['TPortal']['article']['rendertype'] == 'php') {
            ob_start();
			eval(tp_convertphp($context['TPortal']['article']['intro'], true));
            $data .= ob_get_clean();
		}
		elseif($context['TPortal']['article']['rendertype'] == 'bbc' || $context['TPortal']['article']['rendertype'] == 'import') {
            if(TPUtil::isHTML($context['TPortal']['article']['intro']) || isset($context['TPortal']['article']['parsed_bbc'])) {
			    $data .= $context['TPortal']['article']['intro'];
            }
            else {
                $data .= parse_bbc($context['TPortal']['article']['intro']);
            }
		}
		else {
			$data .= $context['TPortal']['article']['intro'];
		}
        $data .= '<p class="tp_readmore"><b><a href="' .$scripturl . '?page='. ( !empty($context['TPortal']['article']['shortname']) ? $context['TPortal']['article']['shortname'] : $context['TPortal']['article']['id'] ) . '' . (( defined('WIRELESS') && WIRELESS ) ? ';' . WIRELESS_PROTOCOL : '' ). '">'.$txt['tp-readmore'].'</a></b></p>';
	}
	else {
		if($context['TPortal']['article']['rendertype'] == 'php') {
            ob_start();
			eval(tp_convertphp($context['TPortal']['article']['body'], true));
            $data .= ob_get_clean();
		}
		elseif($context['TPortal']['article']['rendertype'] == 'bbc') {
            if(TPUtil::isHTML($context['TPortal']['article']['body']) || isset($context['TPortal']['article']['parsed_bbc'])) {
			    $data .= $context['TPortal']['article']['body'];
            }
            else {
			    $data .= parse_bbc($context['TPortal']['article']['body']);
            }

            if(!empty($context['TPortal']['article']['readmore'])) {
                $data .= $context['TPortal']['article']['readmore'];
            }
		}
		elseif($context['TPortal']['article']['rendertype'] == 'import') {
			if(!file_exists($boarddir. '/' . $context['TPortal']['article']['fileimport'])) {
				$data .= '<em>' . $txt['tp-cannotfetchfile'] . '</em>';
            }
			else {
				include($context['TPortal']['article']['fileimport']);
            }
		}
		else {
			$post = $context['TPortal']['article']['body'];
			if ($image_proxy_enabled && !empty($post) && stripos($post, 'http://') !== false) {
				$post = preg_replace_callback("~<img([\w\W]+?)/>~",
					function( $matches ) use ( $boardurl, $image_proxy_secret ) {
						if (stripos($matches[0], 'http://') !== false) {
							$matches[0] = preg_replace_callback("~src\=(?:\"|\')(.+?)(?:\"|\')~",
								function( $src ) use ( $boardurl, $image_proxy_secret ) {
									if (stripos($src[1], 'http://') !== false)
										return ' src="'. $boardurl . '/proxy.php?request='.urlencode($src[1]).'&hash=' . md5($src[1] . $image_proxy_secret) .'"';
									else
										return $src[0];
								},
								$matches[0]);
						}
						return $matches[0];
					},
				$post);
			}
			$data .= $post;
		}
	}
	$data .= '</div> <!-- article_inner -->';
	return $data;
}

function tp_renderblockarticle()
{

	global $context, $txt, $boarddir;

	// just return if data is missing
	if(!isset($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]))
		return;

	echo '
	<div class="article_inner">';
	if($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['rendertype'] == 'php')
		eval($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['body']);
	elseif($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['rendertype'] == 'import')
	{
		if(!file_exists($boarddir. '/' . $context['TPortal']['blockarticles'][$context['TPortals']['blockarticle']]['fileimport']))
			echo '<em>' , $txt['tp-cannotfetchfile'] , '</em>';
		else
			include($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['fileimport']);
	}
	elseif($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['rendertype']=='bbc')
		echo parse_bbc($context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['body']);
	else
		echo $context['TPortal']['blockarticles'][$context['TPortal']['blockarticle']]['body'];
	echo '
	</div>';
	return;
}

function render_template($code, $render = true)
{
    global $context;

    if(!empty($context['TPortal']['disable_template_eval']) && $render == true) {
        if(preg_match_all('~(?<={)([A-Za-z_]+)(?=})~', $code, $match) !== false) {
            foreach($match[0] as $func) {
                if(function_exists($func)) {
                    $output = $func(false);
                    $code   = str_replace( '{'.$func.'}', $output, $code);
                }
            }
            echo $code;
        }
    }
    else {
	    $ncode = 'echo \'' . str_replace(array('{','}'),array("', ","(), '"),$code).'\';';
	    if($render) {
		    eval($ncode);
        }
	    else {
		    return $ncode;
        }
    }
}

function render_template_layout($code, $prefix = '')
{
    global $context;

    if(!empty($context['TPortal']['disable_template_eval'])) {
        if(preg_match_all('~(?<={)([A-Za-z0-9]+)(?=})~', $code, $match) !== false) {
            foreach($match[0] as $suffix) {
                $func = (string)"$prefix$suffix";
                if(function_exists($func)) {
                    ob_start();
                    $func();
                    $output = ob_get_clean();
                    $code   = str_replace( '{'.$suffix.'}', $output, $code);
                }
            }
            echo $code;
        }
    }
    else {
	    $ncode = 'echo \'' . str_replace(array('{','}'),array("', " . $prefix , "(), '"),$code).'\';';
	    eval($ncode);
    }
}

function tp_hidebars($what = 'all' )
{
	global $context;

	if($what == 'all'){
		$context['TPortal']['leftpanel'] = 0;
		$context['TPortal']['centerpanel'] = 0;
		$context['TPortal']['rightpanel'] = 0;
		$context['TPortal']['bottompanel'] = 0;
		$context['TPortal']['toppanel'] = 0;
		$context['TPortal']['lowerpanel'] = 0;
	}
	elseif($what == 'left')
		$context['TPortal']['leftpanel'] = 0;
	elseif($what=='right')
		$context['TPortal']['rightpanel'] = 0;
	elseif($what=='center')
		$context['TPortal']['centerpanel'] = 0;
	elseif($what=='bottom')
		$context['TPortal']['bottompanel'] = 0;
	elseif($what=='top')
		$context['TPortal']['toppanel'] = 0;
	elseif($what=='lower')
		$context['TPortal']['lowerpanel'] = 0;
}

function TPgetlangOption($langlist, $set)
{

	$lang   = explode("|", $langlist);
	if(is_countable($lang)) {
        $num = count($lang);
    }
    else {
        $num = 0;
    }

	$setlang = '';

	for($i=0; $i < $num ; $i = $i + 2){
		if($lang[$i] == $set)
			$setlang = $lang[$i+1];
	}

	return $setlang;
}

function category_col($column, $featured = false, $render = true)
{
    global $context;

    unset($context['TPortal']['article']);

    if(!isset($context['TPortal']['category'][$column])) {
        return;
    }

    if($column == 'featured' ) {
        $context['TPortal']['category']['featured'] = array( $context['TPortal']['category']['featured'] );
    }

    foreach($context['TPortal']['category'][$column] as $article => $context['TPortal']['article']) {
        if(!empty($context['TPortal']['article']['template'])) {
            render_template($context['TPortal']['article']['template'], $render);
        }
        else {
            if(function_exists('ctheme_article_renders')) {
                render_template(ctheme_article_renders($context['TPortal']['category']['options']['catlayout'], false, $featured), $render);
            }
            else {
                render_template(article_renders($context['TPortal']['category']['options']['catlayout'], false, $featured), $render);
            }
        }
        unset($context['TPortal']['article']);
    }
}

// the featured or first article
function category_featured( $render = true)
{
    return category_col('featured', true, $render);

}
// the first half
function category_col1($render = true)
{
    return category_col('col1', false, $render);
}

// the second half
function category_col2($render = true)
{
    return category_col('col2', false, $render);
}

function TPparseRSS($override = '', $encoding = 0)
{
	global $context, $smcFunc, $sourcedir;

	// Initialise the number of RSS Feeds to show
	$numShown = 0;

	$backend = isset($context['TPortal']['rss']) ? $context['TPortal']['rss'] : '';
	if($override != '')
		$backend = $override;

        require_once($sourcedir . '/Subs-Package.php');
        $data   = fetch_web_data($backend);
        $xml    = simplexml_load_string($data);

	if($xml !== false) {
		switch (strtolower($xml->getName())) {
			case 'rss':
				foreach ($xml->channel->item as $v) {
					if($numShown++ >= $context['TPortal']['rssmaxshown'])
						break;

					printf("<div class=\"rss_title%s\"><a target='_blank' href='%s'>%s</a></div>", $context['TPortal']['rss_notitles'] ? '_normal' : '', trim($v->link), $smcFunc['htmlspecialchars'](trim($v->title), ENT_QUOTES));

					if(!$context['TPortal']['rss_notitles'])
						printf("<div class=\"rss_date\">%s</div><div class=\"rss_body\">%s</div>", $v->pubDate, $v->description);
				}
				break;
			case 'feed':
				foreach ($xml->entry as $v) {
					if($numShown++ >= $context['TPortal']['rssmaxshown'])
						break;

					printf("<div class=\"rss_title%s\"><a target='_blank' href='%s'>%s</a></div>", $context['TPortal']['rss_notitles'] ? '_normal' : '', trim($v->link['href']), $smcFunc['htmlspecialchars'](trim($v->title), ENT_QUOTES));

					if(!$context['TPortal']['rss_notitles'])
						printf("<div class=\"rss_date\">%s</div><div class=\"rss_body\">%s</div>", isset($v->issued) ? $v->issued : $v->published, $v->summary);
				}
				break;
		}
	}

}

// Set up the administration sections.
function TPadminIndex($tpsub = '', $module_admin = false) {{{
	global $txt, $context, $scripturl, $smcFunc;

	if(loadLanguage('TPortalAdmin') == false)
		loadLanguage('TPortalAdmin', 'english');

	if($module_admin) {
		// make sure tpadmin is still active
		$_GET['action'] = 'tpadmin';
	}

	$context['admin_tabs'] = array();
	$context['admin_header']['tp_settings'] = $txt['tp-adminheader1'];
	$context['admin_header']['tp_articles'] = $txt['tp-articles'];
	$context['admin_header']['tp_blocks'] = $txt['tp-adminpanels'];
	$context['admin_header']['tp_menubox'] = $txt['tp-menumanager'];
	if (allowedTo('tp_can_admin_shout') || allowedTo('tp_dlmanager') || allowedTo('tp_can_list_images')) {
		$context['admin_header']['custom_modules'] = $txt['custom_modules'];
	}

	if (allowedTo('tp_settings')) {
		$context['admin_tabs']['tp_settings'] = array(
			'settings' => array(
				'title' => $txt['tp-settings'],
				'description' => $txt['tp-settingdesc1'],
				'href' => $scripturl . '?action=tpadmin;sa=settings',
				'is_selected' => $tpsub == 'settings',
			),
			'frontpage' => array(
				'title' => $txt['tp-frontpage'],
				'description' => $txt['tp-frontpagedesc1'],
				'href' => $scripturl . '?action=tpadmin;sa=frontpage',
				'is_selected' => $tpsub == 'frontpage',
			),
		);
	}
	if (allowedTo('tp_editownarticle')) {
		$context['admin_tabs']['tp_articles'] = array(
			'myarticles' => array(
				'title' => $txt['tp-myarticles'],
				'description' => $txt['tp-articledesc1'],
				'href' => $scripturl . '?action=tportal;sa=myarticles',
				'is_selected' => $tpsub == 'myarticles',
			),
		);
	}

	if (allowedTo('tp_articles')) {
		$context['admin_tabs']['tp_articles'] = array(
			'articles' => array(
				'title' => $txt['tp-articles'],
				'description' => $txt['tp-articledesc1'],
				'href' => $scripturl . '?action=tpadmin;sa=articles',
				'is_selected' => (substr($tpsub,0,11)=='editarticle' || in_array($tpsub, array('articles','addarticle','addarticle_php', 'addarticle_bbc', 'addarticle_import','strays','submission'))),
			),
			'categories' => array(
				'title' => $txt['tp-tabs5'],
				'description' => $txt['tp-articledesc2'],
				'href' => $scripturl . '?action=tpadmin;sa=categories',
				'is_selected' => in_array($tpsub, array('categories', 'addcategory','clist')) ,
			),
			'artsettings' => array(
				'title' => $txt['tp-artsettings'],
				'description' => $txt['tp-articledesc3'],
				'href' => $scripturl . '?action=tpadmin;sa=artsettings',
				'is_selected' => $tpsub == 'artsettings',
			),
			'icons' => array(
				'title' => $txt['tp-adminicons'],
				'description' => $txt['tp-articledesc5'],
				'href' => $scripturl . '?action=tpadmin;sa=articons',
				'is_selected' => $tpsub == 'articons',
			),
		);
	}

	if (allowedTo('tp_blocks')) {
		$context['admin_tabs']['tp_blocks'] = array(
			'panelsettings' => array(
				'title' => $txt['tp-allpanels'],
				'description' => $txt['tp-paneldesc1'],
				'href' => $scripturl . '?action=tpadmin;sa=panels',
				'is_selected' => $tpsub == 'panels',
			),
			'blocks' => array(
				'title' => $txt['tp-allblocks'],
				'description' => $txt['tp-blocksdesc1'],
				'href' => $scripturl . '?action=tpadmin;sa=blocks',
				'is_selected' => $tpsub == 'blocks' && !isset($_GET['latest']) && !isset($_GET['overview']),
			),
			'blockoverview' => array(
				'title' => $txt['tp-blockoverview'],
				'description' => '',
				'href' => $scripturl . '?action=tpadmin;sa=blocks;overview',
				'is_selected' => ($tpsub == 'blocks' && isset($_GET['overview'])) || substr($tpsub,0,9) == 'editblock',
			),
		);
	}

	if (allowedTo('tp_blocks')) {
		$context['admin_tabs']['tp_menubox'] = array(
			'menubox' => array(
				'title' => $txt['tp-menumanager'],
				'description' => '',
				'href' => $scripturl . '?action=tpadmin;sa=menubox',
				'is_selected' => in_array($tpsub, array('menubox','linkmanager')),
			),
			'addmenu' => array(
				'title' => isset($_GET['mid']) ? $txt['tp-addmenuitem'] : $txt['tp-addmenu'],
				'description' => '',
				'href' => (isset($_GET['mid']) && is_numeric($_GET['mid'])) ? $scripturl . '?action=tpadmin;sa=addmenu;mid='.$_GET['mid'] : $scripturl . '?action=tpadmin;sa=addmenu;fullmenu',
				'is_selected' => in_array($tpsub, array('addmenu')),
			),
		);
	}

	TPsetupAdminAreas();
	validateSession();

}}}

function tp_collectArticleIcons()
{
	global $context, $boarddir, $boardurl, $smcFunc;

	// get all themes for selection
	$context['TPthemes']  = array();
	$request =  $smcFunc['db_query']('', '
		SELECT th.value AS name, th.id_theme as id_theme, tb.value AS path
		FROM {db_prefix}themes AS th
		LEFT JOIN {db_prefix}themes AS tb ON th.id_theme = tb.id_theme
		WHERE th.variable = {string:thvar}
		AND tb.variable = {string:tbvar}
		AND th.id_member = {int:mem_id}
		ORDER BY th.value ASC',
		array(
			'thvar' => 'name', 'tbvar' => 'images_url', 'mem_id' => 0,
		)
	);
	if(is_resource($request) && $smcFunc['db_num_rows']($request) > 0)
	{
		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			$context['TPthemes'][] = array(
				'id' => $row['id_theme'],
				'path' => $row['path'],
				'name' => $row['name']
			);
		}
		$smcFunc['db_free_result']($request);
	}

	$count = 1;
	$context['TPortal']['articons'] = array();
	$context['TPortal']['articons']['illustrations'] = array();

	$sorted2 = array();
	//illustrations/images
	if ($handle = opendir($boarddir.'/tp-files/tp-articles/illustrations'))
	{
		while (false !== ($file = readdir($handle)))
		{
			if($file != '.' && $file != '..' && $file != '.htaccess' && $file != 'TPno_illustration.png' && in_array(strtolower(substr($file, strlen($file) -4, 4)), array('.gif', '.jpg', '.png')))
			{
				if(substr($file, 0, 2) == 's_')
					$context['TPortal']['articons']['illustrations'][] = array(
						'id' => $count,
						'file' => $file,
						'image' => '<img src="'.$boardurl.'/tp-files/tp-articles/illustrations/'.$file.'" alt="'.$file.'" />',
						'background' => $boardurl.'/tp-files/tp-articles/illustrations/'.$file,
					);
				$count++;
			}
		}
		closedir($handle);
	}
	sort($context['TPortal']['articons']['illustrations']);
}

function tp_recordevent($date, $id_member, $textvariable, $link, $description, $allowed, $eventid)
{
	global $smcFunc;

	$smcFunc['db_insert']('insert',
		'{db_prefix}tp_events',
		array(
            'id_member'     => 'int',
            'date'          => 'int',
            'textvariable'  => 'string',
            'link'          => 'string',
            'description'   => 'string',
            'allowed'       => 'string',
            'eventid'       => 'int',
            'on'            => 'int',
		),
		array($id_member, $date, $textvariable, $link, $description, $allowed, $eventid, 0),
		array('id')
	);
}

function tp_fatal_error($error)
{
	global $context;

	$context['sub_template'] = 'tp_fatal_error';
	$context['TPortal']['errormessage'] = $error;
}

// Recent topic list:   [board] Subject by Poster	Date
function tp_recentTopics($num_recent = 8, $exclude_boards = null, $include_boards = null, $output_method = 'echo')
{
    return ssi_recentTopics($num_recent, $exclude_boards, $include_boards, $output_method);
}

// Download an attachment.
function tpattach()
{
	global $txt, $modSettings, $context, $smcFunc;

	// Some defaults that we need.
	$context['character_set'] = empty($modSettings['global_character_set']) ? (empty($txt['lang_character_set']) ? 'ISO-8859-1' : $txt['lang_character_set']) : $modSettings['global_character_set'];
	$context['utf8'] = $context['character_set'] === 'UTF-8' && (strpos(strtolower(PHP_OS), 'win') === false || @version_compare(PHP_VERSION, '4.2.3') != -1);
	$context['no_last_modified'] = true;

	// Make sure some attachment was requested!
	if (!isset($_REQUEST['attach']) && !isset($_REQUEST['id']))
		fatal_lang_error('no_access', false);

	$_REQUEST['attach'] = isset($_REQUEST['attach']) ? (int) $_REQUEST['attach'] : (int) $_REQUEST['id'];

	if (isset($_REQUEST['type']) && $_REQUEST['type'] == 'avatar')
	{
		$request = $smcFunc['db_query']('', '
			SELECT id_folder, filename, file_hash, fileext, id_attach, attachment_type, mime_type, approved
			FROM {db_prefix}attachments
			WHERE id_attach = {int:id_attach}
				AND id_member > {int:blank_id_member}
			LIMIT 1',
			array(
				'id_attach' => $_REQUEST['attach'],
				'blank_id_member' => 0,
			)
		);
		$_REQUEST['image'] = true;
	}
	// This is just a regular attachment...
	else
	{
		$request = $smcFunc['db_query']('', '
			SELECT a.id_folder, a.filename, a.file_hash, a.fileext, a.id_attach,
				a.attachment_type, a.mime_type, a.approved
			FROM {db_prefix}attachments AS a
			WHERE a.id_attach = {int:attach}
			LIMIT 1',
			array(
				'attach' => $_REQUEST['attach'],
			)
		);
	}
	if ($smcFunc['db_num_rows']($request) == 0)
		fatal_lang_error('no_access', false);
	list ($id_folder, $real_filename, $file_hash, $file_ext, $id_attach, $attachment_type, $mime_type, $is_approved) = $smcFunc['db_fetch_row']($request);
	$smcFunc['db_free_result']($request);

	$filename = getAttachmentFilename($real_filename, $_REQUEST['attach'], $id_folder, false, $file_hash);

	// This is done to clear any output that was made before now. (would use ob_clean(), but that's PHP 4.2.0+...)
	ob_end_clean();
	if (!empty($modSettings['enableCompressedOutput']) && @version_compare(PHP_VERSION, '4.2.0') >= 0 && @filesize($filename) <= 4194304 && in_array($file_ext, array('txt', 'html', 'htm', 'js', 'doc', 'pdf', 'docx', 'rtf', 'css', 'php', 'log', 'xml', 'sql', 'c', 'java')))
		@ob_start('ob_gzhandler');
	else
	{
		ob_start();
		header('Content-Encoding: none');
	}

	// No point in a nicer message, because this is supposed to be an attachment anyway...
	if (!file_exists($filename))
	{
		loadLanguage('Errors');

		header('HTTP/1.0 404 ' . $txt['attachment_not_found']);
		header('Content-Type: text/plain; charset=' . (empty($context['character_set']) ? 'ISO-8859-1' : $context['character_set']));

		// We need to die like this *before* we send any anti-caching headers as below.
		die('404 - ' . $txt['attachment_not_found']);
	}

	// If it hasn't been modified since the last time this attachement was retrieved, there's no need to display it again.
	if (!empty($_SERVER['HTTP_IF_MODIFIED_SINCE']))
	{
		list($modified_since) = explode(';', $_SERVER['HTTP_IF_MODIFIED_SINCE']);
		if (strtotime($modified_since) >= filemtime($filename))
		{
			ob_end_clean();

			// Answer the question - no, it hasn't been modified ;).
			header('HTTP/1.1 304 Not Modified');
			exit;
		}
	}

	// Check whether the ETag was sent back, and cache based on that...
	$eTag = '"' . substr($_REQUEST['attach'] . $real_filename . filemtime($filename), 0, 64) . '"';
	if (!empty($_SERVER['HTTP_IF_NONE_MATCH']) && strpos($_SERVER['HTTP_IF_NONE_MATCH'], $eTag) !== false)
	{
		ob_end_clean();

		header('HTTP/1.1 304 Not Modified');
		exit;
	}

	// Send the attachment headers.
	header('Pragma: ');

	if (!$context['browser']['is_gecko'])
		header('Content-Transfer-Encoding: binary');
	header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 525600 * 60) . ' GMT');
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($filename)) . ' GMT');
	header('Accept-Ranges: bytes');
	header('Set-Cookie:');
	header('Connection: close');
	header('ETag: ' . $eTag);

	// IE 6 just doesn't play nice. As dirty as this seems, it works.
	if ($context['browser']['is_ie6'] && isset($_REQUEST['image']))
		unset($_REQUEST['image']);

	elseif (filesize($filename) != 0)
	{
		$size = @getimagesize($filename);
		if (!empty($size))
		{
			// What headers are valid?
			$validTypes = array(
				1 => 'gif',
				2 => 'jpeg',
				3 => 'png',
				5 => 'psd',
				6 => 'x-ms-bmp',
				7 => 'tiff',
				8 => 'tiff',
				9 => 'jpeg',
				14 => 'iff',
			);

			// Do we have a mime type we can simpy use?
			if (!empty($size['mime']) && !in_array($size[2], array(4, 13)))
				header('Content-Type: ' . strtr($size['mime'], array('image/bmp' => 'image/x-ms-bmp')));
			elseif (isset($validTypes[$size[2]]))
				header('Content-Type: image/' . $validTypes[$size[2]]);
			// Otherwise - let's think safety first... it might not be an image...
			elseif (isset($_REQUEST['image']))
				unset($_REQUEST['image']);
		}
		// Once again - safe!
		elseif (isset($_REQUEST['image']))
			unset($_REQUEST['image']);
	}

	header('Content-Disposition: ' . (isset($_REQUEST['image']) ? 'inline' : 'attachment') . '; filename="' . $real_filename . '"');
	if (!isset($_REQUEST['image']))
		header('Content-Type: application/octet-stream');

	// If this has an "image extension" - but isn't actually an image - then ensure it isn't cached cause of silly IE.
	if (!isset($_REQUEST['image']) && in_array($file_ext, array('gif', 'jpg', 'bmp', 'png', 'jpeg', 'tiff')))
		header('Cache-Control: no-cache');
	else
		header('Cache-Control: max-age=' . (525600 * 60) . ', private');

	if (empty($modSettings['enableCompressedOutput']) || filesize($filename) > 4194304)
		header('Content-Length: ' . filesize($filename));

	// Try to buy some time...
	@set_time_limit(0);

	// Since we don't do output compression for files this large...
	if (filesize($filename) > 4194304)
	{
		// Forcibly end any output buffering going on.
		if (function_exists('ob_get_level'))
		{
			while (@ob_get_level() > 0)
				@ob_end_clean();
		}
		else
		{
			@ob_end_clean();
			@ob_end_clean();
			@ob_end_clean();
		}

		$fp = fopen($filename, 'rb');
		while (!feof($fp))
		{
			if (isset($callback))
				echo $callback(fread($fp, 8192));
			else
				echo fread($fp, 8192);
			flush();
		}
		fclose($fp);
	}
	// On some of the less-bright hosts, readfile() is disabled.  It's just a faster, more byte safe, version of what's in the if.
	elseif (isset($callback) || @readfile($filename) == null)
		echo isset($callback) ? $callback(file_get_contents($filename)) : file_get_contents($filename);

	obExit(false);
}

function art_recentitems($max = 5, $type = 'date' ){

	global $smcFunc;

	$now = forum_time();
	$data = array();
	$orderby = '';

	if($type == 'date')
		$orderby = 'art.date';
	elseif($type == 'views')
		$orderby = 'art.views';
	elseif($type == 'comments')
		$orderby = 'art.comments';

		$request = $smcFunc['db_query']('', '
			SELECT art.id, art.date, art.subject, art.views, art.rating, art.comments
			FROM {db_prefix}tp_articles as art
			WHERE art.off = {int:off} and art.approved = {int:approved}
			AND ((art.pub_start = 0 AND art.pub_end = 0)
				OR (art.pub_start != 0 AND art.pub_start < '. $now .' AND art.pub_end = 0)
				OR (art.pub_start = 0 AND art.pub_end != 0 AND art.pub_end > '. $now .')
				OR (art.pub_start != 0 AND art.pub_end != 0 AND art.pub_end > '. $now .' AND art.pub_start < '. $now .'))
			ORDER BY {raw:orderby} DESC LIMIT {int:limit}',
			array(
				'off' => 0, 'approved' => 1, 'orderby' => $orderby, 'limit' => $max,
			)
		);

	if($smcFunc['db_num_rows']($request) > 0) {
		while ($row = $smcFunc['db_fetch_assoc']($request)) {
			$rat = explode(',', $row['rating'] ?? '');
            if(is_countable($rat)) {
			    $rating_votes = count($rat);
            }
            else {
                $rating_votes = 0;
            }
			if($row['rating'] == '') {
				$rating_votes = 0;
            }
			$total = 0;
			foreach($rat as $mm => $mval) {
				if(is_numeric($mval)) {
					$total = $total + $mval;
                }
			}
			if($rating_votes > 0 && $total > 0) {
				$rating_average = floor($total / $rating_votes);
            }
			else {
				$rating_average = 0;
            }

			$data[] = array(
				'id' => $row['id'],
				'subject' => $row['subject'],
				'views' => $row['views'],
				'date' => timeformat($row['date']),
				'rating' => $rating_average,
				'rating_votes' => $rating_votes,
				'comments' => $row['comments'],
			);
		}
		$smcFunc['db_free_result']($request);
	}
	return $data;
}

function dl_recentitems($number = 8, $sort = 'date', $type = 'array', $cat = 0)
{
	global $txt, $boardurl, $context, $scripturl, $smcFunc;

	// collect all categories to search in
	$mycats = array();
	dl_getcats();
	if($cat > 0)
		$mycats[] = $cat;
	else
	{
		foreach($context['TPortal']['dl_allowed_cats'] as $ca)
			$mycats[] = $ca['id'];
	}

	if($sort == 'author_id')
		$sort = 'author_id';

	// empty?
    if(is_countable($mycats) && count($mycats) > 0 ) {
		$context['TPortal']['dlrecenttp'] = array();
		// decide what to sort from
		if($sort == 'date')
			$sortstring = 'ORDER BY dlm.created DESC';
		elseif($sort == 'views')
			$sortstring = 'ORDER BY dlm.views DESC';
		elseif($sort == 'downloads')
			$sortstring = 'ORDER BY dlm.downloads DESC';
		else
			$sortstring = 'ORDER BY dlm.created DESC';

		if($sort == 'weekdownloads')
			$request = $smcFunc['db_query']('', '
				SELECT dlm.id, dlm.description, dlm.author_id as author_id, dlm.name, dlm.category,
					dlm.file, dlm.downloads, dlm.views, dlm.author_id as author_id, dlm.icon,
					dlm.created, dlm.screenshot, dlm.filesize, dlcat.name AS catname, mem.real_name as real_name
				FROM {db_prefix}tp_dlmanager AS dlm
                LEFT JOIN {db_prefix}members AS mem
				    ON dlm.author_id = mem.id_member
				LEFT JOIN {db_prefix}tp_dlmanager AS dlcat
                    ON dlcat.id = dlm.category
				WHERE dlm.type = {string:type}
				AND dlm.category IN ({array_int:cat})
				{raw:sort} LIMIT {int:limit}',
				array('type' => 'dlitem', 'cat' => $mycats, 'sort' => $sortstring, 'limit' => $number)
			);
		else
			$request = $smcFunc['db_query']('', '
				SELECT dlm.id, dlm.description, dlm.author_id as author_id, dlm.name,
					dlm.category, dlm.file, dlm.downloads, dlm.views, dlm.author_id, dlm.icon,
					dlm.created, dlm.screenshot, dlm.filesize, dlcat.name AS catname, mem.real_name as real_name
				FROM {db_prefix}tp_dlmanager AS dlm
                LEFT JOIN {db_prefix}members AS mem
				    ON dlm.author_id = mem.id_member
				LEFT JOIN {db_prefix}tp_dlmanager AS dlcat
                    ON dlcat.id = dlm.category
				WHERE dlm.type = {string:type}
				AND dlm.category IN ({array_int:cat})
				{raw:sort} LIMIT {int:limit}',
				array('type' => 'dlitem', 'cat' => $mycats, 'sort' => $sortstring, 'limit' => $number)
			);
		if($smcFunc['db_num_rows']($request) > 0)
		{
			while ($row = $smcFunc['db_fetch_assoc']($request))
			{
				$fs = '';
				if($context['TPortal']['dl_fileprefix'] == 'K')
					$fs = ceil($row['filesize'] / 1000). $txt['tp-kb'];
				elseif($context['TPortal']['dl_fileprefix'] == 'M')
					$fs = (ceil($row['filesize'] / 1000) / 1000). $txt['tp-mb'];
				elseif($context['TPortal']['dl_fileprefix'] == 'G')
					$fs = (ceil($row['filesize'] / 1000000) / 1000). $txt['tp-gb'];

				if($context['TPortal']['dl_usescreenshot'] == 1)
				{
					if(!empty($row['screenshot']))
						$ico = $boardurl.'/tp-images/dlmanager/thumb/'.$row['screenshot'];
					else
						$ico = '';
				}
				else
					$ico = '';

				$context['TPortal']['dlrecenttp'][] = array(
					'id' => $row['id'],
					'body' => $row['description'],
					'name' => $row['name'],
					'category' => $row['category'],
					'file' => $row['file'],
					'href' => $scripturl.'?action=tportal;sa=download;dl=item'.$row['id'],
					'downloads' => $row['downloads'],
					'views' => $row['views'],
					'author' => '<a href="'.$scripturl.'?action=profile;u='.$row['author_id'].'">'.$row['real_name'].'</a>',
					'author_id' => $row['author_id'],
					'icon' => $row['icon'],
					'date' => timeformat($row['created']),
					'screenshot' => $ico ,
					'catname' => $row['catname'],
					'cathref' => $scripturl.'?action=tportal;sa=download;dl=cat'.$row['category'],
					'filesize' => $fs,
				);
			}
			$smcFunc['db_free_result']($request);
		}
		if($type == 'array')
			return $context['TPortal']['dlrecenttp'];
		else
		{
			echo '
			<div class="post">
				<ul class="dl_recentitems disc">';
			foreach($context['TPortal']['dlrecenttp'] as $dl)
			{
				echo '<li><a href="'.$dl['href'].'">'.$dl['name'].'</a>';
				if($sort == 'date')
					echo ' <small>[' . $dl['downloads'] . ']</small>';
				elseif($sort == 'views')
					echo ' <small>[' . $dl['views'] . ']</small>';
				elseif($sort == 'downloads')
					echo ' <small>[' . $dl['downloads'] . ']</small>';

				echo '</li>';
			}
			echo '
				</ul>
			</div>';
		}
	}
}

function dl_getcats()
{
	global $context, $smcFunc;

	$context['TPortal']['dl_allowed_cats'] = array();
	$request =  $smcFunc['db_query']('','
		SELECT id, parent, name, access
		FROM {db_prefix}tp_dlmanager
		WHERE type = {string:type}',
		array(
			'type' => 'dlcat'
		)
	);
	if($smcFunc['db_num_rows']($request)>0)
	{
		while ($row = $smcFunc['db_fetch_assoc']($request))
		{
			$show = get_perm($row['access'], 'tp_dlmanager');
			if($show)
				$context['TPortal']['dl_allowed_cats'][$row['id']] = array(
					'id' => $row['id'],
					'name' => $row['name'],
					'parent' => $row['parent'],
				);
		}
		$smcFunc['db_free_result']($request);
	}
}

function TP_bbcbox($input)
{
   echo'<div id="tp_smilebox"></div>';
   echo'<div id="tp_messbox"></div>';

   echo template_control_richedit($input, 'tp_messbox', 'tp_smilebox');
}

function TP_prebbcbox($id, $body = '')
{
	global $sourcedir;

	require_once($sourcedir . '/Subs-Editor.php');

	$editorOptions = array(
		'id' => $id,
		'value' => $body,
		'preview_type' => 2,
		'height' => '300px',
		'width' => '100%',
	);
	create_control_richedit($editorOptions);
}

function tp_getblockstyles21()
{
	return array(
		'0' => array(
			'class' => 'titlebg+windowbg',
			'code_title_left' => '<div class="title_bar"><h3 class="titlebg">',
			'code_title_right' => '</h3></div>',
			'code_top' => '<div class="windowbg tp_block21"><div>',
			'code_bottom' => '</div></div>',
		),
		'1' => array(
			'class' => 'catbg+windowbg',
			'code_title_left' => '<div class="cat_bar"><h3 class="catbg">',
			'code_title_right' => '</h3></div>',
			'code_top' => '<div><div class="windowbg tp_block21">',
			'code_bottom' => '</div></div>',
		),
		'2' => array(
			'class' => 'catbg+roundframe',
			'code_title_left' => '<div class="cat_bar"><h3 class="catbg">',
			'code_title_right' => '</h3></div>',
			'code_top' => '<div><div class="roundframe tp_block21">',
			'code_bottom' => '</div></div>',
		),
		'3' => array(
			'class' => 'titletp+windowbg',
			'code_title_left' => '<div class="tp_half21"><h3 class="titlebg" style="font-size: 1.1em; height:auto;">',
			'code_title_right' => '</h3></div>',
			'code_top' => '<div class="windowbg tp_block21"><div>',
			'code_bottom' => '</div></div>',
		),
		'4' => array(
			'class' => 'cattp+windowbg',
			'code_title_left' => '<div class="tp_half21"><h3 class="catbg">',
			'code_title_right' => '</h3></div>',
			'code_top' => '<div class="windowbg tp_block21"><div>',
			'code_bottom' => '</div></div>',
		),
		'5' => array(
			'class' => 'titlebg+windowbg2',
			'code_title_left' => '<div class="title_bar"><h3 class="titlebg">',
			'code_title_right' => '</h3></div>',
			'code_top' => '<div class="windowbg noup tp_block21"><div>',
			'code_bottom' => '</div></div>',
		),
		'6' => array(
			'class' => 'catbg+windowbg2',
			'code_title_left' => '<div class="cat_bar"><h3 class="catbg">',
			'code_title_right' => '</h3></div>',
			'code_top' => '<div><div class="windowbg noup tp_block21">',
			'code_bottom' => '</div></div>',
		),

		'7' => array(
			'class' => 'catbg+roundframe2',
			'code_title_left' => '<div class="cat_bar"><h3 class="catbg">',
			'code_title_right' => '</h3></div>',
			'code_top' => '<div class="roundframe noup tp_block21"><div>',
			'code_bottom' => '</div></div>',
		),
		'8' => array(
			'class' => 'titletp+windowbg2',
			'code_title_left' => '<div class="tp_half21"><h3 class="titlebg" style="font-size: 1.1em; height:auto;">',
			'code_title_right' => '</h3></div>',
			'code_top' => '<div><div class="windowbg noup tp_block21">',
			'code_bottom' => '</div></div>',
		),
		'9' => array(
			'class' => 'cattp+roundframe2',
			'code_title_left' => '<div class="tp_half21"><h3 class="catbg">',
			'code_title_right' => '</h3></div>',
			'code_top' => '<div class="roundframe noup tp_block21"><div>',
			'code_bottom' => '</div></div>',
		),
	);
}

function get_grps($save = true, $noposts = true)
{
	global $context, $txt, $smcFunc;

	// get all membergroups for permissions
	$context['TPmembergroups'] = array();
	if($noposts)
	{
		$context['TPmembergroups'][] = array(
			'id' => '-1',
			'name' => $txt['tp-guests'],
			'posts' => '-1'
		);
		$context['TPmembergroups'][] = array(
			'id' => '0',
			'name' => $txt['tp-ungroupedmembers'],
			'posts' => '-1'
		);
	}
    $request = $smcFunc['db_query']('', '
        SELECT id_group as id_group, group_name as group_name, min_posts as min_posts
        FROM {db_prefix}membergroups
        WHERE '. ($noposts ? 'min_posts = -1 AND id_group > 1' : '1') .'
        ORDER BY id_group'
    );

	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$context['TPmembergroups'][] = array(
			'id' => $row['id_group'],
			'name' => $row['group_name'],
			'posts' => $row['min_posts']
		);
	}
	$smcFunc['db_free_result']($request);

	if($save)
		return $context['TPmembergroups'];
}

function tp_convertphp($code, $reverse = false)
{

	if(!$reverse)
	{
		return $code;
	}
	else
	{
		return $code;
	}
}

function tp_getDLcats()
{
	global $context, $smcFunc;

	$context['TPortal']['dlcats'] = array();
	$request =  $smcFunc['db_query']('', '
		SELECT id, name
		FROM {db_prefix}tp_dlmanager
		WHERE type = {string:dlcat}
		ORDER BY name',
		array('dlcat' => 'dlcat')
	);
	$count = 0;
	if ($smcFunc['db_num_rows']($request) > 0) {
		while($row = $smcFunc['db_fetch_assoc']($request)) {
			$context['TPortal']['dlcats'][$count] = array('id' => $row['id'], 'name' => $row['name']);
			$count++;
		}
		$smcFunc['db_free_result']($request);
	}
}

function updateTPSettings($addSettings, $check = false)
{
	global $context, $smcFunc;

	if (empty($addSettings) || !is_array($addSettings))
		return;

	if($check)
	{
		foreach ($addSettings as $variable => $value)
		{
			$request = $smcFunc['db_query']('', 'SELECT value FROM {db_prefix}tp_settings WHERE name = \'' . $variable . '\'');

			if($smcFunc['db_num_rows']($request)==0)
			{
				$smcFunc['db_query']('', '
					INSERT INTO {db_prefix}tp_settings
					(name,value) VALUES({string:variable},{' . ($value === false || $value === true ? 'raw' : 'string') . ':value})',
					array(
						'value' => $value === true ? 'value + 1' : ($value === false ? 'value - 1' : $value),
						'variable' => $variable,
					)
				);
			}
			$smcFunc['db_query']('', '
					UPDATE {db_prefix}tp_settings
					SET value = {' . ($value === false || $value === true ? 'raw' : 'string') . ':value}
					WHERE name = {string:variable}',
					array(
						'value' => $value === true ? 'value + 1' : ($value === false ? 'value - 1' : $value),
						'variable' => $variable,
					)
				);

			$context['TPortal'][$variable] = $value === true ? $context['TPortal'][$variable] + 1 : ($value === false ? $context['TPortal'][$variable] - 1 : $value);
		}
	}
	else
	{
		foreach ($addSettings as $variable => $value)
		{
			$smcFunc['db_query']('', '
				UPDATE {db_prefix}tp_settings
				SET value = {' . ($value === false || $value === true ? 'raw' : 'string') . ':value}
				WHERE name = {string:variable}',
				array(
					'value' => $value === true ? 'value + 1' : ($value === false ? 'value - 1' : $value),
					'variable' => $variable,
				)
			);
			$context['TPortal'][$variable] = $value === true ? $context['TPortal'][$variable] + 1 : ($value === false ? $context['TPortal'][$variable] - 1 : $value);
		}
	}
	// Clean out the cache and make sure the cobwebs are gone too.
	cache_put_data('tpSettings', null, 90);

	return;
}

function TPGetMemberColour($member_ids)
{
    global $smcFunc, $db_connection, $db_server, $db_name, $db_user, $db_passwd;
    global $db_prefix, $db_persist, $db_port, $db_mb4;

	if (empty($member_ids)) {
		return false;
    }

    // SMF2.1 and php < 7.0 need this
    if (empty($db_connection)) {
        $db_options = array();
        // Add in the port if needed
        if (!empty($db_port)) {
            $db_options['port'] = $db_port;
        }
        if (!empty($db_mb4)) {
            $db_options['db_mb4'] = $db_mb4;
        }
        $options = array_merge($db_options, array('persist' => $db_persist, 'dont_select_db' => SMF == 'SSI'));
        $db_connection = smf_db_initiate($db_server, $db_name, $db_user, $db_passwd, $db_prefix, $options);
    }

	$member_ids = is_array($member_ids) ? $member_ids : array($member_ids);

    $request = $smcFunc['db_query']('', '
            SELECT mem.id_member, mgrp.online_color AS mg_online_color, pgrp.online_color AS pg_online_color
            FROM {db_prefix}members AS mem
            LEFT JOIN {db_prefix}membergroups AS mgrp
                ON (mgrp.id_group = mem.id_group)
            LEFT JOIN {db_prefix}membergroups AS pgrp
                ON (pgrp.id_group = mem.id_post_group)
            WHERE mem.id_member IN ({array_int:member_ids})',
		    array(
			    'member_ids'	=> $member_ids,
		    )
    );

    $mcol = array();
    if($smcFunc['db_num_rows']($request) > 0) {
        while ($row = $smcFunc['db_fetch_assoc']($request)) {
            $mcol[$row['id_member']]    = !empty($row['mg_online_color']) ? $row['mg_online_color'] : $row['pg_online_color'];
        }
        $smcFunc['db_free_result']($request);
    }

    return $mcol;
}

// profile summary
function tp_profile_summary($memID)
{
	global $txt, $context, $smcFunc;
	$context['page_title'] = $txt['tpsummary'];
	// get all articles written by member
	$request =  $smcFunc['db_query']('', '
		SELECT COUNT(*) FROM {db_prefix}tp_articles
		WHERE author_id = {int:author}',
		array('author' => $memID)
	);
	$result = $smcFunc['db_fetch_row']($request);
	$max_art = $result[0];
	$smcFunc['db_free_result']($request);
	$max_upload = 0;
	if($context['TPortal']['show_download'])
	{
		// get all uploads
		$request =  $smcFunc['db_query']('', '
			SELECT COUNT(*) FROM {db_prefix}tp_dlmanager
			WHERE author_id = {int:author} AND type = {string:type}',
			array('author' => $memID, 'type' => 'dlitem')
		);
		$result = $smcFunc['db_fetch_row']($request);
		$max_upload = $result[0];
		$smcFunc['db_free_result']($request);
	}
	$context['TPortal']['tpsummary']=array(
		'articles' => $max_art,
		'uploads' => $max_upload,
	);
}

// articles and comments made by the member
function tp_profile_articles($member_id) {{{
	global $txt, $context, $scripturl, $smcFunc;

	$context['page_title'] = $txt['articlesprofile'];
    $context['TPortal']['memID'] = $member_id;

    $tpArticle  = TPArticle::getInstance();
	$start      = 0;
	$sorting    = 'date';

    if(isset($context['TPortal']['mystart'])) {
		$start = is_numeric($context['TPortal']['mystart']) ? $context['TPortal']['mystart'] : 0;
    }

    if($context['TPortal']['tpsort'] != '') {
        $sorting = $context['TPortal']['tpsort'];
        if(!in_array($sorting, array('date', 'subject', 'views', 'category', 'comments'))) {
            $sorting = 'date';
        }
    }

	// get all articles written by member
    $max        = $tpArticle->getTotalAuthorArticles($member_id, false, true);

	// get all not approved articles
    $max_approve= $tpArticle->getTotalAuthorArticles($member_id, false, false);

	// get all articles currently being off
    $max_off    = $tpArticle->getTotalAuthorArticles($member_id, true, true);

	$context['TPortal']['all_articles']         = $max;
	$context['TPortal']['approved_articles']    = $max_approve;
	$context['TPortal']['off_articles']         = $max_off;

	$request = $smcFunc['db_query']('', '
		SELECT art.id, art.date, art.subject, art.approved, art.off, art.comments, art.views, art.rating, art.voters,
			art.author_id as authorID, art.category, art.locked
		FROM {db_prefix}tp_articles AS art
		WHERE art.author_id = {int:auth}
		ORDER BY art.{raw:sort} {raw:sorter} LIMIT 15 OFFSET {int:start}',
		array('auth' => $member_id,
		'sort' => $sorting,
		'sorter' => in_array($sorting, array('date', 'views', 'comments')) ? 'DESC' : 'ASC',
		'start' => $start
		)
	);

	if($smcFunc['db_num_rows']($request) > 0){
		while($row = $smcFunc['db_fetch_assoc']($request)) {
			$rat = array();
			$rating_votes = 0;
			$rat = explode(',', $row['rating'] ?? '');
			$rating_votes = count($rat);
			if($row['rating'] == '') {
				$rating_votes = 0;
            }
			$total = 0;
			foreach($rat as $mm => $mval) {
				if(is_numeric($mval)) {
					$total = $total + $mval;
                }
			}
			if($rating_votes > 0 && $total > 0) {
				$rating_average = floor($total / $rating_votes);
            }
			else {
				$rating_average = 0;
            }
			$can_see = true;
			if(($row['approved'] != 1 || $row['off'] == 1)) {
				$can_see = allowedTo('tp_articles');
            }
			if($can_see) {
				$context['TPortal']['profile_articles'][] = array(
					'id' => $row['id'],
					'subject' => $row['subject'],
					'date' => timeformat($row['date']),
					'timestamp' => $row['date'],
					'href' => '' . $scripturl . '?page='.$row['id'],
					'comments' => $row['comments'],
					'views' => $row['views'],
					'rating_votes' => $rating_votes,
					'rating_average' => $rating_average,
					'approved' => $row['approved'],
					'off' => $row['off'],
					'locked' => $row['locked'],
					'catID' => $row['category'],
					'category' => '<a href="'.$scripturl.'?cat='.(isset($context['TPortal']['categories_shortname'][$row['category']]) ? $context['TPortal']['categories_shortname'][$row['category']] : '').'">' . (isset($context['TPortal']['catnames'][$row['category']]) ? $context['TPortal']['catnames'][$row['category']] : '') .'</a>',
					'editlink' => allowedTo('tp_articles') ? $scripturl.'?action=tpadmin;sa=editarticle'.$row['id'] : $scripturl.'?action=tportal;sa=editarticle'.$row['id'],
				);
            }
		}
		$smcFunc['db_free_result']($request);
	}

    // construct pageindexes
	$context['TPortal']['pageindex'] = '';
	if($max > 0) {
		$context['TPortal']['pageindex'] = TPageIndex($scripturl.'?action=profile;area=tparticles;u='.$member_id.';tpsort='.$sorting, $start, $max, '15');
    }

	// setup subaction
	$context['TPortal']['profile_action'] = '';
	if(isset($_GET['sa']) && $_GET['sa'] == 'settings') {
		$context['TPortal']['profile_action'] = 'settings';
    }

	// Create the tabs for the template.
	$context[$context['profile_menu_name']]['tab_data'] = array(
		'title' => $txt['articlesprofile'],
		'description' => $txt['articlesprofile2'],
		'tabs' => array(
			'articles' => array(),
			'settings' => array(),
			),
	);
	// setup values for personal settings - for now only editor choice
	// type = 1 -
	// type = 2 - editor choice
	$result = $smcFunc['db_query']('', '
		SELECT id, value FROM {db_prefix}tp_data
		WHERE type = {int:type} AND id_member = {int:id_mem} LIMIT 1',
		array('type' => 2, 'id_mem' => $member_id)
	);
	if($smcFunc['db_num_rows']($result) > 0) {
		$row = $smcFunc['db_fetch_assoc']($result);
		$context['TPortal']['selected_member_choice'] = $row['value'];
		$context['TPortal']['selected_member_choice_id'] = $row['id'];
		$smcFunc['db_free_result']($result);
	}
	else {
		$context['TPortal']['selected_member_choice'] = 0;
		$context['TPortal']['selected_member_choice_id'] = 0;
	}

    $context['TPortal']['selected_member'] = $member_id;
	if(loadLanguage('TPortalAdmin') == false) {
		loadLanguage('TPortalAdmin', 'english');
    }

}}}

function tp_profile_download($memID)
{
	global $txt, $context, $scripturl, $smcFunc;
	$context['page_title'] = $txt['downloadsprofile'] ;
	// is dl manager on?
	if($context['TPortal']['show_download']==0)
      fatal_lang_error('tp-dlmanageroff', false);
	if(isset($context['TPortal']['mystart']))
		$start = $context['TPortal']['mystart'];
	else
		$start = 0;
	$context['TPortal']['memID'] = $memID;
	if($context['TPortal']['tpsort'] != '')
		$sorting = $context['TPortal']['tpsort'];
	else
		$sorting = 'date';
	$max = 0;
	// get all uploads
	$request = $smcFunc['db_query']('', '
		SELECT COUNT(*) FROM {db_prefix}tp_dlmanager
		WHERE author_id = {int:auth} AND type = {string:type}',
		array('auth' => $memID, 'type' => 'dlitem')
	);
	$result = $smcFunc['db_fetch_row']($request);
	$max = $result[0];
	$smcFunc['db_free_result']($request);
	// get all not approved uploads
	$request = $smcFunc['db_query']('', '
		SELECT COUNT(*) FROM {db_prefix}tp_dlmanager
		WHERE author_id = {int:auth}
		AND type = {string:type}
		AND category < 0',
		array('auth' => $memID, 'type' => 'dlitem')
	);
	$result = $smcFunc['db_fetch_row']($request);
	$max_approve = $result[0];
	$smcFunc['db_free_result']($request);
	$context['TPortal']['all_downloads'] = $max;
	$context['TPortal']['approved_downloads'] = $max_approve;
	$context['TPortal']['profile_uploads'] = array();
	if(!in_array($sorting, array('name', 'created', 'views', 'downloads', 'category')))
		$sorting = 'created';
	$request = $smcFunc['db_query']('', '
		SELECT id, name, category, downloads, views, created, filesize, rating, voters
		FROM {db_prefix}tp_dlmanager
		WHERE author_id = {int:auth}
		AND type = {string:type}
		ORDER BY {raw:sort} {raw:sorter} LIMIT 15 OFFSET {int:start}',
		array('auth' => $memID,
		'type' => 'dlitem',
		'sort' => $sorting,
		'sorter' => in_array($sorting, array('created', 'views', 'downloads')) ? 'DESC' : 'ASC',
		'start' => $start)
	);
	if($smcFunc['db_num_rows']($request) > 0)
	{
		while($row = $smcFunc['db_fetch_assoc']($request))
		{
			$rat = array();
			$rating_votes = 0;
			$rat = explode(',', $row['rating'] ?? '');
			$rating_votes = count($rat);
			if($row['rating'] == '')
				$rating_votes = 0;
			$total = 0;
			foreach($rat as $mm => $mval)
			{
				if(is_numeric($mval))
					$total = $total + $mval;
			}
			if($rating_votes > 0 && $total > 0)
				$rating_average = floor($total / $rating_votes);
			else
				$rating_average = 0;
			$editlink = '';
			if(allowedTo('tp_dlmanager'))
				$editlink = $scripturl.'?action=tportal;sa=download;dl=adminitem'.$row['id'];
			elseif($memID == $context['user']['id'])
				$editlink = $scripturl.'?action=tportal;sa=download;dl=useredit'.$row['id'];
			$context['TPortal']['profile_uploads'][] = array(
				'id' => $row['id'],
				'name' => $row['name'],
				'created' => timeformat($row['created']),
				'category' => $row['category'],
				'href' => $scripturl . '?action=tportal;sa=download;dl=item'.$row['id'],
				'views' => $row['views'],
				'rating_votes' => $rating_votes,
				'rating_average' => $rating_average,
				'approved' => $row['category']>0 ? '1' : '0',
				'downloads' => $row['downloads'],
				'catID' => abs($row['category']),
				'category' => $row['category'],
				'editlink' => $editlink,
			);
		}
		$smcFunc['db_free_result']($request);
	}
	// construct pageindexes
	if($max > 0)
		$context['TPortal']['pageindex']=TPageIndex($scripturl.'?action=profile;area=tpdownload;u='.$memID.';tpsort='.$sorting, $start, $max, '15');
	else
		$context['TPortal']['pageindex'] = '';
}

function tp_pro_shoutbox()
{
	global $txt, $context;
	$context['page_title'] = $txt['tp-shouts'];
}

// TinyPortal
function tp_summary($memID)
{
	global $txt, $context;
	loadtemplate('TPprofile');
	$context['page_title'] = $txt['tpsummary'];
	tp_profile_summary($memID);
}

function tp_articles($memID)
{
	global $txt, $context;
	TP_article_categories();
	loadtemplate('TPprofile');
	$context['page_title'] = $txt['articlesprofile'];
	tp_profile_articles($memID);
}

function tp_download($memID)
{
	global $txt, $context;
	loadtemplate('TPprofile');
	$context['page_title'] = $txt['downloadsprofile'];
	tp_profile_download($memID);
}

if (!function_exists('is_countable')) {
    function is_countable($var) {
        return ( is_array($var) || $var instanceof Countable || $var instanceof \SimpleXMLElement || $var instanceof \ResourceBundle );
    }
}

function TPSaveSettings() {{{
    global $context, $smcFunc;
    // check the session
    checkSession('post');
    $member_id  = TPUtil::filter('memberid', 'post', 'int');
    $item       = TPUtil::filter('item', 'post', 'int');
    $value      = TPUtil::filter('tpwysiwyg', 'post', 'int');
    if( $value !== false ) {
        if( $item > 0 ) {
            $smcFunc['db_query']('', '
                UPDATE {db_prefix}tp_data
                SET value = {int:val} WHERE id = {int:id}',
                array('val' => $value, 'id' => $item)
            );
        }
        elseif ($member_id != false) {
            $smcFunc['db_insert']('INSERT',
                '{db_prefix}tp_data',
                array('type' => 'int', 'id_member' => 'int', 'value' => 'int'),
                array(2, $member_id, $value),
                array('id')
            );
        }
    }

    // go back to profile page
    redirectexit('action=profile;u='.$member_id.';area=tparticles;sa=settings');

}}}

function TPUpdateLog() {{{
    global $context, $smcFunc;

    $context['TPortal']['subaction'] = 'updatelog';
    $request = $smcFunc['db_query']('', '
        SELECT value1 FROM {db_prefix}tp_variables
        WHERE type = {string:type} ORDER BY id DESC',
        array('type' => 'updatelog')
    );
    if($smcFunc['db_num_rows']($request) > 0) {
        $check = $smcFunc['db_fetch_assoc']($request);
        $context['TPortal']['updatelog'] = $check['value1'];
        $smcFunc['db_free_result']($request);
    }
    else {
        $context['TPortal']['updatelog'] = "";
    }
    loadtemplate('TPsubs');
    $context['sub_template'] = 'updatelog';

}}}

?>
