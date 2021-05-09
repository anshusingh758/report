<?php

	if ($_POST) {
		
		$output = $dataType = $dataId = $empType = $benefit = $chargePct = $candidateId = $candidateName = $primeChargePct = $primeChargeDlr = $anyChargeDlr = $clientId = $clientName = $mspChrgPct = $mspChrgDlr = $primeChrgPct = $primeChrgDlr = $markupPct = $marginPct = "";
		
		$dataType = $_POST['dataType'];
		$dataId = $_POST['dataId'];
		
		if ($dataType == 'tax') {
			$empType = $_POST['empType'];
			$benefit = $_POST['benefit'];
			$chargePct = $_POST['chargePct'];
			$oldData = "id:".$dataId.",type:".$dataType.",charge_pct:".$chargePct;
			$output .= '<form id="editModalForm">
				<div class="modal-header" style="background-color: #2266AA;color: #fff;">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title text-center">Edit Tax Settings</h4>
				</div>
				<div class="modal-body">
					<input type="hidden" name="dataType" value="'.$dataType.'">
					<input type="hidden" name="dataId" value="'.$dataId.'">
					<input type="hidden" name="oldData" value="'.$oldData.'">
					<div class="row">
						<div class="col-sm-12">
							<label>Employment Type :</label>
							<input type="text" name="empType" class="form-control" value="'.$empType.'" readonly>
						</div>
					</div>
					<div class="row" style="margin-top: 10px;">
						<div class="col-sm-12">
							<label>Benefit :</label>
							<input type="text" name="benefit" class="form-control" value="'.$benefit.'" readonly>
						</div>
					</div>
					<div class="row" style="margin-top: 10px;">
						<div class="col-sm-12">
							<label>Tax % :</label>
							<input type="text" name="taxPerc" class="form-control checkNumber" value="'.$chargePct.'" required>
						</div>
					</div>
				</div>
				<div class="modal-footer" style="margin-top: 0px;padding: 10px 5px;">
					<button type="submit" class="btn" style="background-color: #2266AA;color: #fff;border: 1px solid #2266AA;"><i class="fa fa-floppy-o"></i> Save</button>
				</div>
			</form>';
		} elseif ($dataType == 'candidate') {
			$candidateId = $_POST['candidateId'];
			$candidateName = $_POST['candidateName'];
			$primeChargePct = $_POST['primeChargePct'];
			$primeChargeDlr = $_POST['primeChargeDlr'];
			$anyChargeDlr = $_POST['anyChargeDlr'];
			$oldData = "id:".$dataId.",type:".$dataType.",c_primeCharge_pct:".$primeChargePct.",c_primeCharge_dlr:".$primeChargeDlr.",c_anyCharge_dlr:".$anyChargeDlr;
			$output .= '<form id="editModalForm">
				<div class="modal-header" style="background-color: #2266AA;color: #fff;">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title text-center">Edit Candidate Fees</h4>
				</div>
				<div class="modal-body">
					<input type="hidden" name="dataType" value="'.$dataType.'">
					<input type="hidden" name="dataId" value="'.$dataId.'">
					<input type="hidden" name="oldData" value="'.$oldData.'">
					<input type="hidden" name="candidateId" value="'.$candidateId.'">
					<div class="row">
						<div class="col-sm-12">
							<label>Candidate :</label>
							<input type="text" name="candidateName" class="form-control" value="'.$candidateName.'" readonly>
						</div>
					</div>
					<div class="row" style="margin-top: 10px;">
						<div class="col-sm-12">
							<label>Prime Fees % :</label>
							<input type="text" name="primeChargePct" class="form-control checkNumber" value="'.$primeChargePct.'" required>
						</div>
					</div>
					<div class="row" style="margin-top: 10px;">
						<div class="col-sm-12">
							<label>Prime Fees $ :</label>
							<input type="text" name="primeChargeDlr" class="form-control checkNumber" value="'.$primeChargeDlr.'" required>
						</div>
					</div>
					<div class="row" style="margin-top: 10px;">
						<div class="col-sm-12">
							<label>Other Fees $ :</label>
							<input type="text" name="anyChargeDlr" class="form-control checkNumber" value="'.$anyChargeDlr.'" required>
						</div>
					</div>
				</div>
				<div class="modal-footer" style="margin-top: 0px;padding: 10px 5px;">
					<button type="submit" class="btn" style="background-color: #2266AA;color: #fff;border: 1px solid #2266AA;"><i class="fa fa-floppy-o"></i> Save</button>
				</div>
			</form>';
		} elseif ($dataType == 'client') {
			$clientId = $_POST['clientId'];
			$clientName = $_POST['clientName'];
			$mspChrgPct = $_POST['mspChrgPct'];
			$mspChrgDlr = $_POST['mspChrgDlr'];
			$primeChrgPct = $_POST['primeChrgPct'];
			$primeChrgDlr = $_POST['primeChrgDlr'];
			$oldData = "id:".$dataId.",type:".$dataType.",mspChrgPct:".$mspChrgPct.",mspChrgDlr:".$mspChrgDlr.",primeChrgPct:".$primeChrgPct.",primeChrgDlr:".$primeChrgDlr;
			$output .= '<form id="editModalForm">
				<div class="modal-header" style="background-color: #2266AA;color: #fff;">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title text-center">Edit Client Fees</h4>
				</div>
				<div class="modal-body">
					<input type="hidden" name="dataType" value="'.$dataType.'">
					<input type="hidden" name="dataId" value="'.$dataId.'">
					<input type="hidden" name="oldData" value="'.$oldData.'">
					<input type="hidden" name="clientId" value="'.$clientId.'">
					<div class="row">
						<div class="col-sm-12">
							<label>Client :</label>
							<input type="text" name="clientName" class="form-control" value="'.$clientName.'" readonly>
						</div>
					</div>
					<div class="row" style="margin-top: 10px;">
						<div class="col-sm-12">
							<label>MSP Fees % :</label>
							<input type="text" name="mspChrgPct" class="form-control checkNumber" value="'.$mspChrgPct.'" required>
						</div>
					</div>
					<div class="row" style="margin-top: 10px;">
						<div class="col-sm-12">
							<label>MSP Fees $ :</label>
							<input type="text" name="mspChrgDlr" class="form-control checkNumber" value="'.$mspChrgDlr.'" required>
						</div>
					</div>
					<div class="row" style="margin-top: 10px;">
						<div class="col-sm-12">
							<label>Prime Fees % :</label>
							<input type="text" name="primeChrgPct" class="form-control checkNumber" value="'.$primeChrgPct.'" required>
						</div>
					</div>
					<div class="row" style="margin-top: 10px;">
						<div class="col-sm-12">
							<label>Prime Fees $ :</label>
							<input type="text" name="primeChrgDlr" class="form-control checkNumber" value="'.$primeChrgDlr.'" required>
						</div>
					</div>
				</div>
				<div class="modal-footer" style="margin-top: 0px;padding: 10px 5px;">
					<button type="submit" class="btn" style="background-color: #2266AA;color: #fff;border: 1px solid #2266AA;"><i class="fa fa-floppy-o"></i> Save</button>
				</div>
			</form>';
		} elseif ($dataType == 'clientMarkupMargin') {
			$clientId = $_POST['clientId'];
			$clientName = $_POST['clientName'];
			$clientType = $_POST['clientType'];
			$percentageValue = $_POST['percentageValue'];
			$oldData = "id:".$dataId.",type:".$dataType.",clientType:".$clientType.",percentageValue:".$percentageValue;

			$markupOptionData = "";
			$marginOptionData = "";

			if ($clientType == "Markup") {
				$markupOptionData = " selected";
			} elseif ($clientType == "Margin") {
				$marginOptionData = " selected";
			}
			
			$output .= '<form id="editModalForm">
				<div class="modal-header" style="background-color: #2266AA;color: #fff;">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title text-center">Edit Client Markup / Margin %</h4>
				</div>
				<div class="modal-body">
					<input type="hidden" name="dataType" value="'.$dataType.'">
					<input type="hidden" name="dataId" value="'.$dataId.'">
					<input type="hidden" name="oldData" value="'.$oldData.'">
					<input type="hidden" name="clientId" value="'.$clientId.'">
					<div class="row">
						<div class="col-sm-12">
							<label>Client :</label>
							<input type="text" name="clientName" class="form-control" value="'.$clientName.'" readonly>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<label>Type :</label>
							<select name="clientType" class="form-control" required>
								<option value="">---Select---</option>
								<option value="Markup"'.$markupOptionData.'>Markup</option>
								<option value="Margin"'.$marginOptionData.'>Margin</option>
							</select>
						</div>
					</div>
					<div class="row" style="margin-top: 10px;">
						<div class="col-sm-12">
							<label>Value % :</label>
							<input type="text" name="percentageValue" class="form-control checkNumber" value="'.$percentageValue.'" required>
						</div>
					</div>
				</div>
				<div class="modal-footer" style="margin-top: 0px;padding: 10px 5px;">
					<button type="submit" class="btn" style="background-color: #2266AA;color: #fff;border: 1px solid #2266AA;"><i class="fa fa-floppy-o"></i> Save</button>
				</div>
			</form>';
		}
		
		echo $output;
	}

?>