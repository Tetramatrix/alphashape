<?php
/***************************************************************
* Copyright notice
*
* (c) 2010-2013 Chi Hoang (info@chihoang.de)
*  All rights reserved
*
***************************************************************/
require_once("concave-hull.php");

// Turn off all error reporting
error_reporting(0);

// example 1
$file = fopen("star.dat", "r");
$set=array();
while (!feof($file))
{
    list($x,$y)=explode(" ",rtrim(fgets($file)));
    $set[] = array(round($x),round($y));  
}
fclose($file);
$hull=new hull();
$hull->main($set,500,400);
$pic=new visualize("/tmp/",$hull);
$pic->genimage();

//example 2
$set=array();
$tree=array(162, 332,182, 299,141, 292,158, 264,141, 408,160, 400,177, 430,151, 442,155, 425,134, 430,126, 447,139, 466,160, 471,167, 447,182, 466,192, 442,187, 413,173,
            403,168, 425,153, 413,179, 275,163, 292,134, 270,143, 315,177, 320,163, 311,162, 281,182, 255,141, 226,156, 235,173, 207,187, 230,204, 194,165, 189,145, 201,158,
            167,190, 165,206, 145,179, 153,204, 114,221, 138,243, 112,248, 139,177, 122,179, 99,196, 82,219,90,240, 75,218, 61,228, 53,211, 34,197, 51,179, 65,155, 70,165,
            85,134, 80,124, 58,153, 44,173, 34,192, 27,156, 19,119, 32,128, 17,138, 36,100, 58,112, 73,100, 92,78, 100,83, 78,61, 63,80, 44,100, 26,60, 39,43, 71,34, 54,32,
            90,53, 104,60, 82,66, 99,247, 94,187, 180,221, 168);
for ($i=0,$end=count($tree);$i<$end;$i+=2)
{
    $set[]=array($tree[$i],$tree[$i+1]);    
}
$hull=new hull();
$hull->main($set,600,600);
$pic=new visualize("/tmp/",$hull);
$pic->genimage();

//example 3
$mapPadding  = 100;
$mapWidth    = 500;
$mapHeight   = 500;
$mapLonLeft  =1000;
$mapLatBottom=1000;
$mapLonRight =   0;
$mapLatTop   =   0;
$set=array();
$geocoord = array ("8.6544487,50.1005233",
                   "8.7839489,50.0907496",
                   "8.1004734,50.2002273",
                   "8.4117234,50.0951493",
                   "8.3508367,49.4765982",
                   "9.1828630,48.7827027",
                   "9.1686483,48.7686426",
                   "9.2118466,48.7829101",
                   "8.9670738,48.9456327");

foreach ($geocoord as $key => $arr)
{
    list($lon,$lat) = explode(",",$arr);
    $mapLonLeft = min($mapLonLeft,$lon);
    $mapLonRight = max($mapLonRight,$lon);
    $mapLatBottom = min($mapLatBottom,$lat);
    $mapLatTop = max($mapLatTop,$lat);
    $set[]=array($lon,$lat);
}

$mapLonDelta = $mapLonRight-$mapLonLeft;
$mapLatDelta = $mapLatTop-$mapLatBottom;

$mapLatTopY=$mapLatTop*(M_PI/180);
$worldMapWidth=(($mapWidth/$mapLonDelta)*360)/(2*M_PI);
$LatBottomSin=min(max(sin($mapLatBottom*(M_PI/180)),-0.9999),0.9999);
$mapOffsetY=$worldMapWidth/2 * log((1+$LatBottomSin)/(1-$LatBottomSin));
$LatTopSin=min(max(sin($mapLatTop*(M_PI/180)),-0.9999),0.9999);
$mapOffsetTopY=$worldMapWidth/2 * log((1+$LatTopSin)/(1-$LatTopSin));
$mapHeightD=$mapOffsetTopY-$mapOffsetY;
$mapRatioH=$mapHeight/$mapHeightD;
$newWidth=$mapWidth*($mapHeightD/$mapHeight);
$mapRatioW=$mapWidth/$newWidth;

foreach ($set as $key => $arr)
{
    list($lon,$lat) = $arr;
    $tx = ($lon - $mapLonLeft) * ($newWidth/$mapLonDelta)*$mapRatioW;
    $f = sin($lat*M_PI/180);
    $ty = ($mapHeightD-(($worldMapWidth/2 * log((1+$f)/(1-$f)))-$mapOffsetY));
}


$hull=new hull();
$hull->main($set,$mapWidth,$mapHeightD);
 
$pic=new visualize("/tmp/",$hull);
$pic->genimage();

?>