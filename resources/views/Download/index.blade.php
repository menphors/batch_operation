<div class="table-flip-scroll">
                <table class="table table-sm table-bordered table-hover flip-content">
                    <thead class="flip-header">
                        <tr> 
                            <th scope="col">Remark</th>
                            <th scope="col">Executed_Date</th>
                            <th scope="col">Executed_By</th>
                            <th scope="col">PhoneNumber</th>    
                            <th scope="col">BatchNumber</th>  
                            <th scope="col">Respone</th>        
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customer_info as $completed_trn)
                            <tr> 
                        <?php
                      
                            if ($completed_trn->batch_number < 10)
                            $value = "0000000".$completed_trn->batch_number."";
                        else if ($completed_trn->batch_number< 100)
                            $value = "000000".$completed_trn->batch_number."";
                        else if ($completed_trn->batch_number< 1000)
                            $value = "0000".$completed_trn->batch_number."";
                        else if ($completed_trn->batch_number< 10000)
                            $value = "000".$completed_trn->batch_number."";
                        else if ($completed_trn->batch_number< 100000)
                            $value = "00".$completed_trn->batch_number."";
                        else if ($completed_trn->batch_number< 100000)
                            $value = "0".$completed_trn->batch_number."";
                        else if ($completed_trn->batch_number<   100000)
                            $value = "".$completed_trn->batch_number."";
                            $val=$completed_trn->batch_number;

                        ?>
                        
                                <td>{{$completed_trn->Remark}}</td>
                                <td>{{$completed_trn->Executed_Date}}</td>
                                <td>{{$completed_trn->user_name}}</td>
                                <td>{{$completed_trn->PhoneNumber}}</td>
                                <td>{{$value}}</td>
                                <td>{{$completed_trn->message}}</td>
                           
                               
                            </tr>
                        @endforeach
                    
                    </tbody>
                </table>
                {{$customer_info->links()}}
                <?php
                  header("Content-Type: application/xls");
                  header("Content-Disposition:attachment; filename=".$value."__Batch.xls");
                ?>
                
            </div>
