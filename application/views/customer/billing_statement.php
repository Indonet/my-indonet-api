<style>
    .logo-indonet{
        width: 200px;
    }
</style> 
<div id="section-to-print" style="background-color: white;"> 
    <div class="" style="width: 100%; ">
        <div  style="width: 80%; float: left">
            <img src="/var/www/api-my.indonet.id/files/img/indonet-logo.png" class="logo-indonet">
            <br>
            <br>
            <strong>PT IndoInternet Tbk</strong><br>
            <p class="">Rumah Indonet
                <br> Jl. Rempoa Raya No. 11 
                <br> Ciputat, Tangerang Selatan, Banten 15412
                <br> Telp: (021) 73882525  Fax: (021) 73882626
                <br> N.P.W.P. : 01.673.865.0-058.000  
                <br> PKP.: 01.673.865.0-058.000
            </p>
        </div> 
        <div style="width: 20%; float: right">
            <h1>Billing Statement</h1>
        </div>  
    </div>
    <div style="width: 100%;">
    <hr>
    </div>
    <div class="row justify-content-center"> 
        <?php    
            $dateObj   = DateTime::createFromFormat('!m', $month_bill);
            $month_name_now = $dateObj->format('F');   
        ?>
        <div class="col-md-11">
            <div class="d-flex  flex-column flex-md-row"> 
                <div class="d-flex flex-column px-0 col-md-8">   
                    <div class="width-400">  
                        <h3 class="font-bold"><b><?=$data_cust['NAME']?></b></h3>
                        <h4 class="font-bold"><?=$data_cust['KNOWNAS']?></h4>
                        <h5 class="font-bold"><?=$data_cust['INVOICEADDRESS']?></h5>
                    </div>
                </div>
                <div class="d-flex flex-column col-md-2">  
                    <table class="table display_inv text-center">
                        <thead>
                            <tr>
                                <th><b>Due Date</b></th> 
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>  <b>20 <?php echo $month_name_now.' '. $year_bill; ?> </b></td> 
                            </tr> 
                        </tbody>
                    </table> 
                </div>
                <div class="d-flex flex-column col-md-2">
                    <table class="table display_inv text-center">
                        <thead>
                            <tr>
                                <th> <b>Amount Billing </b></th> 
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td> <b>IDR <?=number_format($inv_month_bill, 2, '.', ',');?>  </b></td> 
                            </tr> 
                        </tbody>
                    </table> 
                </div> 
            </div> 
        </div> 
        <div class="col-md-11">
            <div class="d-flex  flex-column flex-md-row"> 
                <div class="d-flex flex-column px-0 col-md-8">  
                    &nbsp;
                </div>
                <div class="d-flex flex-column col-md-4">  
                    <table class="table display_info_inv"> 
                        <tbody>
                            <tr>
                                <td><b>Invoice Month</b></td> 
                                <td><?php echo $month_name_now.' '.$year_bill; ?></td> 
                            </tr>
                            <tr>
                                <td><b>Billing No</b></td> 
                                <td>SO-<?php echo $month_bill.''.substr($year_bill,2); ?>-<?=$data_cust['ACCOUNTNUM']?></td> 
                            </tr>
                            <tr>
                                <td><b>Customer No</b></td> 
                                <td><?=$data_cust['ACCOUNTNUM']?></td> 
                            </tr> 
                        </tbody>
                    </table> 
                </div>  
            </div> 
        </div>
    </div> 
    <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0"> 
        <div class="col-md-10">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="pl-0 font-weight-bold text-muted text-uppercase">Date</th>
                            <th class="text-center font-weight-bold text-muted text-uppercase">Description</th> 
                            <th class="text-center pr-0 font-weight-bold text-muted text-uppercase width-100" colspan="2">Total</th>
                        </tr>
                    </thead>
                    <tbody> 
                        <tr class="font-weight-boldest"> 
                            <?php
                                foreach ($inv_detail_bill as $key => $value) {
                                    if($value['TRANSTYPE'] == '95'){                             
                                        echo '<td>&nbsp;</td>';
                                        echo '<td>Saldo bulan lalu</td>'; 
                                        echo '<td style="text-align: right">IDR</td>';
                                        echo '<td style="text-align: right" class=" width-100">'.number_format($value['AMOUNTMST'],2, '.', ',').'</td>';
                                    }
                                }
                            ?>
                        </tr>
                        <tr> 
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <?php  
                            array_multisort(array_column($inv_detail_bill, 'TXT'), SORT_ASC, $inv_detail_bill);
                            foreach ($inv_detail_bill as $key => $value) {   
                                if($value['TRANSTYPE'] == '15' && $value['NOTPRINT'] != 1){  
                                    echo "<tr class='font-weight-boldest'>";   
                                    echo '<td>'.date("d - M - Y",strtotime($value['DOCUMENTDATE'])).' <b>'.$value['TXT'].'</b></td>';                          
                                    echo '<td>'.$value['TXT'].'</td>'; 
                                    echo '<td style="text-align: right">IDR</td>';
                                    echo '<td style="text-align: right"  class=" width-100">'.number_format($value['AMOUNTMST'],2, '.', ',').'</td>';
                                    echo "</tr>";  
                                }
                            }
                        ?>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td> 
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
                            echo $month_bill;
                            foreach ($inv_product as $key => $value) {    
                                if($name_product == $value['NAME']){ 
                                    $name_product = $value['NAME'];
                                    $amount_ori = $value['AMOUNTMST'];
                                    $amount = $amount+($amount_ori/1.1); 
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
                                    $amount = $amount_ori/1.1;
                                    $inv_date = $value['INVOICEDATE'];
                                    $array_save = array('name'=>$name_product, 'amount'=>$amount, 'inv_date'=>$inv_date);
                                    array_push($array_inv, $array_save);  
                                } 
                            } 
                            $amount_total = 0;
                            foreach ($array_inv as $key => $value) {    
                                echo "<tr class='font-weight-boldest'>";  
                                echo '<td>'.date("d - M - Y",strtotime($value['inv_date'])).'</td>';                          
                                echo '<td>'.$value['name'].'</td>';                             
                                echo '<td style="text-align: right">IDR</td>';
                                echo '<td style="text-align: right"  class=" width-100">'.number_format($value['amount'],2, '.', ',').'</td>';
                                echo "</tr>"; 
                                $amount_total = $amount_total+$value['amount'];

                            }
                            echo "<tr class='font-weight-boldest'>";  
                            echo '<td colspan="4">&nbsp;</td>';                           
                            echo "</tr>"; 
                            echo "<tr class='font-weight-boldest'>";                            
                            echo '<td colspan="2">Subtotal (Base Amount Taxable)</td>';                             
                            echo '<td style="text-align: right">IDR</td>';
                            echo '<td style="text-align: right"  class=" width-100">'.number_format($amount_total,2, '.', ',').'</td>';
                            echo "</tr>"; 
                            echo "<tr class='font-weight-boldest'>";                            
                            echo '<td colspan="2">PPN-VAT  (10%)</td>';                             
                            echo '<td style="text-align: right">IDR</td>';
                            echo '<td style="text-align: right"  class=" width-100">'.number_format($amount_total*10/100,2, '.', ',').'</td>';
                            echo "</tr>"; 
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div> 
    <div class="row justify-content-center bg-gray-100 py-8 px-8 py-md-10 px-md-0">
        <div class="col-md-10">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>  
                            <th class="font-weight-bold text-right text-muted text-uppercase">TOTAL AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="font-weight-bolder">  
                            <td class="text-danger text-right font-size-h3 font-weight-boldest">IDR <?=number_format($inv_month_bill,2, '.', ',');?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div> 
    <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0 hide">
        <div class="col-md-10">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>  
                            <th class="font-weight-bold text-right text-muted text-uppercase">TOTAL AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="font-weight-bolder">  
                            <td class="text-danger text-right font-size-h3 font-weight-boldest">IDR <?=number_format($inv_month_bill,2, '.', ',');?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div> 
</div> 