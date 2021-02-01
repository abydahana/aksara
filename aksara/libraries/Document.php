<?php namespace Aksara\Libraries;
/**
 * The PDF Library
 * This class is used to generate PDF's file
 *
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

ini_set('pcre.backtrack_limit', 99999999);

class Document
{
	/**
	 * parameter
	 *
	 * @object
	 */
	private $_params								= array();
	
	/**
	 * default page width
	 *
	 * @string
	 */
	private $_pageWidth								= '8.5in';
	
	/**
	 * default page height
	 *
	 * @string
	 */
	private $_pageHeight							= '13in';
	
	/**
	 * page orientation
	 *
	 * @string
	 */
	private $_pageOrientation						= 'portrait';
	
	/**
	 * margin top
	 *
	 * @int
	 */
	private $_pageMarginTop							= 0;
	
	/**
	 * margin right
	 *
	 * @int
	 */
	private $_pageMarginRight						= 0;
	
	/**
	 * margin bottom
	 *
	 * @int
	 */
	private $_pageMarginBottom						= 0;
	
	/**
	 * margin left
	 * @int
	 */
	private $_pageMarginLeft						= 0;
	
	public function __construct()
	{
	}
	
	public function generate($html = null, $filename = null, $method = 'embed', $params = array())
	{
		// push parameter
		$this->_params								= array_merge($this->_params, $params);
		
		// default page width (better use "in" a.k.a inches)
		if(!isset($this->_params['page-width']))
		{
			$this->_params['page-width']			= '8.5in';
		}
		// default page height (better use "in" a.k.a inches)
		if(!isset($this->_params['page-height']))
		{
			$this->_params['page-height']			= '13in';
		}
		// default top margin of page
		if(!isset($this->_params['margin-top']))
		{
			$this->_params['margin-top']			= 10;
		}
		// default right margin of page
		if(!isset($this->_params['margin-right']))
		{
			$this->_params['margin-right']			= 10;
		}
		// default bottom margin of page
		if(!isset($this->_params['margin-bottom']))
		{
			$this->_params['margin-bottom']			= 10;
		}
		// default left margin of page
		if(!isset($this->_params['margin-left']))
		{
			$this->_params['margin-left']			= 10;
		}
		if('export' == strtolower($method))
		{
			// use excel generator
			// online doc can be found in https://xxx.xx/
			return $this->_excel($html, $filename, $method, $this->_params);
		}
		else
		{
			// use mPDF instead
			// online doc can be found in https://mpdf.github.io/
			$output									= $this->_mpdf($html, $filename, 'attach', $this->_params);
			
			if($method == 'embed')
			{
				service('response')->setContentType('application/pdf');
				
				return service('response')->setBody($output)->send();
			}
			else
			{
				return $output;
			}
		}
	}
	
	public function pageSize($width = '8.5in', $height = '13in')
	{
		// explode to get initial setup
		$widthHeight								= explode(' ', preg_replace('!\s+!', ' ', $width));
		
		if(2 == sizeof($widthHeight))
		{
			// the page size and orientation is sets with units
			$this->_params['page-width']			= $widthHeight[0];
			$this->_params['page-height']			= $widthHeight[1];
		}
		elseif('landscape' == strtolower($height))
		{
			// the page size and orientation is sets with initial, ex: A4, landscape
			$this->_params['page-size']				= $width;
			$this->_params['orientation']			= $height;
		}
		else
		{
			// the page size and orientation is sets with initial, ex: A4, landscape
			$this->_params['page-width']			= $width;
			$this->_params['page-height']			= $height;
		}
		
		return $this;
	}
	
	public function pageMargin($top = 0, $right = 0, $bottom = 0, $left = 0)
	{
		// hack the retard setup
		if($top && !$right && !$bottom && !$left)
		{
			// margin of the edge is equal
			$this->_params['margin-top']			= $top;
			$this->_params['margin-right']			= $top;
			$this->_params['margin-bottom']			= $top;
			$this->_params['margin-left']			= $top;
		}
		elseif($top && $right && !$bottom && !$left)
		{
			// margin-top and bottom is equal, also margin-right and left
			$this->_params['margin-top']			= $top;
			$this->_params['margin-right']			= $right;
			$this->_params['margin-bottom']			= $top;
			$this->_params['margin-left']			= $right;
		}
		elseif($top && $right && $bottom && !$left)
		{
			// only left margin is equal to the right margin
			$this->_params['margin-top']			= $top;
			$this->_params['margin-right']			= $right;
			$this->_params['margin-bottom']			= $bottom;
			$this->_params['margin-left']			= $right;
		}
		else
		{
			// all edge is used custom margin
			$this->_params['margin-top']			= $top;
			$this->_params['margin-right']			= $right;
			$this->_params['margin-bottom']			= $bottom;
			$this->_params['margin-left']			= $left;
		}
		
		return $this;
	}
	
	private function _mpdf($html = null, $filename = null, $method = 'embed', $params = array())
	{
		// rendering mode
		$params['mode']								= 'utf-8';
		
		// auto top margin
		$params['setAutoTopMargin']					= 'stretch';
		
		// auto bottom margin
		$params['setAutoBottomMargin']				= 'stretch';
		
		// use subtitutions
		$params['showImageErrors']					= true;
		
		// use subtitutions
		$params['useSubstitutions']					= false;
		
		// table proportions
		$params['keep_table_proportions']			= true;
		
		// auto page break enabled
		$params['autoPageBreak']					= true;
		
		// temporary folder
		$params['tempDir']							= sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'mpdf';
		
		// used font
		$params['default_font']						= (isset($params['default_font']) ? $params['default_font'] : 'tahoma');
		
		// DPI
		$params['dpi']								= 80;
		
		/* check if page size is defined */
		if(isset($params['page-width']) && isset($params['page-height']))
		{
			// set the page size
			$params['format']						= array(preg_replace('/[^0-9.]/', '', $params['page-width']) * 25.4, preg_replace('/[^0-9.]/', '', $params['page-height']) * 25.4);
		}
		
		// load generator
		$pdf										= new \Mpdf\Mpdf($params);
		
		// render output
		$pdf->SetCreator('Aby Dahana (abydahana.github.io)');
		$pdf->SetAuthor('Aby Dahana (abydahana.github.io)');
		
		// add watermark
		if(isset($params['setWatermarkText']))
		{
			$pdf->SetWatermarkText($params['setWatermarkText']);
			$pdf->showWatermarkText					= true;
		}
		if(isset($params['setWatermarkImage']))
		{
			$pdf->SetWatermarkImage($params['setWatermarkImage']);
			$pdf->showWatermarkImage				= true;
		}
		
		$pdf->WriteHTML($html);
		
		if($method == 'attach')
		{
			// attach to email
			return $pdf->Output($filename . '.pdf', 'S');
		}
		elseif($method == 'download')
		{
			// download results
			return $pdf->Output($filename . '.pdf', 'D');
		}
		else
		{
			// display to browser
			return $pdf->Output($filename . '.pdf', 'I');
		}
	}
	
	private function _excel($html = null, $filename = null, $method = 'embed', $params = array())
	{
		libxml_use_internal_errors(true);
		
		// remove special tags
		$html										= preg_replace('/<htmlpagefooter(.*)<\/htmlpagefooter>/iUs', '', preg_replace('/<htmlpageheader(.*)<\/htmlpageheader>/iUs', '', $html));
		
		// load dom
		$dom										= new \DOMDocument();
		$dom->loadHTML($html);
		
		// get only style element
		$styles										= $dom->getElementsByTagName('style');
		$css										= null;
		foreach($styles as $style)
		{
			$css									= $dom->saveHTML($style);
		}
		
		// get only table element
		$tables										= $dom->getElementsByTagName('table');
		$output										= null;
		foreach($tables as $table)
		{
			if($table->getAttribute('class') !== 'table') continue;
			
			$output									.= $dom->saveHTML($table); 
		}
		
		$output										= '<!DOCTYPE html><head><title>' . $filename . '</title>' . $css . '</head><body>' . $output . '</body></html>';
		
		header('Content-type: application/vnd.ms-excel');
		header('Content-Transfer-Encoding: binary');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Disposition: attachment; filename=' . $filename . '.xls');
		
		echo $output;
	}
}