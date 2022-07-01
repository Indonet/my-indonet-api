<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>Informasi Tagihan</title> 
		<style>
			.invoice-box {
				/* max-width: 800px; */
				margin: auto;
				/* padding: 30px; */
				/* border: 1px solid #eee; */
				/* box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); */
				font-size: 14px;
				line-height: 20px;
				font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
				color: #555;
			}

			.invoice-box table {
				width: 100%;
				line-height: inherit;
				text-align: left;
			}

			.invoice-box table td {
				padding: 5px;
				vertical-align: middle;
			}

			.invoice-box table tr td:nth-child(2) {
				text-align: right;
			}

			.invoice-box table tr.top table td {
				padding-bottom: 20px;
			}

			.invoice-box table tr.top table td.title {
				font-size: 45px;
				line-height: 12px;
				color: #333;
			}

			.invoice-box table tr.information table td {
				padding-bottom: 40px;
			}

			.invoice-box table tr.heading td {
				background: #eee;
				border-bottom: 1px solid #ddd;
				font-weight: bold;
			}

			.invoice-box table tr.details td {
				padding-bottom: 20px;
			}

			.invoice-box table tr.item td {
				border-bottom: 1px solid #eee;
			}

			.invoice-box table tr.item.last td {
				border-bottom: none;
			}

			.invoice-box table tr.total td:nth-child(2) {
				border-top: 2px solid #eee;
				font-weight: bold;
			}

			@media only screen and (max-width: 600px) {
				.invoice-box table tr.top table td {
					width: 100%;
					display: block;
					text-align: center;
				}

				.invoice-box table tr.information table td {
					width: 100%;
					display: block;
					text-align: center;
				}
			}
			.total_info { 
				width: 80% !important;
				float: right;
			}
			.total_info tr.header td {
				background: #afb3b2; 
				font-weight: bold;
				text-align: center !important;
				font-size: 14px !important;
				padding-bottom: 0px !important; 
			}
			.total_info tr.details td {
				background: #dae0df;
				border-bottom: 1px solid #ddd;
				font-weight: bold;
				text-align: center !important;
				font-size: 14px !important;
				padding-bottom: 1px !important; 
			}
			
			/** RTL **/
			.invoice-box.rtl {
				direction: rtl;
				font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
			}

			.invoice-box.rtl table {
				text-align: right;
			}

			.invoice-box.rtl table tr td:nth-child(2) {
				text-align: left;
			}
            .indonet-pt{
				font-weight: bold;
                font-size: 17px;
				margin-bottom: 20px;
            }
            .indonet-info{ 
                font-size: 14px;
				line-height: 20px;
            }
			.logo-pt{
				margin-bottom: 30px;
				width: 100%; 
				max-width: 150px
			}
			.info-cust td{    
				font-size: 14px !important; 
				padding-bottom: 1px !important; 
			}
			.info-end{
				border: 1px solid black;
				padding: 20px;
				margin-top: 20px;
			}
			.footer_div {
				position: fixed;
				left: 0;
				bottom: 0;
				width: 100%; 
				color: black;
				text-align: right;
			}
			.arrow-down {
				width: 0; 
				height: 0; 
				border-left: 60px solid transparent;
				border-right: 60px solid transparent;				
				border-top: 50px solid #f00;
				margin-top: -150px;
			}
		</style>
	</head><body>
		<?php    
			$dateObj   = DateTime::createFromFormat('!m', $month_bill);
			$month_name_now = $dateObj->format('F');    
			$path = './files/img/indonet-logo.png';
			$type = pathinfo($path, PATHINFO_EXTENSION);
			$data = file_get_contents($path);
			$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data); 
		?> 
		<div class="invoice-box">
			<table cellpadding="0" cellspacing="0">
				<tr class="top">
					<td colspan="2">
						<table>
							<tr>
								<td class="title">
									<img src="<?=$base64?>" class="logo-pt"/> 
                                    <br>                                    
                                    <span class="indonet-pt">PT IndoInternet Tbk</span><br>
                                    <span class="indonet-info"> Rumah Indonet 
                                        <br> Jl. Rempoa Raya No. 11 
                                        <br> Ciputat, Tangerang Selatan, Banten 15412
                                        <br> Telp: (021) 73882525  Fax: (021) 73882626
                                        <br> N.P.W.P. : 01.673.865.0-058.000  
                                        <br> PKP.: 01.673.865.0-058.000
                                    </span>
								</td> 
								<td>
                                    <h3>Informasi Tagihan</h3>
								</td>
							</tr>
						</table>
					</td>
				</tr>  
				<tr class="information">
					<td colspan="2">
                    <hr style="border-top: 2px solid black;">
						<table>
							<tr>
								<td>
                                    <h3 class="font-bold"><b><?=$data_cust['NAME']?></b></h3>
                                    <h4 class="font-bold"><?=$data_cust['KNOWNAS']?></h4>
                                    <h5 class="font-bold"><?=$data_cust['INVOICEADDRESS']?></h5>
								</td> 
								<td>
									<table class="total_info">
										<tr class="header">
											<td>Tgl. Jatuh Tempo</td> 
											<td>Jumlah Tagihan</td>
										</tr>
										<tr class="details">
											<?php 
											$inv_month_bill = number_format(round($inv_month_bill),2, '.', ',');  
											if($inv_month_bill < 0){ 
												$inv_month_bill = '('.str_replace("-","",$inv_month_bill).')'; 
											} 
											?>
											<td>20 <?php echo $month_name_now.' '. $inv_year_name; ?></td> 
											<td>IDR <?=$inv_month_bill;?></td>
										</tr> 
										<tr class="info-cust">
											<td>No. Tagihan</td> 
											<td><?=$billing_code?></td>
										</tr> 
										<tr class="info-cust">
											<td>No. Pelanggan</td> 
											<td><?=$data_cust['ACCOUNTNUM']?></td>
										</tr>  
										<!-- <tr class="info-cust">
											<td>Alamat Email</td> 
											<td><?=$data_cust['EMAIL']?></td>
										</tr>   -->
										<tr class="info-cust">
											<td>Subnet</td> 
											<td><?=$data_cust['DISTRICTNAME']?></td>
										</tr>  
									</table>  
								</td>								 
							</tr>  
						</table>
					</td>
				</tr>   
			</table>
			<table>
				<tr class="heading">
					<td>Ringkasan Tagihan</td> 
					<td colspan="2" style="text-align: center;">Total (IDR)</td>
				</tr> 
				<tr class="item">
					<?php
						foreach ($inv_detail_bill as $key => $value) {
							if($value['TRANSTYPE'] == '95'){        
								$val_amount = number_format(round($value['AMOUNTMST']),2, '.', ',');  
								if($val_amount < 0){ 
									$val_amount = '('.str_replace("-","",$val_amount).')'; 
								} 
								echo '<td>Saldo bulan lalu</td>'; 
								echo '<td style="text-align: right">IDR</td>'; 
								echo '<td style="text-align: right" class=" width-100">'.$val_amount.'</td>';
							}
						}
					?>
				</tr>  
				<tr class="item"> 
					<td  colspan="3">&nbsp;</td> 
				</tr> 
				<?php   
					array_multisort(array_column($inv_detail_bill, 'TRANSDATE'), SORT_ASC, $inv_detail_bill); 
					foreach ($inv_detail_bill as $key => $value) {   
						if($value['TRANSTYPE'] == '15' && $value['NOTPRINT'] != 1){   
							$val_amount = number_format(round($value['AMOUNTMST']),2, '.', ',');  
							if($val_amount < 0){ 
								$val_amount = '('.str_replace("-","",$val_amount).')'; 
							} 
							echo "<tr class='item'>";   
							echo '<td>'.date("d - M - Y",strtotime($value['DOCUMENTDATE'])).' <b>'.$value['TXT'].'</b></td>';   
							echo '<td style="text-align: right">IDR</td>'; 
							echo '<td style="text-align: right" class=" width-100">'.$val_amount.'</td>';
							echo "</tr>";  
						}
					}
				?> 
				<tr class="item"> 
					<td  colspan="3">&nbsp;</td> 
				</tr> 
				<?php  
					$inv_product = array();
					foreach ($inv_detail_bill as $key => $row){
						if($row['TRANSTYPE'] != '95' && $row['TRANSTYPE'] != '15' && $row['NOTPRINT'] != 1){  
							$wek[$key]  = $row['NAME'];
							$array_data = array('ACCOUNTNUM' => $row['ACCOUNTNUM'], 'NAME'=>$row['NAME'], 'INVOICEDATE'=>$row['INVOICEDATE'], 'AMOUNTMST'=>$row['AMOUNTMST']);
							array_push($inv_product, $array_data); 
						}
					}     
					array_multisort($wek, SORT_ASC, $inv_product);  
					$name_product = '';
					$array_inv = array(); 
					if((int)$month_bill.(int)$year_bill < 422){ 
						$ppn_tax = 1.1;
					}else{ 
						$ppn_tax = 1.11;
					}
					foreach ($inv_product as $key => $value) {    
						if($name_product == $value['NAME']){ 
							$name_product = $value['NAME'];
							$amount_ori = $value['AMOUNTMST']; 
							$amount = $amount+($amount_ori/$ppn_tax); 
							$inv_date = $value['INVOICEDATE']; 
							foreach ($array_inv as $key2 => $value2) {
								if($value2['name'] == $name_product){
									unset($array_inv[$key2]); 
								} 
							}  
							$array_save = array('name'=>$name_product, 'amount'=>$amount, 'inv_date'=>$inv_date);
							array_push($array_inv, $array_save);  
						}else{ 
							$name_product = $value['NAME'];
							$amount_ori = $value['AMOUNTMST']; 
							$amount = $amount_ori/$ppn_tax;
							$inv_date = $value['INVOICEDATE'];
							$array_save = array('name'=>$name_product, 'amount'=>$amount, 'inv_date'=>$inv_date);
							array_push($array_inv, $array_save);  
						} 
					} 
					$amount_total = 0;
					array_multisort(array_column($array_inv, 'inv_date'), SORT_ASC, $array_inv); 
					foreach ($array_inv as $key => $value) {    
						$val_amount = number_format(round($value['amount']),2, '.', ',');  
						if($val_amount < 0){ 
							$val_amount = '('.str_replace("-","",$val_amount).')'; 
						} 
						echo "<tr class='item'>";  
						echo '<td>'.date("d - M - Y",strtotime($value['inv_date'])).' <b>'.$value['name'].'</b></td>';                             
						echo '<td style="text-align: right">IDR</td>';
						echo '<td style="text-align: right"  class=" width-100">'.$val_amount.'</td>';
						echo "</tr>"; 
						$amount_total = $amount_total+$value['amount'];

					} 
					if((int)$month_bill.(int)$year_bill < 422){ 
						$ppn_text = 'PPN-VAT  (10%)'; 
						$ppn_val = round($amount_total*10/100);  
						$ppn_val = number_format($ppn_val,2, '.', ',');  
					}else{
						$ppn_text = 'PPN-VAT  (11%)'; 
						$ppn_val = round($amount_total*11/100);  
						$ppn_val = number_format($ppn_val,2, '.', ',');  
					}
					$amount_total = number_format(round($amount_total),2, '.', ',');   
					if($amount_total < 0){ 
						$amount_total = '('.str_replace("-","",$amount_total).')'; 
					}  
					echo "<tr class='item'>";  
					echo '<td colspan="3">&nbsp;</td>';                           
					echo "</tr>"; 
					echo "<tr class='item'>";                            
					echo '<td><b>Subtotal (Base Amount Taxable)</b></td>';                             
					echo '<td style="text-align: right">IDR</td>';
					echo '<td style="text-align: right"  class=" width-100">'.$amount_total.'</td>';
					echo "</tr>"; 
					echo "<tr class='item'>";                            
					echo '<td><b>'.$ppn_text.'</b></td>';                             
					echo '<td style="text-align: right">IDR</td>';
					echo '<td style="text-align: right"  class=" width-100">'.$ppn_val.'</td>';
					echo "</tr>"; 
				?>
				<tr class="heading">
					<td style="text-align: center">Jumlah Tagihan</td> 
					<td>IDR</td>
					<td style="text-align: right"><b><?=$inv_month_bill;?></b></td>
				</tr> 
			</table>
			<table class="info-end">
				<tr class="item-info-end">
					<td style="text-align: center">Pelanggan yth, bersama ini kami informasikan jatuh tempo pembayaran adalah tanggal 20 setiap bulannya.
						Mohon agar melakukan pembayaran paling lambat tanggal 20 untuk menghindari proses pemblokiran.
						Informasi lebih lanjut hubungi departmen billing di <b>+6221-2755-5222</b> atau email ke <b>billing@indonet.co.id</b>
						untuk konfirmasi apabila pembayaran telah dilakukan. <b>Sejak Tanggal 1 November 2021 ,Billing system Indonet hanya menerbitkan
						lembaran informasi tagihan ini,dan tidak dengan format lainnya</b>
					</td>  
				</tr>
			</table>
			<table class="">
				<tr class="item-info-end">
					<div class="arrow-down"></div>
					<td style="text-align: left; width: 30%; border: 1px solid black; background-color: #afb3b2;">
						Cantumkan <br>
						Alamat <b>E-mail</b> dan <b>No. Pelanggan</b> pada slip pembayaran anda 
					</td>  
					<td style="text-align: left; width: 70%;border: 1px solid black;">
						Jenis Pembayaran :<br>
						1. Kartu Kredit (Hub.6221-27555-222) dan Tunai (Kantor indonet pada jam kerja).<br>
						2. Transfer OCBC NISP (5458-0005-7287) atas nama PT.INDOINTERNET TBK<br>
						3. Online Payment https://my.indonet.id (VISA, MasterCard, JCB, QRIS)<br>
						4. Virtual Account: <?=$virtual_acc_bca['BANKTYPE']?> (<?=$virtual_acc_bca['MK_VIRTUALACCOUNT']?>) a/n <?=$data_cust['NAME']?><br>
					</td>  
				</tr>
			</table>
		</div>
		<div class="footer_div">
			<p><i>Proses penagihan ini dilakukan secara komputerisasi, tidak diperlukan tanda tangan yang berwenang</i></p>
		</div>
	</body>
	</html>