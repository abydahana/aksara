<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Coordinate Helper
 *
 * This helper is preparing all required data for site needs
 * such as menus and application's settings
 *
 * @package		Coordinate
 * @version		1.0
 * @author 		Aby Dahana <abydahana@gmail.com>
 * @copyright 	Copyright (c) 2016, Aby Dahana
 * @link		https://www.facebook.com/abyprogrammer
**/

/**
 * utm_to_latlng
 * Return the UTM source to latitude and longitude coordinate
 *
 * $zone		= 48 for Jakarta
 */
function utm_to_latlng($x = 0, $y = 0, $zone = 0, $aboveEquator = false)
{
	if(!is_numeric($x) || !is_numeric($y) or !is_numeric($zone))
	{
		return false;
	}
	
	$southhemi										= false;
	
	if(!$aboveEquator)
	{
		$southhemi									= true;
	}
	
	$latlon											= UTMXYToLatLon($x, $y, $zone, $southhemi);
	$lat											= radian_to_degree($latlon[0]);
	$lng											= radian_to_degree($latlon[1]);
	
	return array($lng, $lat);
}

/**
 * latlng_to_utm
 * Return the latitude and longitude coordinate to UTM source
 */
function latlng_to_utm($lat = 0, $lon = 0)
{
	if(!is_numeric($lon) || !is_numeric($lat) || ($lon <- 180.0 || $lon >= 180.0) || ($lat <- 90.0 || $lat > 90.0))
	{
		return false;
	}
	
	$zone											= floor(($lon + 180.0) / 6) + 1;
	$result											= LatLonToUTMXY(degree_to_radian($lat), degree_to_radian($lon),$zone);
	$x												= $result[0];
	$y												= $result[1];
	$aboveEquator									= false;
	
	if($lat > 0)
	{
		$aboveEquator								= true;
	}
	
	return array($x, $y, $zone, $aboveEquator);
}

/**
 * radian_to_degree
 * Return the radian to the degree
 */
function radian_to_degree($rad = 0)
{
	$pi												= 3.14159265358979;
	return ($rad / $pi * 180.0);
}

/**
 * degree_to_radian
 * Return the degree to the radian
 */
function degree_to_radian($deg = 0)
{
	$pi												= 3.14159265358979;
	return ($deg / 180.0 * $pi);
}

function UTMCentralMeridian($zone = 0)
{
	$cmeridian										= degree_to_radian(-183.0 + ($zone * 6.0));
	return $cmeridian;
}

function LatLonToUTMXY($lat = 0, $lon = 0, $zone = 0)
{
	$xy												= MapLatLonToXY ($lat, $lon, UTMCentralMeridian($zone));
	
	/* Adjust easting and northing for UTM system. */
	$UTMScaleFactor									= 0.9996;
	$xy[0]											= $xy[0] * $UTMScaleFactor + 500000.0;
	$xy[1]											= $xy[1] * $UTMScaleFactor;
	
	if($xy[1] < 0.0)
	{
		$xy[1]										= $xy[1] + 10000000.0;
	}
	
	return $xy;
}

function UTMXYToLatLon($x = 0, $y = 0, $zone = 0, $southhemi = 0)
{
	$latlon											= array();
	$UTMScaleFactor									= 0.9996;
	$x												-= 500000.0;
	$x												/= $UTMScaleFactor;
	
	/* If in southern hemisphere, adjust y accordingly. */
	if($southhemi)
	{
		$y											-= 10000000.0;
	}
	$y												/= $UTMScaleFactor;
	$cmeridian										= UTMCentralMeridian ($zone);
	$latlon											= MapXYToLatLon ($x, $y, $cmeridian);
	
	return $latlon;
}

function MapXYToLatLon($x = 0, $y = 0, $lambda0 = 0)
{
	$philambda										= array();
	$sm_b											= 6356752.314;
	$sm_a											= 6378137.0;
	$UTMScaleFactor									= 0.9996;
	$sm_EccSquared									= .00669437999013;
	$phif											= FootpointLatitude ($y);
	$ep2											= (pow($sm_a, 2.0) - pow($sm_b, 2.0)) / pow($sm_b, 2.0);
	$cf												= cos($phif);
	$nuf2											= $ep2 * pow($cf, 2.0);
	$Nf												= pow($sm_a, 2.0) / ($sm_b * sqrt (1 + $nuf2));
	$Nfpow											= $Nf;
	$tf												= tan($phif);
	$tf2											= $tf * $tf;
	$tf4											= $tf2 * $tf2;
	$x1frac											= 1.0 / ($Nfpow * $cf);
	$Nfpow											*= $Nf;   
	$x2frac											= $tf / (2.0 * $Nfpow);
	$Nfpow											*= $Nf;   
	$x3frac											= 1.0 / (6.0 * $Nfpow * $cf);
	$Nfpow											*= $Nf;   
	$x4frac											= $tf / (24.0 * $Nfpow);
	$Nfpow											*= $Nf;   
	$x5frac											= 1.0 / (120.0 * $Nfpow * $cf);
	$Nfpow											*= $Nf;   
	$x6frac											= $tf / (720.0 * $Nfpow);
	$Nfpow											*= $Nf;   
	$x7frac											= 1.0 / (5040.0 * $Nfpow * $cf);
	$Nfpow											*= $Nf;   
	$x8frac											= $tf / (40320.0 * $Nfpow);
	$x2poly											= -1.0 - $nuf2;
	$x3poly											= -1.0 - 2 * $tf2 - $nuf2;
	$x4poly											= 5.0 + 3.0 * $tf2 + 6.0 * $nuf2 - 6.0 * $tf2 * $nuf2- 3.0 * ($nuf2 *$nuf2) - 9.0 * $tf2 * ($nuf2 * $nuf2);
	$x5poly											= 5.0 + 28.0 * $tf2 + 24.0 * $tf4 + 6.0 * $nuf2 + 8.0 * $tf2 * $nuf2;
	$x6poly											= -61.0 - 90.0 * $tf2 - 45.0 * $tf4 - 107.0 * $nuf2	+ 162.0 * $tf2 * $nuf2;
	$x7poly											= -61.0 - 662.0 * $tf2 - 1320.0 * $tf4 - 720.0 * ($tf4 * $tf2);
	$x8poly											= 1385.0 + 3633.0 * $tf2 + 4095.0 * $tf4 + 1575 * ($tf4 * $tf2);
	$philambda[0]									= $phif + $x2frac * $x2poly * ($x * $x) + $x4frac * $x4poly * pow($x, 4.0) + $x6frac * $x6poly * pow($x, 6.0) + $x8frac * $x8poly * pow($x, 8.0);
	$philambda[1]									= $lambda0 + $x1frac * $x + $x3frac * $x3poly * pow($x, 3.0) + $x5frac * $x5poly * pow($x, 5.0) + $x7frac * $x7poly * pow($x, 7.0);
	
	return $philambda;
}

function FootpointLatitude ($y = 0)
{
	$sm_b											= 6356752.314;
	$sm_a											= 6378137.0;
	$UTMScaleFactor									= 0.9996;
	$sm_EccSquared									= .00669437999013;
	$n												= ($sm_a - $sm_b) / ($sm_a + $sm_b);
	$alpha_											= (($sm_a + $sm_b) / 2.0)* (1 + (pow($n, 2.0) / 4) + (pow($n, 4.0) / 64));
	$y_												= $y / $alpha_;
	$beta_											= (3.0 * $n / 2.0) + (-27.0 * pow($n, 3.0) / 32.0)+ (269.0 * pow($n, 5.0) / 512.0);
	$gamma_											= (21.0 * pow($n, 2.0) / 16.0)+ (-55.0 * pow($n, 4.0) / 32.0);
	$delta_											= (151.0 * pow($n, 3.0) / 96.0)+ (-417.0 * pow($n, 5.0) / 128.0);
	$epsilon_										= (1097.0 * pow($n, 4.0) / 512.0);
	$result											= $y_ + ($beta_ * sin(2.0 * $y_)) + ($gamma_ * sin(4.0 * $y_)) + ($delta_ * sin(6.0 * $y_)) + ($epsilon_ * sin(8.0 * $y_));
	
	return $result;
}
function MapLatLonToXY ($phi = 0, $lambda = 0, $lambda0 = 0)
{
	$xy												= array();
	$sm_b											= 6356752.314;
	$sm_a											= 6378137.0;
	$UTMScaleFactor									= 0.9996;
	$sm_EccSquared									= .00669437999013;
	$ep2											= (pow($sm_a, 2.0) - pow($sm_b, 2.0)) / pow($sm_b, 2.0);
	$nu2											= $ep2 * pow(cos($phi), 2.0);
	$N												= pow($sm_a, 2.0) / ($sm_b * sqrt(1 + $nu2));
	$t												= tan($phi);
	$t2												= $t * $t;
	$tmp											= ($t2 * $t2 * $t2) - pow($t, 6.0);
	$l												= $lambda - $lambda0;
	$l3coef											= 1.0 - $t2 + $nu2;
	$l4coef											= 5.0 - $t2 + 9 * $nu2 + 4.0 * ($nu2 * $nu2);
	$l5coef											= 5.0 - 18.0 * $t2 + ($t2 * $t2) + 14.0 * $nu2- 58.0 * $t2 * $nu2;
	$l6coef											= 61.0 - 58.0 * $t2 + ($t2 * $t2) + 270.0 * $nu2- 330.0 * $t2 * $nu2;
	$l7coef											= 61.0 - 479.0 * $t2 + 179.0 * ($t2 * $t2) - ($t2 * $t2 * $t2);
	$l8coef											= 1385.0 - 3111.0 * $t2 + 543.0 * ($t2 * $t2) - ($t2 * $t2 * $t2);
	$xy[0]											= $N * cos($phi) * $l + ($N / 6.0 * pow(cos($phi), 3.0) * $l3coef * pow($l, 3.0)) + ($N / 120.0 * pow(cos($phi), 5.0) * $l5coef * pow($l, 5.0)) + ($N / 5040.0 * pow(cos($phi), 7.0) * $l7coef * pow($l, 7.0));
	$xy[1]											= ArcLengthOfMeridian ($phi) + ($t / 2.0 * $N * pow(cos($phi), 2.0) * pow($l, 2.0)) + ($t / 24.0 * $N * pow(cos($phi), 4.0) * $l4coef * pow($l, 4.0)) + ($t / 720.0 * $N * pow(cos($phi), 6.0) * $l6coef * pow($l, 6.0)) + ($t / 40320.0 * $N * pow(cos($phi), 8.0) * $l8coef * pow($l, 8.0));
	
	return $xy;
}

function ArcLengthOfMeridian($phi = 0)
{
	$sm_b											= 6356752.314;
	$sm_a											= 6378137.0;
	$UTMScaleFactor									= 0.9996;
	$sm_EccSquared									= .00669437999013;
	$n												= ($sm_a - $sm_b) / ($sm_a + $sm_b);
	$alpha											= (($sm_a + $sm_b) / 2.0) * (1.0 + (pow($n, 2.0) / 4.0) + (pow($n, 4.0) / 64.0));
	$beta											= (-3.0 * $n / 2.0) + (9.0 * pow($n, 3.0) / 16.0) + (-3.0 * pow($n, 5.0) / 32.0);
	$gamma											= (15.0 * pow($n, 2.0) / 16.0) + (-15.0 * pow($n, 4.0) / 32.0);
	$delta											= (-35.0 * pow($n, 3.0) / 48.0) + (105.0 * pow($n, 5.0) / 256.0);
	$epsilon										= (315.0 * pow($n, 4.0) / 512.0);
	$result											= $alpha * ($phi + ($beta * sin(2.0 * $phi)) + ($gamma * sin(4.0 * $phi)) + ($delta * sin(6.0 * $phi)) + ($epsilon * sin(8.0 * $phi)));
	
	return $result;
}

function geojson2png($geojson = '[]', $stroke_color = '#ff0000', $fill_color = '#ff0000')
{
	$geojson										= json_decode($geojson);
	$paths											= '';
	$markers										= '';
	
	if(isset($geojson->features))
	{
		foreach($geojson->features as $key => $val)
		{
			$properties								= array();
			
			if('LineString' == $val->geometry->type || 'MultiLineString' == $val->geometry->type)
			{
				$paths								.= '&path=color:0x' . str_replace('#', null, $stroke_color) . '99|weight:1';

				foreach($val->geometry->coordinates as $_key => $_val)
				{
					if(is_array($_val))
					{
						foreach($_val as $__key => $__val)
						{
							$paths					.= '|' . $__val[1] . ',' . $__val[0];
						}
					}
					else
					{
						$paths						.= '|' . $_val[1] . ',' . $_val[0];
					}
				}
			}
			elseif('Polygon' == $val->geometry->type || 'MultiPolygon' == $val->geometry->type)
			{
				$paths								.= '&path=color:0x' . str_replace('#', null, $stroke_color) . '|weight:1|fillcolor:0x' . str_replace('#', null, $fill_color) . 35;
				
				foreach($val->geometry->coordinates as $_key => $_val)
				{
					foreach($_val as $__key => $__val)
					{
						if(is_array($__val))
						{
							foreach($__val as $___key => $___val)
							{
								$paths				.= '|' . $___val[1] . ',' . $___val[0];
							}
						}
						else
						{
							$paths					.= '|' . $__val[1] . ',' . $__val[0];
						}
					}
				}
			}
			elseif('Point' == $val->geometry->type)
			{
				$markers							.= '&markers=scale:1';
				$markers							.= '|' . $val->geometry->coordinates[1] . ',' . $val->geometry->coordinates[0];
			}
		}
	}
	elseif(isset($geojson->lat) && isset($geojson->lng))
	{
		$markers									.= '&markers=scale:1';
		$markers									.= '|' . $geojson->lat . ',' . $geojson->lng;
	}
	
	$params											= array
	(
		'key'										=> get_setting('google_maps_api_key'),
		'zoom'										=> '',
		'size'										=> '600x600',
		'format'									=> 'png32',
		'scale'										=> 2,
		'sensor'									=> 'false',
	);
	
	return 'http://maps.googleapis.com/maps/api/staticmap?' . http_build_query($params) . $markers . $paths;
}
