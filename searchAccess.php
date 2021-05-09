<?php
	include('config.php');

	if($_POST){
		$uiddx2=$_POST['uiddx'];
		$qry3="SELECT
				m.uid,
				r.rid,
			    r.rname,
			    cat.catname
			FROM
				mapping AS m
			    JOIN reports AS r ON r.rid=m.rid
			    JOIN category AS cat ON r.catid=cat.catid
			WHERE
				m.uid='$uiddx2'
			ORDER BY r.rid ASC";
		$res3=mysqli_query($misReportsConn,$qry3);

		$del1=0;

		if(mysqli_num_rows($res3)>0){
			$output='<form id="frm-example" onsubmit="parent.scrollTo(0, 0); return true">
        	<input type="hidden" name="uidXX2" value="'.$uiddx2.'" readonly>
			<div class="row" style="margin-top: 25px;margin-bottom: 25px;">
			<div class="col-md-6">
			<label>View Reports :</label>
			<table id="viewAccessTable" class="table table-bordered table-striped">
			<thead>
			<tr style="background-color: #337AB7;color: #fff;font-size: 13px;">
			<th style="text-align: center;vertical-align: middle;"><input style="height:15px;width:15px;cursor: pointer;outline: none;" type="checkbox" name="select_all" id="select_all" data-toggle="tooltip" data-placement="top" title="Select All Reports!"></th>
			<th style="text-align: center;vertical-align: middle;">ID</th>
			<th style="text-align: center;vertical-align: middle;">Reports</th>
			<th style="text-align: center;vertical-align: middle;">Category</th>
			</tr>
			</thead>
			<tbody>';

			while($row3=mysqli_fetch_array($res3)){
	            $output .='
	            <tr style="font-size:13px;">
	            	<td style="text-align: center;vertical-align: middle;"><input style="height:14px;width:14px;cursor:pointer;outline: none;" type="checkbox" class="checkboxes" name="checked_id[]" id="checked_id" value="'.$row3['rid'].'" data-toggle="tooltip" data-placement="top" title="Select!"></td>
	                <td style="text-align: center;vertical-align: middle;">'.$row3['rid'].'</td>
	                <td style="vertical-align: middle;">'.$row3['rname'].'</td>
	                <td style="text-align: center;vertical-align: middle;">'.$row3['catname'].'</td>
	            </tr>';
			}

			$output.='<tr>
			<td colspan="4"><button type="submit" class="btn btn-danger form-control" style="border-radius: 0px;outline: none;"><i style="font-size: 18px;" class="fa fa-trash-o"></i> Remove Access!</button></td>
			</tr>
			</tbody>
			</table>
			</div>
			</div>
			</form>';
			echo $output;
		}
	}
?>

<script>
	$(document).ready(function(){
	    $('#select_all').on('click',function(){
	        if(this.checked){
	            $('.checkboxes').each(function(){
	                this.checked = true;
	            });
	        }else{
	             $('.checkboxes').each(function(){
	                this.checked = false;
	            });
	        }
	    });

		$('.checkboxes').on('click',function(){
			if($('.checkboxes:checked').length == $('.checkboxes').length){
				$('#select_all').prop('checked',true);
			}else{
				$('#select_all').prop('checked',false);
			}
		});
	});
</script>

<script>
	$(document).ready(function(){

		$('#frm-example').submit(function(e){
			if($('.checkboxes:checked').length == 0){
				swal({
                    title: 'Please select atleast one checkbox!',
                    type: 'error',
                    button: 'OK!'
                });
				return false;
			}
			if($('.checkboxes:checked').length > 0){
				e.preventDefault();
				$.ajax({
					url: 'removeAccess.php',
					type: 'POST',
					data: $('#frm-example').serialize(),
					success: function(opt){
	            		swal({
		                    title: 'Access successfully removed!',
		                    type: 'success',
		                    button: 'OK!'
		                },function(isConfirm){
		                    alert('ok');
		                });
		                $('.swal2-confirm').click(function(){
							location.reload();
		                });
		            }
				});
				return true;
			}
		});

	});
</script>
