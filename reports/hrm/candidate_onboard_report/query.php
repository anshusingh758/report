<?php
	include_once('../../../config.php');


	$array = array();
	$mainQuery = "SELECT DISTINCT e.id as candidateId,ocd.status as status,ocd.complianceId as co_do_id FROM vtech_tools.onbording_candidate_documents as ocd LEFT JOIN vtech_tools.compliance_mapping as cm on cm.complianceId = ocd.complianceId LEFT JOIN vtechhrm.employees as e on e.employment_status = cm.employmentType AND e.id = ocd.candidateId
	 WHERE e.status = 'OnBoarding'";

	$dailyResult = mysqli_query($vtechTools, $mainQuery);

	while($mainRow = mysqli_fetch_array($dailyResult)){
    	$array[$mainRow['candidateId']][] = $mainRow;
  	}

  	$query = "SELECT 
			  cl.id as complianceId, 
			  cl.name 
			from 
			  compliance_list as cl";


	$queryResult = mysqli_query($vtechTools, $query);
    
    while($mainRow = mysqli_fetch_array($queryResult)){
    	$complianceArray[$mainRow['complianceId']] = $mainRow;
  	}

  	$documentCandidateStatus = array();
	foreach ($array as $key => $candidateDocument) {
		foreach ($candidateDocument as $documentKey => $document) {
		if (isset($documentCandidateStatus[$document["candidateId"]])) {
			$documentCandidateStatus[$document["candidateId"]][$document["co_do_id"]] = $document["status"];	
		} else {
$documentCandidateStatus[$document["candidateId"]] = array($document["co_do_id"] => $document["status"]);
		}
		}	
		 }

		 $countCompliance = array();
	$mainQuery = "SELECT DISTINCT e.id,count(cl.name) as required, count(CASE WHEN ocd.status = 2 then 1 ELSE NULL END) AS completed FROM compliance_list as cl LEFT JOIN compliance_mapping as cm on cm.complianceId = cl.id LEFT JOIN vtechhrm.employees as e on e.employment_status = cm.employmentType LEFT JOIN onbording_candidate_documents as ocd on ocd.candidateId = e.id AND ocd.complianceId = cl.id WHERE e.status = 'OnBoarding' GROUP BY e.id";

  	$dailyRESULT = mysqli_query($vtechTools, $mainQuery);
    
    while($mainROW = mysqli_fetch_array($dailyRESULT)){
    	$countCompliance[$mainROW['id']][] = $mainROW;
  	}


?>