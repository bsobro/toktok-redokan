<?php

/**
 * Contains an array of tabs and displays them on the screen.
 *
 * @package WPFEPP
 * @since 2.3.0 
 **/
class WPFEPP_Tab_Collection {

	/**
	 * An array of WPFEPP_Tab objects.
	 *
     * @access private
	 * @var array
	 **/
	private $tabs;

	/**
	 * Class constructor. Initializes array of tabs.
	 **/
	function __construct()
	{
		$this->tabs = array();
	}

	/**
	 * Adds a tab to the array.
	 *
	 * @var WPFEPP_Tab $tab An object of WPFEPP_Tab class or any of its child classes.
	 **/
	public function add($tab) {
		$this->tabs[$tab->get_slug()] = $tab;
	}

	/**
	 * Removes a tab from the array.
	 *
	 * @var string $id Slug of the tag to be removed.
	 **/
	public function remove($id) {
		unset($this->tabs[$id]);
	}

	/**
	 * Calls the add_actions() function of each item in the tabs array.
	 **/
	public function add_actions(){
		foreach ($this->tabs as $id => $tab){
			$tab->add_actions();
		}
	}

	/**
	 * Outputs the HTML of tab navigation as well as the currently selected tab.
	 **/
	public function display() {
		$first_tab 		= reset($this->tabs);
		$current_tab 	= isset($_GET['tab']) ? $_GET['tab'] : $first_tab->get_slug();
		?>
			<h2 class="nav-tab-wrapper">
				<?php
					foreach ($this->tabs as $id => $tab):
						$tab_url 	= remove_query_arg( array('tab', 'updated', 'deleted', 'settings-updated') );
						$tab_url 	= esc_url( add_query_arg( array('tab' => $tab->get_slug()), $tab_url ) );
						$class 		= ( $tab->get_slug() == $current_tab ) ? 'nav-tab-active' : '';
				?>
					<a class="nav-tab <?php echo $class; ?>" href="<?php echo $tab_url; ?>"><?php echo $tab->get_name(); ?></a>
				<?php endforeach; ?>
			</h2>
			<div class="wpfepp-op <?php echo $current_tab; ?>-tab">
				<?php $this->tabs[$current_tab]->display(); ?>
			</div>
		<?php
	}
}