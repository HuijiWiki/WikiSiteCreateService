<?php

require_once('/var/www/html/SiteMaintenance/WikiSite/WikiSite.php');
require_once('/var/www/html/SiteMaintenance/WikiSite/Invitation.php');
require_once('/var/www/html/SiteMaintenance/WikiSite/ErrorMessage.php');
function receive_message($conn, $data){
	if(!$conn || !$data) return;
	$in = json_decode($data);
//	var_dump($data);
//	var_dump($in);
	if($in->action == 'create' && $in->target == 'wikisite'){
		$params = $in->params;
		if($params->domainName == null || $params->domainPrefix == null || $params->domainType == null ||
		   $params->domainDescription == null|| $params->invitationCode == null|| $params->manifestName == null ||
		   $params->userId == null || $params->userName==null){
			$conn->send(generate_send_message("fail", "params missing", "info"));	
			return;
		}else{
			if(Invitation::checkInvitation($params->invitationCode) == ErrorMessage::INV_NOT_FOUND){
				$conn->send(generate_send_message("fail", "invitation code is not valid", "info"));	
				return;
			}
			$conn->send(generate_send_message("success", "Start building new wikisite: ".$params->domainPrefix, "info"));
			$wikisite = new WikiSite($params->domainPrefix, $params->domainName, $params->domainType, $params->domainDescription, 
						 $params->manifestName, $params->userId, $params->userName);
			$ret = $wikisite->create($conn);
			if($ret == 0){
				Invitation::expireInvitation($params->invitationCode);			
				$conn->send(generate_send_message("success", "Finish building new wikisite: ".$params->domainPrefix, "info"));
			}else{
				$conn->send(generate_send_message("fail", "Fail building new wikisite: ".$params->domainPrefix, "info"));
			}
		}


	}else{
		$conn->send(WikiSite::generate_send_message("fail", "invalid way", "info"));
	}
}

function generate_send_message($status, $message, $action){
	$o = (object)[
		'status' => $status,
		'message' => $message,
		'action' => $action,
	];

	return json_encode($o);
}

?>
