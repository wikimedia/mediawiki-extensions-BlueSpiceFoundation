<?php

class BsBaseTemplate extends BaseTemplate {

	function __construct() {
		parent::__construct();
		global $wgHooks;
		$wgHooks['BSWidgetBarGetDefaultWidgets'][] = array(&$this, 'onBSWidgetBarGetDefaultWidgets');
		$wgHooks['BSWidgetListHelperInitKeyWords'][] = array(&$this, 'onBSWidgetListHelperInitKeyWords');

		$this->data['bs_search_input'] = array(
			'id' => 'searchInput',
			'type' => 'text'
		);
		$this->data['bs_search_hidden_fields'] = array();
		$this->data['bs_title_actions'] = array();
		$this->data['bs_personal_info'] = array();
		$this->data['bs_dataBeforeContent'] = array();
		$this->data['bs_dataAfterContent'] = array();
		$this->data['bs_navigation_main'] = array(
			'navigation' => array(
				'position' => 10,
				'label' => '',
				'content' => '' //Gets filled in "execute"
			)
		);
		$this->data['bs_navigation_sites'] = '';
	}

	/**
	 * Returns or echoes HTML generated from an ambigous data array
	 * @param mixed $data Can contain "ViewBaseElement"s,
	 * "BaseTemplate::makeListItem"-arrays or strings. Or it can be a string
	 * @param bool $return Wether to return the gernerated HTML or output it
	 * directly to stdout
	 * @return string Empty string if $return == false, otherwise the HTML
	 * produced from the $data array
	 */
	protected function processDataArray( $data, $return = false ) {
		$out = '';
		$itemHTML = '';

		foreach ( $data as $key => $item ) {
			if ( $item instanceof ViewBaseElement ) {
				$itemHTML = $item->execute ();
			}
			elseif(is_array( $item )) {
				if( $item['content'] && $item['content'] instanceof ViewBaseElement ) {
					$itemHTML = $item['content']->execute();
				}
				elseif( $item['id'] ) {
					$itemHTML = $this->makeListItem($item['id'], $item);
				}
			}
			elseif(is_string($item)) {
				$itemHTML = $item;
			}

			if( $return === true ) {
				$out .= $itemHTML;
			}
			else {
				echo $itemHTML;
			}
		}
		return $out;
	}

	/**
	 * Returns or echoes HTML generated from an ambigous data array. Performs
	 * a ksort on the array first
	 * @param array $data Can contain "ViewBaseElement"s,
	 * "BaseTemplate::makeListItem"-arrays or strings
	 * @param bool $return Wether to return the gernerated HTML or output it
	 * directly to stdout
	 * @return string Empty string if $return == false, otherwise the HTML
	 * produced from the $data array
	 */
	protected function processData( $data, $return = false ) {
		if( is_array( $data ) ) {
			$this->sortDataArrayByPosition($data);
			return $this->processDataArray($data, $return);
		}
		elseif( is_string( $data ) ) {
			if( !$return ) {
				var_dump($data);
				echo $data;
			}
			return $data;
		}
	}

	/**
	 * Expects arrays with index 'position' of type integer
	 * @param array $itemA
	 * @param array $itemB
	 * @return int The result of comparison
	 */
	protected function sortDataArrayByPositionCallback( $itemA, $itemB ) {
		$result = 0;
		if( !isset( $itemA['position'] ) || !isset( $itemB['position'] ) ) {
			return $result;
		}
		if( $itemA['position'] > $itemB['position'] ) {
			$result = 1;
		}
		else {
			$result = -1;
		}
		return $result;
	}

	public function sortDataArrayByPosition( &$data ) {
		uasort( $data, array( $this, 'sortDataArrayByPositionCallback' ) );
	}

	//TODO: Maybe do this in a 'SkinTemplateOutputPageBeforeExec' hook
	//handler in BSF. Would need to be run as last handler
	protected function prepareData() {
		//Add the default print link to a title
		$this->data['bs_title_actions'][10] = array(
			'id' => 'bs-ta-print',
			'href' => $this->getSkin()->getTitle()
				->getLocalURL( array( 'printable' => 'yes' ) ),
			'title' => $this->getMsg('bs-title-actions-print-title')->text(),
			'text' => $this->getMsg('bs-title-actions-print-text')->text(),
			'class' => 'icon-print'
		);

		//To avoid an additional output statement we attach
		//"bs_dataAfterContent" to "dataAfterContent"
		$this->prepareDataAfterContent();

		//Fill in content of "navigation tab" at "execute" time
		$this->data['bs_navigation_main']['navigation']['label'] = $this->getMsg( 'bs-tab_navigation' )->text();
		$this->data['bs_navigation_main']['navigation']['content'] = $this->getNavigationSidebar();
		$this->data['bs_navigation_main']['navigation']['class'] = 'icon-menu2';
		$this->sortDataArrayByPosition( $this->data['bs_navigation_main'] );

		ksort( $this->data['bs_personal_info'] );
	}

	/**
	 * Transfers $this->data['bs_dataAfterContent'] to
	 * $this->data['dataAfterContent']. Very skin dependent. Usually is is
	 * rendered as jquery.ui-tabbable markup
	 * @return boolean Wether or not the data was could be processed
	 */
	protected function prepareDataAfterContent() {
		if ( !isset($this->data['bs_dataAfterContent']) ) {
			return false;
		}

		$this->sortDataArrayByPosition($this->data['bs_dataAfterContent']);

		$aDACList = array();
		$aDACList[] = '<ul id="bs-data-after-content-tabs">';
		$aDACContents = array();
		foreach( $this->data['bs_dataAfterContent'] as $key => $item ) {
			if ( $item instanceof ViewBaseElement ) {
				$aDACList[] = $item->execute();
			} else {
				$aDACList[] = Html::rawElement(
					'li',
					array(),
					Html::element(
						'a',
						array(
							'href' => '#'.$key
						),
						$item['label']
					)
				);
				$content = $item['content'];
				if( $item['content'] instanceof ViewBaseElement ) {
					$content = $item['content']->execute();
				}

				$aDACContents[] = Html::rawElement(
					'div',
					array(
						'id' => $key
					),
					$content
				);
			}
		}
		$aDACList[] = '</ul>';

		$this->set(
			'dataAfterContent',
			$this->data['dataAfterContent'].
			'<div id="bs-data-after-content">'.
			implode( "\n", $aDACList ).
			implode( "\n", $aDACContents ).
			'</div>'
		);

		return true;
	}

	protected function printLogo() {
		?>
		<div id="bs-logo" role="banner">
			<a style="background-image: url(<?php $this->text( 'logopath' ) ?>);"
			   href="<?php echo htmlspecialchars( $this->data['nav_urls']['mainpage']['href'] ) ?>"
			   <?php echo $this->tooltipAndAccesskeyAttribs( 'p-logo' ); ?>>
			</a>
		</div>
		<?php
	}

	protected function printNavigationMain() {
		?>
		<div id="bs-nav-sections">
			<h2><?php $this->msg( 'navigation-heading' ) ?></h2>
			<ul id ="bs-nav-tabs">
				<?php
				foreach( $this->data['bs_navigation_main'] as $key => $data ) {
					?>
				<li>
					<a id="bs-tab-<?php echo $key;
						?>" href="#bs-nav-section-<?php echo $key;
						?>" title="<?php echo $data['label'];
						?>" class="bs-nav-tab-icon <?php echo $data['class']
						?>">
						<span><?php echo $data['label'] ?></span>
					</a>
				</li>
					<?php
				}
				?>
			</ul>
			<?php
			foreach( $this->data['bs_navigation_main'] as $key => $data ) {
				?>
			<div id="bs-nav-section-<?php echo $key; ?>" class="bs-nav-tab">
			<?php echo $data['content']; ?>
			</div>
				<?php
			}
			?>
		</div>
		<?php
	}

	protected function printDataBeforeContent() {
		//tbd get data from prebodyhtml, no need for a switch then
		if ( $this->data['bs_dataBeforeContent'] ) {
			echo Html::rawElement(
				'div',
				array(
					'id' => 'bs-data-before-content',
					'class' => 'clearfix'
				),
				$this->processData($this->data['bs_dataBeforeContent'], true)
			);
		}
	}

	protected function printDataAfterContent() {
		if ( $this->data['dataAfterContent'] ) { //MW standard
			echo $this->html( 'dataAfterContent' );
		}
	}

	protected function printContentActions(){
		//MediaWiki standard navigation elements for content area
		$namespaces = $this->data['content_navigation']['namespaces'];
		$variants = $this->data['content_navigation']['variants']; //Not used at the moment
		$views = $this->data['content_navigation']['views'];
		$actions = $this->data['content_navigation']['actions'];
		?>
		<div id='left-navigation'>
			<div id="p-namespaces" role="navigation" class="<?php if ( count( $namespaces ) == 0 ) { echo ' emptyPortlet'; } ?>" aria-labelledby="p-namespaces-label">
				<h3 id="p-namespaces-label"><?php $this->msg( 'namespaces' ) ?></h3>
				<ul<?php $this->html( 'userlangattributes' ) ?>>
					<?php foreach ( $namespaces as $key => $item){
						echo $this->makeListItem( $key, $item );
					}?>
				</ul>
			</div>
		</div>
		<div id='right-navigation'>
			<div id="p-views" role="navigation" class="<?php if ( count( $views ) == 0 ) { echo ' emptyPortlet'; } ?>" aria-labelledby="p-views-label">
				<h3 id="p-views-label"><?php $this->msg( 'views' ) ?></h3>
				<ul<?php $this->html( 'userlangattributes' ) ?>>
					<?php foreach ( $views as $key => $item){
						echo $this->makeListItem( $key, $item );
					}?>
				</ul>
			</div>
			<div id="p-cactions" role="navigation" aria-labelledby="p-cactions-label">
				<h3 id="p-cactions-label"><?php $this->msg( 'actions' ) ?></h3>
				<ul>
					<?php if ( count( $actions ) > 0 ) { ?>
					<li id="bs-cactions-button" class="">
						<a href="#" class="icon-arrow-down9-after"><span><?php $this->msg( 'bs-tools-button' ) ?></span></a>
						<div class="menu">
							<ul<?php $this->html( 'userlangattributes' ) ?>>
							<?php foreach ( $actions as $key => $item){
								echo $this->makeListItem( $key, $item );
							}?>
							</ul>
						</div>
					</li>
					<?php } ?>
				</ul>
			</div>
		</div>
		<?php
	}

	protected function printSiteNotice() {
		if ( $this->data['sitenotice'] ) {
			echo '<div id="siteNotice">';
			echo $this->html( 'sitenotice' );
			echo '</div>';
		}
	}

	protected function printFirstHeading() {
		$sStyle = '';
		if( $this->getSkin()->getTitle()->isMainPage() ) {
			$sStyle = ' style="display: none"';
		}

		//From Vector
		$this->data['pageLanguage'] =
			$this->getSkin()->getTitle()->getPageViewLanguage()->getHtmlCode();
		?>
		<h1 id="firstHeading" class="firstHeading" lang="<?php
				$this->text( 'pageLanguage' );
			?>"<?php echo $sStyle;
			?>><span dir="auto"><?php $this->html( 'title' ) ?></span></h1>
		<?php
	}

	protected function getToolboxMarkUp( $bRenderHeading = true ) {
		$aToolboxLinkList = array();
		foreach ( $this->getToolbox() as $key => $tbitem ) {
			$aToolboxLinkList[] = $this->makeListItem( $key, $tbitem );
		}

		$sToolboxLinkList = implode("\n", $aToolboxLinkList);

		ob_start();
		wfRunHooks('SkinTemplateToolboxEnd', array(&$this));
		$sToolboxEndLinkList = ob_get_contents();
		ob_end_clean();

		$aOut = array();
		$aOut[] = '<div class="portlet bs-nav-links" id="p-tb">';
		if ( $bRenderHeading === true ) {
			$aOut[] = '  <h5>' . $this->translator->translate( 'toolbox' ) . '</h5>';
		}
		$aOut[] = '  <div class="pBody">';
		$aOut[] = '    <ul>';
		$aOut[] = $sToolboxLinkList;
		$aOut[] = $sToolboxEndLinkList;
		$aOut[] = '    </ul>';
		$aOut[] = '  </div>';
		$aOut[] = '</div>';
		//getAfterPortlet runs BaseTamplate::renderAfterPortlet which is introduced in MediaWiki 1.23
		//$aOut[] = $this->getAfterPortlet( 'tb' );

		return implode( "\n", $aOut );
	}

	public function getToolbox() {
		$baseToolbox = parent::getToolbox();

		// adding link to Allpages
		$oAllPages = SpecialPageFactory::getPage( 'Allpages' );
		$baseToolbox['specialpageallpages'] = array(
			'href' => $oAllPages->getTitle()->getLinkURL(),
			'text' => $oAllPages->getDescription(),
			'id' => 't-allpages'
		);

		return $baseToolbox;
	}

	// introduced in MediaWiki 1.23
//	protected function getAfterPortlet($name) {
//		ob_start();
//		$this->renderAfterPortlet($name);
//		$sAfterPortlet = ob_get_contents();
//		ob_end_clean();
//
//		return $sAfterPortlet;
//	}

	protected function printToolBox() {
		echo $this->getToolboxMarkUp();
	}

	public function getToolBoxWidget() {
		$oWidgetView = new ViewWidget();
		$oWidgetView->setId('bs-toolbox')
			->setTitle($this->translator->translate('toolbox'))
			->setBody($this->getToolboxMarkUp(false))
			->setTooltip($this->translator->translate('toolbox'));

		return $oWidgetView;
	}

	public function onBSWidgetBarGetDefaultWidgets( &$aViews, $oUser, $oTitle ) {
		if ( !isset( $this->data['sidebar']['TOOLBOX'] ) ) {
			$aViews[] = $this->getToolBoxWidget();
		}
		return true;
	}

	public function onBSWidgetListHelperInitKeyWords( &$aKeywords, $oTitle ) {
		$aKeywords['TOOLBOX'] = array($this, 'getToolBoxWidget');
		return true;
	}

	protected function getNavigationSidebar() {
		$aPortlets = array();

		foreach ($this->data['sidebar'] as $bar => $cont) {
			$sTitle = ( wfMessage( $bar )->isBlank() ) ? $bar : wfMessage( $bar )->plain();

			$aOut = array();

			if ( $bar == 'TOOLBOX' ) {
				$aPortlets[$bar] = $this->getToolboxMarkUp();
				continue;
			}
			if ( $cont ) {
				$aOut[] = '<div id="p-' . Sanitizer::escapeId( $bar ) . '" class="bs-nav-links">';
				$aOut[] = '  <h5>' . $sTitle . '</h5>';
				$aOut[] = '  <ul>';
				foreach ($cont as $key => $val) {
					if ( strpos( $val['text'], "|" ) !== false ) {
						$aVal = explode( '|', $val['text'] );
						$val['id'] = 'n-' . $aVal[0];
					}

					$sCssClass = (!isset($val['active']) ) ? ' class="active"' : '';
					$sTarget = ( isset($val['target']) ) ? ' target="' . $val['target'] . '"' : '';
					$sRel = ( isset($val['rel']) ) ? ' rel="' . $val['rel'] . '"' : '';
					$aOut[] = '<li id="' . Sanitizer::escapeId($val['id']) . '"' . $sCssClass . ' class="clearfix">';
					if ( !empty( $aVal ) ) {
						$oFile = wfFindFile( $aVal[1] );
						if ( strpos( $lang = $this->translator->translate( $aVal[0] ), "&lt;" ) === false ) {
							$aVal[0] = $lang;
						}

						if ( is_object( $oFile ) && $oFile->exists() ) {
							if ( BsExtensionManager::isContextActive( 'MW::SecureFileStore::Active' ) ) {
								$sUrl = SecureFileStore::secureStuff( $oFile->getUrl(), true );
							} else {
								$sUrl = $oFile->getUrl();
							}
							$aOut[] = '<div style="background:url(' . $sUrl . ') center no-repeat; width:24px; height:24px;" class="left_navigation_icon" ></div>';
						} else {
							//default
							$aOut[] = '<div class="left_navigation_icon"></div>';
						}
						$aOut[] = '<a href="' . htmlspecialchars($val['href']) . '" title="' . htmlspecialchars($aVal[0]) .'" ' . $sTarget . $sRel . '>' . htmlspecialchars($aVal[0]) . '</a>';
					} else {
						$aOut[] = '<div class="left_navigation_icon"></div>';
						$aOut[] = '<a href="' . htmlspecialchars($val['href']) . '" title="' . htmlspecialchars($val['text']) .'" ' . $sTarget . $sRel . '>' . htmlspecialchars($val['text']) . '</a>';
					}
					$aOut[] = '</li>';
					unset( $aVal );
				}
				$aOut[] = '</ul>';
				$aOut[] = '</div>';
				//getAfterPortlet runs BaseTamplate::renderAfterPortlet which is introduced in MediaWiki 1.23
				//$aOut[] = $this->getAfterPortlet( $bar );

				$aPortlets[$bar] = implode("\n", $aOut);
			}
		}

		if ($this->data['language_urls']) {
			wfRunHooks( 'otherlanguages', array( $this, true ) );
			$aOut = array();
			$aOut[] = '<div title="' . wfMessage('otherlanguages')->plain() . '" id="p-lang" class="bs-widget portal">';
			$aOut[] = '  <div class="bs-widget-head">';
			$aOut[] = '    <h5 class="bs-widget-title" ' . $this->data['userlangattributes'] . '>' . wfMessage('otherlanguages')->plain() . '</h5>';
			$aOut[] = '  </div>';
			$aOut[] = '  <div class="bs-widget-body bs-nav-links">';
			$aOut[] = '    <ul>';
			foreach ($this->data['language_urls'] as $langlink) {
				$aOut[] = '      <li class="' . htmlspecialchars($langlink['class']) . '">';
				$aOut[] = '        <a href="' . htmlspecialchars($langlink['href']) . '">' . $langlink['text'] . '</a>';
				$aOut[] = '      </li>';
			}
			$aOut[] = '    </ul>';
			$aOut[] = '  </div>';
			$aOut[] = '</div>';
			$aPortlets['language_urls'] = implode("\n", $aOut);
		}

		$aOut = array();
		foreach ($aPortlets as $sKey => $vPortlet) {
			if ($vPortlet instanceof ViewBaseElement) {
				$aOut[] = $vPortlet->execute();
			} else {
				$aOut[] = $vPortlet; //Check for string?
			}
		}
		return implode("\n", $aOut);
	}

	/**
	 * @global Title $wgTitle
	 * @global User $wgUser
	 * @global WebRequest $wgRequest
	 */
	protected function printPersonalTools() {
		$oUser = $this->getSkin()->getUser();
		$aOut = array();
		$aOut[] = '<div id="bs-user-container">';
		$aOut[] = '  <div id="bs-button-user">';
		$aOut[] = '    <h3 id="p-personal-label">'.$this->getMsg( 'personaltools' )->text().'</h3>';
		if ( !$oUser->isAnon() ) {
			$aOut[] = "<div id='bs-personal-name'>";
			$aOut[] = BsCore::getUserDisplayName();
			$aOut[] = "</div>";
		}

		if ($oUser->isLoggedIn())
			$aOut[] = BsCore::getInstance()->getUserMiniProfile($oUser, array("width" => "32", "height" => "32"))->execute();
		else
			$aOut[] = "<span class='bs-personal-not-loggedin'>" . Linker::link(SpecialPage::getTitleFor('login'), wfMessage("login")->plain()) . "</span>";

		$this->printPersonalInfo($aOut);

		$personalTools = $this->getPersonalTools();
		$aOut[] = '    <div id="bs-personal-menu-container">';
		$aOut[] = '      <ul id="bs-personal-menu" '.$this->data['userlangattributes'].'>';
		foreach ( $personalTools as $key => $item ) {
			$aOut[] = $this->makeListItem( $key, $item );
		}
		$aOut[] = '      </ul>';
		$aOut[] = '    </div>';
		$aOut[] = '  </div>';
		$aOut[] = '</div>';

		echo implode("\n", $aOut);
	}

	protected function printPersonalInfo(&$aOut){
		$aOut[] = '<ul id="bs-personal-info">';
		foreach( $this->data['bs_personal_info'] as $item ) {
			$sActiveClass = $item['active'] ? 'active' : '';
			$aOut[] = Html::rawElement(
				'li',
				array(
					'id' => $item['id'],
					'class' => $sActiveClass
				),
				Html::element(
					'a',
					array(
						'href' => $item['href'],
						'class' => $item['class'].' '.$sActiveClass
					),
					$item['text']
				)
			);
		}
		$aOut[] = '</ul>';
		return true;
	}

	protected function printSkyScraper() {
		if ( isset( $this->data['bs_skyscraper'] ) ) {
			echo Html::rawElement(
				'div',
				array( 'id' => 'bs-skyscraper' ),
				$this->html( 'bs_skyscraper' )
			);
		}
	}

	protected function printNavigationSites() {
		$aOut = array();
		$aOut[] = '<div id="bs-apps">';
		if($this->data['bs_navigation_sites']) {
			$aOut[] = $this->data['bs_navigation_sites'];
		}
		$aOut[] = '</div>';
		echo implode("\n", $aOut);
	}

	protected function printSearchBox() {
		?>
<div id="p-search" role="search">
	<h3<?php $this->html( 'userlangattributes' ) ?>><label for="searchInput"><?php $this->msg( 'search' ) ?></label></h3>
		<form action="<?php $this->text( 'wgScript' ) ?>" id="searchform">
			<?php
			$class = $this->data['rtl'] ? "search-rtl" : "search-ltr";
			$attrs = array(
				'id' => 'searchButton',
				'type' => 'submit',
				'name' => 'button',
				'alt' => wfMessage( "searchbutton" )->plain(),
				'width' => '12',
				'height' => '13',
				'class' => $class . ' icon-search-light'
			);
			$attrs += Linker::tooltipAndAccesskeyAttribs( 'search-fulltext' );

			if ( $this->data['rtl'] ) {
				echo HTML::element( 'button', $attrs );
			}
			echo $this->makeSearchInput( $this->data['bs_search_input'] );
			if ( !$this->data['rtl'] ) {
				echo HTML::element( 'button', $attrs );
			}
			?>
			<input type='hidden' name="title" value="<?php $this->text( 'searchtitle' ) ?>"/>
			<?php
			foreach ( $this->data['bs_search_hidden_fields'] as $key => $value ) {
				echo Html::hidden( $key, $value );
			}
			?>
		</form>
</div>
<?php
	}

	protected function tooltipAndAccesskeyAttribs($sName) {
		return Xml::expandAttributes( Linker::tooltipAndAccesskeyAttribs( $sName ) );
	}

	public function printTitleActions() {
		$sAction = $this->getSkin()->getRequest()->getVal( 'action', 'view' );

		$aContentActions = $this->data['bs_title_actions'];
		if ( count( $aContentActions ) < 1 || $sAction != 'view'
				|| $this->getSkin()->getTitle()->isSpecialPage()) {
			return;
		}
		$aOut = array();
		$aOut[] = '<ul id="bs-title-actions">';
		foreach ($aContentActions as $aContentAction) {
			$aOut[] = '<li class="' . $aContentAction['id'] . '">';
			$aOut[] = Html::rawElement(
				'a',
				array(
					'href' => $aContentAction['href'],
					'title' => $aContentAction['title'],
					'class' => $aContentAction['class'],
					'id' => $aContentAction['id']
				),
				Html::element(
					'span',
					array(),
					$aContentAction['text']
				)
			);
			$aOut[] = "</li>";
		}
		$aOut[] = '</ul>';
		echo implode("\n", $aOut);
	}

	public function execute() {
		wfSuppressWarnings();
		$this->prepareData();
		$this->skin = $this->data['skin'];

		$this->html('headelement');
		wfRestoreWarnings();
	}

	/**
	 * Overrides base class method to make sure "login/logout" is always the
	 * last item
	 * @return array
	 */
	public function getPersonalTools() {
		$ptools = parent::getPersonalTools();

		if( $ptools['logout'] ) {
			$logout = $ptools['logout'];
			unset($ptools['logout']);
			$ptools['logout'] = $logout;
		}

		if( $ptools['login'] ) {
			$login = $ptools['login'];
			unset($ptools['login']);
			$ptools['login'] = $login;
		}

		return $ptools;
	}
}
