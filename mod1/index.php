<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Antoine <>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/


	// DEFAULT initialization of a module [BEGIN]
unset($MCONF);
require_once('conf.php');
require_once($BACK_PATH.'init.php');
require_once($BACK_PATH.'template.php');

$LANG->includeLLFile('EXT:file_search/mod1/locallang.xml');
require_once(PATH_t3lib.'class.t3lib_scbase.php');
//$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]



/**
 * Module 'Search file' for the 'file_search' extension.
 *
 * @author	Antoine <>
 * @package	TYPO3
 * @subpackage	tx_filesearch
 */
class  tx_filesearch_module1 extends t3lib_SCbase {
				var $pageinfo;

				/**
				 * Initializes the Module
				 * @return	void
				 */
				function init()	{
					global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

					parent::init();

					/*
					if (t3lib_div::_GP('clear_all_cache'))	{
						$this->include_once[] = PATH_t3lib.'class.t3lib_tcemain.php';
					}
					*/
				}

				/**
				 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
				 *
				 * @return	void
				 */
				function menuConfig()	{
					/*global $LANG;
					$this->MOD_MENU = Array (
						'function' => Array (
							'1' => $LANG->getLL('function1'),
							'2' => $LANG->getLL('function2'),
							'3' => $LANG->getLL('function3'),
						)
					);*/
					parent::menuConfig();
				}

				/**
				 * Main function of the module. Write the content to $this->content
				 * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
				 *
				 * @return	[type]		...
				 */
				function main()	{
					global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

					// Access check!
					// The page will show only if there is a valid page and if this page may be viewed by the user
					$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
					$access = is_array($this->pageinfo) ? 1 : 0;

					if (1==1)//($this->id && $access) || ($BE_USER->user['admin'] && !$this->id))	
					{
						// Draw the header.
						$this->doc = t3lib_div::makeInstance('bigDoc');
						$this->doc->backPath = $BACK_PATH;
						$this->doc->form='<form action="" method="POST">';

							// JavaScript
						$this->doc->JScode = '
							<script language="javascript" type="text/javascript">
								script_ended = 0;
								function jumpToUrl(URL)	{
									document.location = URL;
								}
							</script>
						';
						$this->doc->postCode='
							<script language="javascript" type="text/javascript">
								script_ended = 1;
								if (top.fsMod) top.fsMod.recentIds["web"] = 0;
							</script>
						';

						$headerSection = $this->doc->getHeader('pages',$this->pageinfo,$this->pageinfo['_thePath']).'<br />'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.path').': '.t3lib_div::fixed_lgd_pre($this->pageinfo['_thePath'],50);

						$this->content.=$this->doc->startPage($LANG->getLL('title'));
						$this->content.=$this->doc->header($LANG->getLL('title'));
						$this->content.=$this->doc->spacer(5);
						$this->content.=$this->doc->section('',$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function'])));
						$this->content.=$this->doc->divider(5);


						// Render content:
						$this->moduleContent();


						// ShortCut
						if ($BE_USER->mayMakeShortcut())	{
							$this->content.=$this->doc->spacer(20).$this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
						}

						$this->content.=$this->doc->spacer(10);
					} else {
							// If no access or if ID == zero

						$this->doc = t3lib_div::makeInstance('mediumDoc');
						$this->doc->backPath = $BACK_PATH;

						$this->content.=$this->doc->startPage($LANG->getLL('title'));
						$this->content.=$this->doc->header($LANG->getLL('title'));
						$this->content.=$this->doc->spacer(5);
						$this->content.=$this->doc->spacer(10);
					}
				}

				/**
				 * Prints out the module HTML
				 *
				 * @return	void
				 */
				function printContent()	{

					$this->content.=$this->doc->endPage();
					echo $this->content;
				}

				/**
				 * Generates the module content
				 *
				 * @return	void
				 */
				function moduleContent()	{
				  global $BE_USER;

				  $content='<BR><strong>Utilisez "*" pour n\'importe quelle chaine de caract&egrave;res. <BR>N\'utilisez pas de caract&egrave;res accentu&eacute;s.<BR><BR>Exemple   \'cathedrale*jpg\'  <BR><BR><br><br>
				  <div align="center"><form action="' . $_SERVER['PHP_SELF'] . '" method="POST">
    				  <input name="keywords" type="TEXT" size="50" value="expression" />
    				  <input type="SUBMIT" value="OK" />
				  </form></strong></div>';
$break = 0;
				  if (isset($_POST["keywords"])) {
				    $content .= "<h2><BR><BR>R&eacute;sulats : </h2><BR><h3>";

				    foreach ($BE_USER->groupData['filemounts'] as $key=> $filemount) {
				      $break = 0;

				      foreach ($BE_USER->groupData['filemounts'] as $key2=> $filemount2) {
				      //$content .= "path1 = ". $filemount['path'] . " , path2 = " . $filemount2['path'];			        
				      // Si une chemin déjà exploré ou à explorer contient déjà le notre on stop
				      $pos = strpos($filemount2['path']."coucou", $filemount['path']);				      
				      if (($pos === false) || ($filemount['path'] == $filemount2['path'])) { 
				          //$content .= " rien " . $filemount2['path'] . "<BR>";
				        }
				      else { 
				        //$content .= "substring : " . $filemount['path'];
				        $break = 1; 
				        break;
				        }
				      }
				      if ($break == 1) { 
				        break; 
				      }
				      else {
					$files = array();
					exec( "find " . $filemount['path'] . " -type f -iname \"*" . $_POST["keywords"] . "*\"", $files);
							  
				        for ($i = 0; $i < sizeof($files); $i++) {
					  $array = split("/fileadmin/", $files[$i]);
					  $array2 = split("/", $files[$i]);
					  $content .= "<a target=\"_blank\" href=\"http://" .  $_SERVER['SERVER_NAME'] . "/fileadmin/$array[1]\">". $array2[sizeof($array2)-1] . "</a><BR><BR>";
				        }
				      }
				    }
				    $content .= '</h3><BR><BR><BR><div align="center"><form action="' . $_SERVER['PHP_SELF'] . '" method="POST">
    				  <input name="keywords" type="TEXT" size="50" value="expression" />
    				  <input type="SUBMIT" value="OK" />
				  </form></strong></div>';
				  }
				  $this->content.=$this->doc->section('Recherche sur le nom des fichiers :',$content,0,1);
				}
		}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/file_search/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/file_search/mod1/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_filesearch_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>
