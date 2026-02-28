<div class="card card-primary card-outline">
    <?php if ($this->permission->method('customer', 'create')->access()): ?>
        <div class="card-header">
            <h4><?php echo makeString(['customer_list']); ?>
                <small class="float-right">
                    <button type="button" class="btn btn-success btn-md" data-target="#csvModal" data-toggle="modal"  ><i class="ti-plus" aria-hidden="true"></i>
                        <?php echo makeString(['import_csv']); ?></button>
                    <button type="button" class="btn btn-primary btn-md" data-target="#add0" data-toggle="modal"  ><i class="ti-plus" aria-hidden="true"></i>
                        <?php echo html_escape("Adicionar Novo Cliente"); ?></button>
                </small>
            </h4>
        </div>
    <?php endif; ?>
    <div id="csvModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <strong><?php echo makeString(['import_csv']); ?></strong>
                    <a href="<?php echo base_url('admin_assets/csv/customer_csv_sample.csv'); ?>" class="btn btn-primary pull-right">
                        <i class="fa fa-download"> </i> <?php echo makeString(['download_sample_file']); ?>
                    </a>
                    
                </div>
                <div class="modal-body">
                <?php echo form_open_multipart('customer/customer_info/customer_csv_upload') ?>
                    
                        <div class="form-group row">
                            <label for="firstname" class="col-sm-4 col-form-label"><?php echo makeString(['upload_csv_file']) ?> <span class="txt-color">*</span></label>
                            <div class="col-sm-8">
                                <input name="csv_file" class="form-control" type="file" id="customer_name" required>
                            </div>
                        </div>
                        <div class="form-group-row">
                            <label class="col-sm-4">&nbsp;</label>
                            <div class="col-sm-8">
                                <button type="submit" class="btn btn-success w-md m-b-5"><?php echo makeString(['upload']) ?></button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                </div>
            </div>

        </div>
    </div>
    <div id="add0" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <strong><?php echo makeString(['add_new']); ?></strong>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-12">
                            <div class="panel">
                                <div class="panel-body">
                                    <?php echo  form_open('customer/customer_info/create'); ?>
                                    <?php echo form_hidden('id', (!empty($intinfo->id) ? html_escape($intinfo->id) : null)) ?>
                                    <div class="form-group row">
                                        <label for="firstname" class="col-sm-4 col-form-label"><?php echo makeString(['customer_name']) ?> <span class="txt-color">*</span></label>
                                        <div class="col-sm-8">
                                            <input name="customer_name" autocomplete="off" class="form-control" type="text" placeholder="<?php echo makeString(['customer_name']) ?>" id="customer_name" value="">
                                        </div>
                                    </div>

                                    <div class="form-group row">

                                        <label for="email" class="col-sm-4 col-form-label"><?php echo makeString(['email']) ?> <span class="txt-color">*</span></label>
                                        <div class="col-sm-8">
                                            <input name="email" autocomplete="off" class="form-control" type="text" placeholder="<?php echo makeString(['email']) ?>" id="email" value="">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="phone" class="col-sm-4 col-form-label"><?php echo makeString(['mobile']) ?><span class="txt-color">*</span> </label>
                                        <div class="col-sm-8">
                                            <input name="mobile" autocomplete="off" class="form-control telefone-mask" type="text" placeholder="(00) 00000-0000" id="mobile" value="">
                                        </div>
                                    </div>
                                    
                                    <!-- CAMPOS BRASILEIROS -->
                                    <div class="form-group row">
                                        <label for="tipo_pessoa" class="col-sm-4 col-form-label"><?php echo makeString(['tipo_pessoa']) ?></label>
                                        <div class="col-sm-8">
                                            <select class="form-control" name="tipo_pessoa" id="tipo_pessoa" onchange="toggleCpfCnpj(this.value)">
                                                <option value="F"><?php echo makeString(['pessoa_fisica']) ?></option>
                                                <option value="J"><?php echo makeString(['pessoa_juridica']) ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row" id="cpf_group">
                                        <label for="cpf" class="col-sm-4 col-form-label">CPF</label>
                                        <div class="col-sm-8">
                                            <input name="cpf" autocomplete="off" class="form-control cpf-mask" type="text" placeholder="000.000.000-00" id="cpf" value="">
                                        </div>
                                    </div>
                                    <div class="form-group row" id="cnpj_group" style="display:none;">
                                        <label for="cnpj" class="col-sm-4 col-form-label">CNPJ</label>
                                        <div class="col-sm-8">
                                            <input name="cnpj" autocomplete="off" class="form-control cnpj-mask" type="text" placeholder="00.000.000/0000-00" id="cnpj" value="">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="cep" class="col-sm-4 col-form-label">CEP</label>
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <input name="cep" autocomplete="off" class="form-control cep-mask" type="text" placeholder="00000-000" id="cep" value="">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-info" onclick="buscarCEP()"><i class="fa fa-search"></i> Buscar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="cidade" class="col-sm-4 col-form-label"><?php echo makeString(['cidade']) ?></label>
                                        <div class="col-sm-8">
                                            <input name="cidade" autocomplete="off" class="form-control" type="text" placeholder="Cidade" id="cidade" value="">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="estado" class="col-sm-4 col-form-label"><?php echo makeString(['estado']) ?></label>
                                        <div class="col-sm-8">
                                            <select name="estado" class="form-control" id="estado">
                                                <option value="">Selecione...</option>
                                                <option value="AC">Acre</option>
                                                <option value="AL">Alagoas</option>
                                                <option value="AP">Amapá</option>
                                                <option value="AM">Amazonas</option>
                                                <option value="BA">Bahia</option>
                                                <option value="CE">Ceará</option>
                                                <option value="DF">Distrito Federal</option>
                                                <option value="ES">Espírito Santo</option>
                                                <option value="GO">Goiás</option>
                                                <option value="MA">Maranhão</option>
                                                <option value="MT">Mato Grosso</option>
                                                <option value="MS">Mato Grosso do Sul</option>
                                                <option value="MG">Minas Gerais</option>
                                                <option value="PA">Pará</option>
                                                <option value="PB">Paraíba</option>
                                                <option value="PR">Paraná</option>
                                                <option value="PE">Pernambuco</option>
                                                <option value="PI">Piauí</option>
                                                <option value="RJ">Rio de Janeiro</option>
                                                <option value="RN">Rio Grande do Norte</option>
                                                <option value="RS">Rio Grande do Sul</option>
                                                <option value="RO">Rondônia</option>
                                                <option value="RR">Roraima</option>
                                                <option value="SC">Santa Catarina</option>
                                                <option value="SP">São Paulo</option>
                                                <option value="SE">Sergipe</option>
                                                <option value="TO">Tocantins</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- FIM CAMPOS BRASILEIROS -->
                                    
                                    <div class="form-group row">
                                        <label for="mobile" class="col-sm-4 col-form-label"><?php echo makeString(['previous_balance']) ?> </label>
                                        <div class="col-sm-8">
                                            <input name="previous_balance" class="form-control" type="number" placeholder="<?php echo makeString(['previous_balance']) ?>" id="previous_balance" value="">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="address" class="col-sm-4 col-form-label"><?php echo makeString(['isreceipt']) ?> </label>
                                        <div class="col-sm-8">
                                            <select class="form-control select2 content-width1" name="paytype">
                                                <option selected="selected" value="c"><?php echo makeString(['received_amount']); ?></option>
                                                <option value="d"><?php echo makeString(['payment_amount']); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="address" class="col-sm-4 col-form-label"><?php echo makeString(['address']) ?> </label>
                                        <div class="col-sm-8">
                                            <textarea name="address" cols="30" rows="3" autocomplete="off" class="form-control" placeholder="<?php echo makeString(['address']) ?>"></textarea>
                                        </div>

                                    </div>

                                    <div class="form-group row">
                                        <label for="address" class="col-sm-4 col-form-label"><?php echo makeString(['status']) ?> </label>
                                        <div class="col-sm-8">
                                            <select class="form-control select2 content-width1" name="status" data-placeholder="<?php echo makeString(['select_one']); ?>">
                                                <option value=""></option>
                                                <option selected="selected" value="1"><?php echo makeString(['active']); ?></option>
                                                <option value="0"><?php echo makeString(['inactive']); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group text-right">
                                        <button type="reset" class="btn btn-primary w-md m-b-5"><?php echo makeString(['reset']) ?></button>
                                        <button type="submit" class="btn btn-success w-md m-b-5"><?php echo makeString(['ad']) ?></button>
                                    </div>
                                    <?php echo form_close() ?>
                                </div>  
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>

    <div id="edit" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <strong><?php echo makeString(['update']); ?></strong>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body editinfo">
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
    <div class="row">
        <!--  table area -->
        <div class="col-sm-12">
            <div class="card-body">
                <table id="datagrid" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th><?php echo makeString(['sl_no']) ?></th>
                            <th><?php echo makeString(['customer_name']) ?></th>
                            <th><?php echo makeString(['mobile']) ?></th>
                            <th><?php echo makeString(['email']) ?></th>
                            <th><?php echo makeString(['status']) ?></th>
                            <th><?php echo makeString(['action']) ?></th> 
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($customer_infolist)) {
                            ?>
                            <?php $sl = 1; ?>
                            <?php foreach ($customer_infolist as $customer) { ?>
                                <tr class="<?php echo ($sl & 1) ? "odd gradeX" : "even gradeC" ?>">
                                    <td><?php echo $sl; ?></td>
                                    <td><?php echo html_escape($customer->name); ?></td>
                                    <td><?php echo html_escape($customer->mobile); ?></td>
                                    <td><?php echo html_escape($customer->email); ?></td>
                                    <td><?php
                                        if ($customer->status == 1) {
                                            echo html_escape("Ativo");
                                        } else {
                                            echo html_escape("Inativo");
                                        }
                                        ?>
                                        </td>
                                    <td class="center">
                                        <?php if ($this->permission->method('customer', 'update')->access()): ?>
                                            <input name="url" type="hidden" id="url_<?php echo $customer->id; ?>" value="<?php echo base_url("customer/customer_info/updateintfrm") ?>" />
                                            <a onclick="editinfo('<?php echo $customer->id; ?>')" class="btn btn-info btn-sm text-cl1 text-white" data-toggle="tooltip" data-placement="left" title="Editar"><i class="fas fa-edit" aria-hidden="true"></i></a> 
                                            <?php
                                        endif;

                                        if ($this->permission->method('customer', 'delete')->access()):
                                            ?>
                                            <a href="<?php echo base_url("customer/customer_info/delete/$customer->id") ?>" onclick="return confirm('<?php echo makeString(["are_you_sure"]) ?>')" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="right" title="Excluir"><i class="fa fa-trash" aria-hidden="true"></i></a> 
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php $sl++; ?>
                            <?php } ?> 
                        <?php } ?> 
                    </tbody>
                </table>  
            </div>
        </div>
    </div>
</div>