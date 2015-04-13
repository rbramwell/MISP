<?php
class JSONConverterTool {
	public function event2JSON($event, $isSiteAdmin=false) {
		$event['Event']['Org'][0] = $event['Org'];
		$event['Event']['Orgc'][0] = $event['Orgc'];
		if (isset($event['SharingGroup']['SharingGroupOrg'])) {
			foreach ($event['SharingGroup']['SharingGroupOrg'] as $key => $sgo) {
				$event['SharingGroup']['SharingGroupOrg'][$key]['Organisation'] = array(0 => $event['SharingGroup']['SharingGroupOrg'][$key]['Organisation']);
			}
		}
		if (isset($event['SharingGroup']['SharingGroupServer'])) {
			foreach ($event['SharingGroup']['SharingGroupServer'] as $key => $sgs) {
				$event['SharingGroup']['SharingGroupServer'][$key]['Server'] = array(0 => $event['SharingGroup']['SharingGroupServer'][$key]['Server']);
			}
		}
		if (isset($event['SharingGroup'])) $event['Event']['SharingGroup'][0] = $event['SharingGroup'];
		$event['Event']['Attribute'] = $event['Attribute'];
		$event['Event']['ShadowAttribute'] = $event['ShadowAttribute'];
		$event['Event']['RelatedEvent'] = $event['RelatedEvent'];
		
		if (isset($event['EventTag'])) {
			foreach ($event['EventTag'] as $k => $tag) {
				$event['Event']['Tag'][$k] = $tag['Tag'];
			}
		}
			
		//
		// cleanup the array from things we do not want to expose
		//
		unset($event['Event']['user_id']);
		// hide the org field is we are not in showorg mode
		if (!Configure::read('MISP.showorg') && !$isSiteAdmin) {
			unset($event['Event']['org']);
			unset($event['Event']['orgc']);
			unset($event['Event']['from']);
		}
		
		if (isset($event['Event']['Attribute'])) {
			// remove value1 and value2 from the output and remove invalid utf8 characters for the xml parser
			foreach ($event['Event']['Attribute'] as $key => $value) {
				unset($event['Event']['Attribute'][$key]['value1']);
				unset($event['Event']['Attribute'][$key]['value2']);
			}
		}

		if (isset($event['Event']['RelatedEvent'])) {
			foreach ($event['Event']['RelatedEvent'] as $key => $value) {
				$temp = $value['Event'];
				unset($event['Event']['RelatedEvent'][$key]['Event']);
				$event['Event']['RelatedEvent'][$key]['Event'][0] = $temp;
				unset($event['Event']['RelatedEvent'][$key]['Event'][0]['user_id']);
				if (!Configure::read('MISP.showorg') && !$isSiteAdmin) {
					unset($event['Event']['RelatedEvent'][$key]['Event'][0]['org']);
					unset($event['Event']['RelatedEvent'][$key]['Event'][0]['orgc']);
				}
				unset($temp);
			}
		}
		$result = array('Event' => $event['Event']);
		return json_encode($result);
	}
}