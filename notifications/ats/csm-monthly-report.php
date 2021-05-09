<?php
	include_once("../../config.php");
	include_once("../../functions/reporting-service.php");
	// include_once("../../PHPMailer/PHPMailerAutoload.php");
	include_once("../../email-config.php");

	//Embed Image
	$mail->AddEmbeddedImage("../../images/company_logo.png", "companyLogo");

	// Add a sender
	$mail->setFrom('vtech.admin@vtechsolution.us','vTech Reports');

	$firstDay = date("m-d-Y",strtotime('first day of last month'));
	$lastDay = date("m-d-Y",strtotime('last day of last month'));
	$firstDayYMD = date("Y-m-d",strtotime('first day of last month'));
	$lastDayYMD = date("Y-m-d",strtotime('last day of last month'));
	$titleMonth = date("M_Y",strtotime('last day of last month'));

	$managerListQuery = mysqli_query($catsConn, "SELECT
		extra_field_options
	FROM
		extra_field_settings
	WHERE
		field_name = 'Manager - Client Service'");

	$managerListRow = mysqli_fetch_array($managerListQuery);

	$managerListNameGroup = explode(",", str_replace("+", " ", $managerListRow['extra_field_options']));
	unset($managerListNameGroup[0]);
	$managerListNameFinalGroup = "'" . implode( "','",  $managerListNameGroup) . "'";

	$managerNameQUERY = mysqli_query($catsConn, "SELECT
	    user_id AS managerId,
	    concat(first_name,' ',last_name) AS managerName,
	    email AS managerEmail
	FROM
	    user
	WHERE
	    concat(first_name,' ',last_name) IN ($managerListNameFinalGroup)
	AND
	    access_level!='0'
	GROUP BY managerName");
	
	if(mysqli_num_rows($managerNameQUERY) > 0){
		while($managerNameROW = mysqli_fetch_array($managerNameQUERY)){

			// Email subject
			$mail->Subject = 'Monthly Report_'.$managerNameROW['managerName'].'_'.$titleMonth;
			
			$mailContent = '<!DOCTYPE html>
			<html>
			<body>
				<table style="width: 100%;">
					<tr>
						<td style="text-align: center;"><img src="cid:companyLogo"></td>
					</tr>
					<tr>
						<td style="background-color: #2266AA;padding: 7px;"></td>
					</tr>
					<tr>
						<td style="background-color: #ccc;padding: 3px;"></td>
					</tr>
					<tr>
						<td style="font-size: 20px;font-weight: bold;">Monthly Report by Recruiter<span style="color: #2266AA;"> ('.$managerNameROW["managerName"].')</span><span style="font-size: 16px;color: #449D44;"> ('.$firstDay.'<span style="color:#2266AA;"> --to-- </span>'.$lastDay.')</span></td>
					</tr>
					<tr>
						<td><br>
							<table style="width: 100%;border: 1px solid #ddd;">
								<thead>
									<tr style="background-color: #ccc;color: #000;">
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;" rowspan="3">Recruiter</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;" colspan="4">Joborder</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;" rowspan="3">Submission</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;" rowspan="3">Interview</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;" rowspan="3">Interview Decline</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;" rowspan="3">Offer</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;" rowspan="3">Place</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;" rowspan="3">Delivery Failed</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;" rowspan="3">Total GP<br>(Per-Hour)</th>
									</tr>
									<tr style="background-color: #ccc;color: #000;">
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;" colspan="2">Assigned</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;" colspan="2">Unanswered</th>
									</tr>
									<tr style="background-color: #ccc;color: #000;">
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">New</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">Active</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">New</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">Active</th>
									</tr>
								</thead>
								<tbody>';

			$recruiterListQUERY = mysqli_query($catsConn, "SELECT
				user_id AS recruiterId,
				concat(first_name,' ',last_name) AS recruiterName
			FROM
				user
			WHERE
				notes = '".$managerNameROW['managerName']."'
			AND
				access_level != '0'
			GROUP BY recruiterName
			ORDER BY recruiterName ASC");
		
			$newAX = $totAX = $totAHXX = $newAHXX = $totsubXX = $totivXX = $totivdXX = $totoffXX = $totjoinXX = $totdfailXX = $gpByRecruiter = $totalGPvalueX =  array();

			while($recruiterListROW = mysqli_fetch_array($recruiterListQUERY)){
				$recruiterId = $recruiterListROW['recruiterId'];
				$recruiterName = $recruiterListROW["recruiterName"];

				$mainQUERY = mysqli_query($catsConn, "SELECT
					a.newA,
				    a.totA,
				    b.totAH,
				    b.newAH,
				    c.totsub,
				    c.totiv,
				    c.totivd,
				    c.totoff,
				    c.totjoin,
				    c.totdfail
				FROM
				(SELECT
					COUNT(CASE WHEN (date_format(date_created,'%Y-%m-%d') BETWEEN '$firstDayYMD' AND '$lastDayYMD') THEN joborder_id END) AS newA,
					COUNT(CASE WHEN status='Active' THEN joborder_id END) AS totA
				FROM
					joborder
				WHERE
					recruiter = '$recruiterId') AS a,
				(SELECT
					COUNT(DISTINCT CASE WHEN job.status = 'Active' THEN job.joborder_id END) AS totAH,
					COUNT(DISTINCT CASE WHEN (date_format(job.date_created,'%Y-%m-%d') BETWEEN '$firstDayYMD' AND '$lastDayYMD') THEN job.joborder_id END) AS newAH
				FROM
					joborder AS job
					JOIN candidate_joborder_status_history AS cjsh ON cjsh.joborder_id = job.joborder_id
				WHERE
					job.recruiter = '$recruiterId'
				AND
					cjsh.status_to = '400') AS b,
				(SELECT
					COUNT(CASE WHEN cjsh.status_to='400' THEN 1 END) AS totsub,
					COUNT(CASE WHEN cjsh.status_to='500' THEN 1 END) AS totiv,
					COUNT(CASE WHEN cjsh.status_to='560' THEN 1 END) AS totivd,
					COUNT(CASE WHEN cjsh.status_to='600' THEN 1 END) AS totoff,
					COUNT(CASE WHEN cjsh.status_to='800' THEN 1 END) AS totjoin,
					COUNT(CASE WHEN cjsh.status_to='900' THEN 1 END) AS totdfail
				FROM
					candidate_joborder_status_history AS cjsh
					JOIN candidate_joborder AS cj ON cjsh.joborder_id = cj.joborder_id AND cjsh.candidate_id = cj.candidate_id
				WHERE
					cj.added_by = '$recruiterId'
				AND
					date_format(cjsh.date,'%Y-%m-%d') BETWEEN '$firstDayYMD' AND '$lastDayYMD') AS c");
				
				while($mainROW = mysqli_fetch_array($mainQUERY)){
					$newA = $mainROW['newA'];
					$totA = $mainROW['totA'];
					$newAX[] = $mainROW['newA'];
					$totAX[] = $mainROW['totA'];

					$totAH = $mainROW['totAH'];
					$newAH = $mainROW['newAH'];

					$totAHX = $mainROW['totA'] - $mainROW['totAH'];
					$totAHXX[] = $totAHX;
					$newAHX = $mainROW['newA'] - $mainROW['newAH'];
					$newAHXX[] = $newAHX;

					$totsubX = $mainROW['totsub'];
					$totivX = $mainROW['totiv'];
					$totivdX = $mainROW['totivd'];
					$totoffX = $mainROW['totoff'];
					$totjoinX = $mainROW['totjoin'];
					$totdfailX = $mainROW['totdfail'];
					$totsubXX[] = $mainROW['totsub'];
					$totivXX[] = $mainROW['totiv'];
					$totivdXX[] = $mainROW['totivd'];
					$totoffXX[] = $mainROW['totoff'];
					$totjoinXX[] = $mainROW['totjoin'];
					$totdfailXX[] = $mainROW['totdfail'];
				}
						
				$gpQuery = "SELECT
					comp.company_id AS clientId,
					CAST(replace(e.custom3,'$','') AS DECIMAL (10,2)) AS billRate,
					CAST(replace(e.custom4,'$','') AS DECIMAL (10,2)) AS payRate,
					es.id AS employmentId,
					es.name AS employmentType,
					e.id AS employeeId,
					e.custom1 AS benefit,
					e.custom2 AS benefitlist
				FROM
					candidate_joborder AS cj
					LEFT JOIN candidate_joborder_status_history AS cjsh ON cjsh.joborder_id = cj.joborder_id AND cjsh.candidate_id = cj.candidate_id
					LEFT JOIN vtech_mappingdb.system_integration AS si ON si.c_candidate_id = cjsh.candidate_id
					LEFT JOIN vtechhrm.employees AS e ON e.id = si.h_employee_id
					LEFT JOIN vtechhrm.employmentstatus AS es ON es.id = e.employment_status
					LEFT JOIN company as comp on comp.company_id = si.c_company_id
				WHERE
					cj.added_by = '$recruiterId'
				AND
					cjsh.status_to='800'
				AND
					date_format(cjsh.date,'%Y-%m-%d') BETWEEN '$firstDayYMD' AND '$lastDayYMD'
				GROUP BY cjsh.candidate_id";

				$gpResult = mysqli_query($catsConn, $gpQuery);
				$grossMargin = "0";
				while($gpRow = mysqli_fetch_array($gpResult)) {
					$employeeId = $gpRow['employeeId'];
					$clientId = $gpRow['clientId'];
					$billRate = $gpRow['billRate'];
					$payRate = $gpRow['payRate'];
					$employmentId = $gpRow['employmentId'];
					$benefit = $gpRow['benefit'];

					$benefitDelimiter = array("","[","]",'"');
					$replacedBenefit = str_replace($benefitDelimiter, $benefitDelimiter[0], $gpRow['benefitlist']);
					$benefitlist = $replacedBenefit;

					$benefitLists = explode(",",$benefitlist);

					foreach($benefitLists AS $benefitListsValue){
						$finalTaxRate = candidateTaxRate($vtechMappingdbConn,$employmentId,$benefitListsValue,$benefit,$payRate);
					}

					if(($finalTaxRate > 0) && ($replacedBenefit != '') && ($replacedBenefit != ' ') && ($employmentId ==1 || $employmentId == 4)){
						$finalTaxRate = round(($finalTaxRate + ($payRate * 0.11)), 2);
					}
					if(($finalTaxRate > 0) && ($replacedBenefit != '') && ($replacedBenefit != ' ') && ($employmentId ==5 || $employmentId == 2)){
						$finalTaxRate = round(($finalTaxRate + ($payRate * 0.02)), 2);
					}

					$finalCombinedRate[] = mspFeesAndPrimeCharges($vtechMappingdbConn,$clientId,$employeeId,$billRate);
					
					$finalMspFees = $finalCombinedRate['mspFees'];
					
					$finalPrimeCharge = $finalCombinedRate['primeCharge'];

					$candidateRate = round(($payRate + $finalTaxRate + $finalMspFees + $finalPrimeCharge), 2);

					$grossMargin += round(($billRate - $candidateRate), 2);
				}
				$grossMarginX[] = $grossMargin;
								$mailContent.='<tr>
										<td style="text-align: left;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.ucwords($recruiterName).'</td>
										<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.$newA.'</td>
										<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.$totA.'</td>
										<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.$newAHX.'</td>
										<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.$totAHX.'</td>';
								if($totsubX=="0" || $totsubX=="1"){
									$mailContent.='<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;color:red;">'.$totsubX.'</td>';
								}else{
									$mailContent.='<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.$totsubX.'</td>';
								}
								$mailContent.='
										<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.$totivX.'</td>
										<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.$totivdX.'</td>
										<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.$totoffX.'</td>
										<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.$totjoinX.'</td>
										<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;">'.$totdfailX.'</td>
										<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;">'.$grossMargin.'</td>
									</tr>';
			}
								$mailContent.='</tbody>
								<tfoot>
									<tr style="background-color:#ccc;color:#000;">
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;">Total</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;">'.array_sum($newAX).'</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;">'.array_sum($totAX).'</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;">'.array_sum($newAHXX).'</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;">'.array_sum($totAHXX).'</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;">'.array_sum($totsubXX).'</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;">'.array_sum($totivXX).'</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;">'.array_sum($totivdXX).'</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;">'.array_sum($totoffXX).'</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;">'.array_sum($totjoinXX).'</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;">'.array_sum($totdfailXX).'</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;">'.array_sum($grossMarginX).'</th>
									</tr>
								</tfoot>
							</table><br>
						</td>
					</tr>
				</table>
			</body>
			</html>';
			
			$mailContent .= '<!DOCTYPE html>
			<html>
			<body>
				<table style="width: 100%;">
					<tr>
						<td style="text-align: center;"><br><br></td>
					</tr>
					<tr>
						<td style="background-color: #2266AA;padding: 7px;"></td>
					</tr>
					<tr>
						<td style="background-color: #ccc;padding: 3px;"></td>
					</tr>
					<tr>
						<td style="font-size: 20px;font-weight: bold;">Monthly Report by Client<span style="color: #2266AA;"> ('.$managerNameROW["managerName"].')</span><span style="font-size: 16px;color: #449D44;"> ('.$firstDay.'<span style="color:#2266AA;"> --to-- </span>'.$lastDay.')</span></td>
					</tr>
					<tr>
						<td><br>
							<table style="width: 100%;border: 1px solid #ddd;">
								<thead>
									<tr style="background-color: #ccc;color: #000;">
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;" rowspan="3">Client</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;" colspan="6">Joborder</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;" rowspan="3">Submission</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;" rowspan="3">Interview</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;" rowspan="3">Interview Decline</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;" rowspan="3">Interview Cancel</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;" rowspan="3">Offer</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;" rowspan="3">Place</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;" rowspan="3">Delivery Failed</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;" rowspan="3">Total GP<br>(Per-Hour)</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;" rowspan="3">Termination</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;" rowspan="3">Lost GP<br>(Per-Hour)</th>
									</tr>
									<tr style="background-color: #ccc;color: #000;">
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;" colspan="3">New</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;" colspan="3">Total</th>
									</tr>
									<tr style="background-color: #ccc;color: #000;">
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">All</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">OnHold</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">Canceled</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">Active</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">Unanswered</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">Openings</th>
									</tr>
								</thead>
								<tbody>';
			
			$clientListQUERY = mysqli_query($catsConn, "SELECT
				comp.company_id AS clientId,
			    comp.name AS clientName,
				COUNT(job.joborder_id) AS totajob,
				SUM(job.openings) AS jobOpening,
			    SUM(CAST(replace(job.salary,'$','') AS DECIMAL (10,2))*(job.openings)) AS openingSalary
			FROM
				company AS comp
			    JOIN joborder AS job ON job.company_id = comp.company_id
			WHERE
				comp.owner = '".$managerNameROW['managerId']."'
			AND
				job.status = 'Active'
			GROUP BY clientName");
			
			$abill = "0";
			
			$newallX = $newonholdX = $newcanceledX = $totajobXX = $totunansXX = $joxX = $totsubXX = $totivXX = $totivdXX = $totivcXX = $totoffXX = $totjoinXX = $totdfailXX = array();
			
			while($clientListROW = mysqli_fetch_array($clientListQUERY)){
				$clientId = $clientListROW['clientId'];
				$clientName = $clientListROW['clientName'];
				$totajobX = $clientListROW['totajob'];
				$totajobXX[] = $clientListROW['totajob'];
				$jobOpening = $clientListROW['jobOpening'];
				$joxX[] = $clientListROW['jobOpening'];
				$openingSalary = $clientListROW['openingSalary'];
				$abill = round($openingSalary / $jobOpening, 2);

				$mainClientQUERY = mysqli_query($catsConn, "SELECT
					a.newall,
					a.newonhold,
					a.newcanceled,
				    b.totasub,
				    c.totsub,
					c.totiv,
					c.totivd,
					c.totivc,
					c.totoff,
					c.totjoin,
					c.totdfail
				FROM
				(SELECT
					COUNT(job.joborder_id) AS newall,
					COUNT(CASE WHEN job.status='OnHold' THEN 1 END) AS newonhold,
					COUNT(CASE WHEN job.status='Canceled' THEN 1 END) AS newcanceled
				FROM
					company AS comp
				    JOIN joborder AS job ON job.company_id = comp.company_id
				WHERE
					comp.company_id = '$clientId'
				AND
					date_format(job.date_created,'%Y-%m-%d') BETWEEN '$firstDayYMD' AND '$lastDayYMD'
				GROUP BY comp.company_id) AS a,
				(SELECT
				    COUNT(DISTINCT job.joborder_id) AS totasub
				FROM
					company AS comp
				    JOIN joborder AS job ON job.company_id=comp.company_id
					JOIN candidate_joborder_status_history AS cjsh ON cjsh.joborder_id = job.joborder_id
				WHERE
				    comp.company_id = '$clientId'
				AND
					cjsh.status_to = '400'
				AND
					job.status = 'Active') AS b,
				(SELECT
				    COUNT(CASE WHEN cjsh.status_to='400' THEN 1 END) AS totsub,
				    COUNT(CASE WHEN cjsh.status_to='500' THEN 1 END) AS totiv,
				    COUNT(CASE WHEN cjsh.status_to='560' THEN 1 END) AS totivd,
				    COUNT(CASE WHEN cjsh.status_to='570' THEN 1 END) AS totivc,
				    COUNT(CASE WHEN cjsh.status_to='600' THEN 1 END) AS totoff,
				    COUNT(CASE WHEN cjsh.status_to='800' THEN 1 END) AS totjoin,
				    COUNT(CASE WHEN cjsh.status_to='900' THEN 1 END) AS totdfail
				FROM
					company AS comp
					JOIN joborder AS job ON comp.company_id =job.company_id
				    JOIN candidate_joborder_status_history AS cjsh ON job.joborder_id=cjsh.joborder_id
				WHERE
					comp.company_id = '$clientId'
				AND
					date_format(cjsh.date,'%Y-%m-%d') BETWEEN '$firstDayYMD' AND '$lastDayYMD'
				GROUP BY comp.company_id) AS c");
				
				$newall = $newonhold = $newcanceled = $totasub = $totsubX = $totivX = $totivdX = $totivcX = $totoffX = $totjoinX = $totdfailX = $totunansX = "0";
				
				while($mainClientROW = mysqli_fetch_array($mainClientQUERY)){
					$newall = $mainClientROW['newall'];
					$newonhold = $mainClientROW['newonhold'];
					$newcanceled = $mainClientROW['newcanceled'];
					$newallX[] = $mainClientROW['newall'];
					$newonholdX[] = $mainClientROW['newonhold'];
					$newcanceledX[] = $mainClientROW['newcanceled'];

					$totasub = $mainClientROW['totasub'];

					$totunansX = $totajobX - $totasub;
					$totunansXX[] = $totunansX;

					$totsubX = $mainClientROW['totsub'];
					$totivX = $mainClientROW['totiv'];
					$totivdX = $mainClientROW['totivd'];
					$totivcX = $mainClientROW['totivc'];
					$totoffX = $mainClientROW['totoff'];
					$totjoinX = $mainClientROW['totjoin'];
					$totdfailX = $mainClientROW['totdfail'];
					$totsubXX[] = $mainClientROW['totsub'];
					$totivXX[] = $mainClientROW['totiv'];
					$totivdXX[] = $mainClientROW['totivd'];
					$totivcXX[] = $mainClientROW['totivc'];
					$totoffXX[] = $mainClientROW['totoff'];
					$totjoinXX[] = $mainClientROW['totjoin'];
					$totdfailXX[] = $mainClientROW['totdfail'];
				}
								$mailContent.='<tr>
										<td style="text-align: left;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.$clientName.'</td>
										<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.$newall.'</td>
										<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.$newonhold.'</td>
										<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.$newcanceled.'</td>
										<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.$totajobX.'</td>
										<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.$totunansX.'</td>
										<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.$jobOpening.'</td>
										<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.$totsubX.'</td>
										<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.$totivX.'</td>
										<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.$totivdX.'</td>
										<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.$totivcX.'</td>
										<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.$totoffX.'</td>
										<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;border-right: 1px solid #ddd;">'.$totjoinX.'</td>
										<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;">'.$totdfailX.'</td>
										<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;"></td>
										<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;"></td>
										<td style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;"></td>
									</tr>';
			}
								$mailContent.='</tbody>
								<tfoot>
									<tr style="background-color:#ccc;color:#000;">
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;">Total</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;">'.array_sum($newallX).'</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;">'.array_sum($newonholdX).'</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;">'.array_sum($newcanceledX).'</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;">'.array_sum($totajobXX).'</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;">'.array_sum($totunansXX).'</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;">'.array_sum($joxX).'</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;">'.array_sum($totsubXX).'</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;">'.array_sum($totivXX).'</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;">'.array_sum($totivdXX).'</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;">'.array_sum($totivcXX).'</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;">'.array_sum($totoffXX).'</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;">'.array_sum($totjoinXX).'</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;">'.array_sum($totdfailXX).'</th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;"></th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;"></th>
										<th style="text-align: center;vertical-align: middle;border-top: 1px solid #ddd;"></th>
									</tr>
								</tfoot>
							</table>
						</td>
					</tr>
					<tr>
						<td style="color: #555;text-align: right;font-size: 13px;"><br>* Auto generated notification. Please DO NOT reply *<br><hr style="border: 1px dashed #ccc;"></td>
					</tr>
				</table>
			</body>
			</html>';

			// Add a recipient
			//$mail->addAddress($managerNameROW['managerEmail']);
			//$mail->addBcc('ravip@vtechsolution.us');

			echo $mailContent;
			
			//include("../../functions/email-send-config.php");
		}
	}
?>
