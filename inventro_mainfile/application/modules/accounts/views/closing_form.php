<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/modules/accounts/assets/css/closing.css')?>">
<div class="row">
    <div class="col-sm-12">
        <div class="mb-2">
            <a href="<?php echo base_url('accounts/account/closing_list') ?>" class="btn btn-primary m-b-5 m-r-2"><i class="ti-align-justify"> </i>
                <?php echo makeString(['closing_list']); ?>
            </a>
        </div>

        <div class="">
            <?php
            $error = $this->session->flashdata('error');
            $success = $this->session->flashdata('success');
            if ($error != '') {
                echo $error;
            }
            if ($success != '') {
                echo $success;
            }
            ?>
        </div>
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><?php echo html_escape($title); ?></h3>
            </div>
            
            <div class="card-body">
                <div class="col-sm-12">
                    
                        <div class="row">
                        <div class="col-sm-8">
                            <?php echo form_open('accounts/account/save_closing', array('class' => 'form-vertical', 'id' => 'validate')) ?>
                            <div class="form-group row">
                              <label for="last_closing" class="col-sm-3 control-label"><?php echo makeString(['last_closing_balance']); ?></label>
                              <div class="col-sm-6">
                                  <input type="text" name="last_closing_balance" class="form-control" id="last_closing" value="<?php echo html_escape($cash_data['closing_balance']);?>">
                              </div>
                          </div>

 <div class="form-group row">
            <label for="receipt" class="col-sm-3 control-label"><?php echo makeString(['receipt']); ?></label>
            <div class="col-sm-6">
                <input type="text" name="receipt" class="form-control" id="receipt" value="<?php echo html_escape($cash_data['debit']);?>">
            </div>
        </div>
         <div class="form-group row">
            <label for="payment" class="col-sm-3 control-label"><?php echo makeString(['payment']); ?></label>
            <div class="col-sm-6">
                <input type="text" name="payment" class="form-control" id="payment" value="<?php echo html_escape($cash_data['credit']);?>">
            </div>
        </div>
         <div class="form-group row">
            <label for="balance" class="col-sm-3 control-label"><?php echo makeString(['balance']); ?></label>
            <div class="col-sm-6">
                <input type="text" name="balance" class="form-control" id="balance" value="<?php echo html_escape($cash_data['balance']);?>">
            </div>
        </div>

        <div class="form-group row">
            <label for="adjustment" class="col-sm-3 control-label"><?php echo makeString(['adjustment']); ?></label>
            <div class="col-sm-6">
                <input type="text" name="adjustment" class="form-control" id="adjustment" value="">
            </div>
        </div>

         <div class="form-group row">
            <label for="balance" class="col-sm-3 control-label"></label>
            <div class="col-sm-4">
               <button type="submit" class="btn btn-primary form-control"><?php echo makeString(['save']);?></button>
            </div>
        </div>
        <?php echo form_close() ?>
                        </div>
                        
                        <div class="col-sm-4">
  <div class="calcontainer">
    <div class="screen">
      <h1 id="mainScreen">0</h1>
    </div>
    <table>
      <tr>
        <td><button value="7" id="7" onclick="InputSymbol(7)">7</button></td>
        <td><button value="8" id="8" onclick="InputSymbol(8)">8</button></td>
        <td><button value="9" id="9" onclick="InputSymbol(9)">9</button></td>
        <td><button onclick="DeleteLastSymbol()">c</button></td>
      </tr>
      <tr>
        <td><button value="4" id="4" onclick="InputSymbol(4)">4</button></td>
        <td><button value="5" id="5" onclick="InputSymbol(5)">5</button></td>
        <td><button value="6" id="6" onclick="InputSymbol(6)">6</button></td>
        <td><button value="/" id="104" onclick="InputSymbol(104)">/</button></td>
      </tr>
      <tr>
        <td><button value="1" id="1" onclick="InputSymbol(1)">1</button></td>
        <td><button value="2" id="2" onclick="InputSymbol(2)">2</button></td>
        <td><button value="3" id="3" onclick="InputSymbol(3)">3</button></td>
        <td><button value="*" id="103" onclick="InputSymbol(103)">*</button></td>
      </tr>
      <tr>
        <td><button value="0" id="0" onclick="InputSymbol(0)">0</button></td>
        <td><button value="." id="128" onclick="InputSymbol(128)">.</button></td>
        <td><button value="-" id="102" onclick="InputSymbol(102)">-</button></td>
        <td><button value="+" id="101" onclick="InputSymbol(101)">+</button></td>
      </tr>
      <tr>
        <td colspan="3"><button onclick="ClearScreen()">C</button></td>
        <td colspan="2"><button onclick="CalculateTotal()">=</button></td>
       
      </tr>
    </table>
</div>
                        </div>
                    </div>
                </div>
            </div>            
            
        </div>
    </div>
</div>

