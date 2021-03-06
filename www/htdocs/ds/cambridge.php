<?
class CambridgeDataSource extends DataSource
{
	static $datafile = 'resources/camlib.xml';

	static function getAll()
	{
		//id, lat, long, label
		$libs = simplexml_load_file(self::$datafile);
		foreach($libs->library as $lib)
		{
			if($lib->lat == null || $lib->lng == null)
				continue;
			$point['id'] = 'http://www.lib.cam.ac.uk/#'.(string)$lib->code;
			$point['lat'] = (float)$lib->lat;
			$point['long'] = (float)$lib->lng;
			$point['label'] = (string)$lib->name;
			$point['icon'] = self::$iconpath.'Education/library.png';
			$points[] = $point;
		}
		return $points;
	}

	// Process library data
	static function getEntries($q, $cats)
	{	
		$pos = array();
		$label = array();
		$type = array();
		$url = array();
		$icon = array();
		$data = self::getLibraries($q);
		foreach($data as $point) {
			$point['icon'] = self::$iconpath.'Education/library.png';
			if(!self::visibleCategory($point['icon'], $cats))
				continue;
			$pos[$point['pos']] ++;
			if(preg_match('/'.$q.'/i', $point['label']))
			{
				$label[$point['label']] ++;
				$type[$point['label']] = "offering";
			}
			if(preg_match('/'.$q.'/i', $point['poslabel']))
			{
				$label[$point['poslabel']] += 10;
				$type[$point['poslabel']] = "workstation";
				$url[$point['poslabel']] = $point['pos'];
				$icon[$point['poslabel']] = $point['icon'];
			}
		}
		return array($pos, $label, $type, $url, $icon);
	}

	static function getLibraries($q)
	{
		//poslabel, label, pos, icon
		$libs = simplexml_load_file(self::$datafile);
		foreach($libs->library as $lib)
		{
			if($lib->lat == null || $lib->lng == null)
				continue;
			$point['poslabel'] = (string)$lib->name;
			$point['pos'] = 'http://www.lib.cam.ac.uk/#'.(string)$lib->code;
			$point['label'] = 'library';//(string)$lib->name;
			$point['icon'] = self::$iconpath.'Education/library.png';
			$points[] = $point;
		}
		return $points;
	}

	static function visibleCategory($icon, $cats)
	{
		global $iconcats;
		if($iconcats == null) include_once "inc/categories.php";
		return in_cat($iconcats, $icon, $cats);
	}
}
?>
