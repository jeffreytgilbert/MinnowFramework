<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */


/*****************************************************
// Example:
// 
// echo PageResults::display(0, 4231, 30, 'test.php');
*****************************************************/

/**
 * Create a standardized way to generate links for pages of sql results 
 * @package Navitation
 */
class PageData
{
	// original call args
	private $origin=0;
	private $start=0;
	private $total_items=0;
	private $items_per_page=0;
	private $pages_shown=7;
	private $url='';
//	private $css='pages';
//	private $style='standard';
	// calculated data
	private $total_pages=0;
	private $first_page=0;
	private $previous_page=0;
	private $next_page=0;
	private $last_page=0;
	private $this_page=0;
	private $remaining_items=0;
	// page links
	// - can be html, images, whatever
	private $first_button = '&laquo; First';
	private $back_button = '&#8249; Back';
	private $next_button = 'Next &#8250;';
	private $last_button = 'Last &raquo;';
	// - actual link with button inside it
	private $first_link = ''; 
	private $back_link = '';
	private $next_link = '';
	private $last_link = '';
	// store rendered pages so they dont rerender if they're recalled (its a speed thing)
	private $cache=array();
	
	public function getTotalItems(){
		return $this->total_items;
	}
	
	/**
	 * Return a list of pages in a string given the page position, total results, results per page, and url to target.
	 * @param int $start
	 * @param int $total_items
	 * @param int $items_per_page
	 * @param string $url
	 * @param string $css
	 * @param string $style
	 * @return string
	 */
	public function __construct($start, $total_items, $items_per_page, $pages_shown=7)
	{
		// set initial values 
		$this->origin=$start;
		$this->total_items=$total_items;
		$this->items_per_page=$items_per_page;
		$this->pages_shown=$pages_shown;
		
		// page math
		$this->total_pages = ($this->items_per_page > 0)?ceil($this->total_items/$this->items_per_page):0;
		
		$remainder=($this->items_per_page > 0)?$start%$this->items_per_page:0;
		if($remainder == 0) { $this->start=$start; }
		else { $this->start=$start-$remainder; }
		unset($remainder);
		
		if($this->total_pages > 0)
		{
			$this->previous_page=$this->start-$this->items_per_page;
			$this->next_page=$this->start+$this->items_per_page;
			$this->this_page= ($this->items_per_page > 0)?$this->start/$this->items_per_page:0;
			
			// If this isnt the first page, figure out how many pages will be displayed
			if($this->this_page > 0)
			{
				// half the total pages subtracted from your starting point
				$this->remaining_items=ceil($this->this_page-($this->pages_shown/2)); 
				// if the remainder is a positive number then just use half for the rest of the display
				if($this->remaining_items >= 0)
				{
					$this->first_page=$this->remaining_items;
					$this->remaining_items=$this->pages_shown/2;
				}
				// otherwise start from 0
				else
				{
					$this->first_page=0;
					$this->remaining_items=$this->pages_shown-(($this->pages_shown/2)+$this->remaining_items);
				}
			}
			else
			{
				// If no pages have been displayed you still have to show all the pages possible.
				$this->remaining_items=$this->pages_shown;
				$this->first_page=0;
			}
	
			// Calculate where the last page would be by default.
			$this->last_page=$this->remaining_items+$this->this_page;
			// If the remainder of pages is more than the total of pages available, just use the total pages
			if($this->last_page > $this->total_pages) { $this->last_page=$this->total_pages; }
		}
	}
	
	public function display($url, $css='pages', $style='standard')
	{
		// if we've already rendered the page, return the finished copy without rerendering it
		if(isset($this->cache[$url.$css.$style])) { return $this->cache[$url.$css.$style]; }
		
		$this->url=$url;
		
		if($this->total_pages==1) { return ''; } // yeah, just dont display 1 page things
		$page_list='';
		
		// First
		if($this->start > 0)
		{ $this->first_link = self::link($this->url, $this->first_button, 0).' '; }
		else
		{ $this->first_link = ''; }
		
		// Back
		if($this->previous_page >= 0)
		{ $this->back_link = self::link($this->url, $this->back_button, $this->previous_page).' '; }
		else
		{ $this->back_link = ''; }
		
		// Next
		if($this->next_page < $this->total_items)
		{ $this->next_link = ' '.self::link($this->url, $this->next_button, $this->next_page); }
		else
		{ $this->next_link = ''; }
		
		// Last
		if(($this->start+$this->items_per_page) < $this->total_items)
		{ $this->last_link = ' '.self::link($this->url, $this->last_button, ($this->total_pages-1)*$this->items_per_page); }
		else
		{ $this->last_link = ''; }
		
		// Individual pages
		$iterations = 0;
		for($x=$this->first_page ; $x<$this->last_page ; $x++)	// march version
		{
			// why is this here? should this be a "return" when nothings finished rendering?
			//if($iterations > 50) 		{ return $page_list; } 
			if($x==$this->this_page)	{ $page_list.='<span class="current">'.number_format($x+1).'</span> '; }
			else						{ $page_list.=self::link($this->url, number_format($x+1), ($x*$this->items_per_page),null).' '; }
			$iterations++;
		}
		
		switch($style)
		{
			case 'standard':
				// 1-20 of 1000 << First | < Back | Next > | Last >>
				if(($this->start+$this->items_per_page) > $this->total_items)	{ $page_end=$this->total_items; }
				else 															{ $page_end=$this->start+$this->items_per_page; }
				
				$this->cache[$url.$css.$style]='<div style="margin:5px 0px 5px 0px">'
					.'<div class="'.$css.'" style="width:200px; float:right; text-align:right">'.$this->first_link.' '.$this->back_link.' '.$this->next_link.' '.$this->last_link.'</div>'
					.'<div class="'.$css.'" style="width:200px; float:left; text-align:left">'.number_format(($this->start+1)).' - '.number_format(($page_end)).' of '.number_format($this->total_items).' Results. </div>'
					.'<div class="'.$css.'">'.$page_list.'</div>'
					.'</div>';
			break;
			default:
				$this->cache[$url.$css.$style]='Unsupported linking type.';
			break;
		}
		return $this->cache[$url.$css.$style];
	}
	
	/**
	 * Create a link
	 * @param string $url
	 * @param string $label
	 * @param int $pos
	 * @param string $css
	 * @return string
	 */
	private static function link($url, $label, $pos, $css=null)
	{
		if(isset($css)) { $style=' class="'.$css.'"'; }
		else { $style=''; }
		
		if(empty($pos)) { $pos = 0; }
		
		return '<a'.$style.' href="'.$url.'start='.$pos.'">'.$label.'</a>';
	}
	
	public function getData($data)
	{
		if(isset($this->$data)) { return $this->$data; }
		else { return null; }
	}
}
