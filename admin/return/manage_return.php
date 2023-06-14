<div class="col-md-2">
                            <div class="form-group">
                                <label for="quantity" class="control-label">Quantidade</label>
                                <input type="number" id="quantity" class="form-control" min="1" value="1">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="cost" class="control-label">Custo</label>
                                <input type="text" id="cost" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-primary" id="add_item">Adicionar</button>
                        </div>
                    </div>
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Quantidade</th>
                                <th>Custo</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(isset($id)): ?>
                            <?php 
                            $items = $conn->query("SELECT ri.*,il.name as item FROM return_item ri inner join item_list il on ri.item_id = il.id where ri.return_id = $id ");
                            while($row = $items->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php echo $row['item'] ?></td>
                                <td><?php echo $row['quantity'] ?></td>
                                <td><?php echo number_format($row['cost'],2) ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="rem_item('<?php echo $row['id'] ?>')">Remover</button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </fieldset>
                <hr>
                <div class="form-group">
                    <label for="remarks" class="control-label text-info">Observações</label>
                    <textarea name="remarks" id="remarks" cols="30" rows="3" class="form-control"><?php echo isset($remarks) ? $remarks : '' ?></textarea>
                </div>
            </div>
        </form>
    </div>
    <div class="card-footer">
        <button form="return-form" class="btn btn-primary float-right">Salvar</button>
        <a href="./" class="btn btn-default">Cancelar</a>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('.select2').select2({placeholder:"Por favor, selecione aqui",width:"relative"});
        $('[name="supplier_id"]').trigger('change');
    })
    $('[name="supplier_id"]').change(function(){
        var supplier_id = $(this).val();
        $('#item_id').html('<option disabled selected></option>');
        $.ajax({
            url:'get_item.php?id='+supplier_id,
            success:function(resp){
                if(resp){
                    resp = JSON.parse(resp);
                    if(resp.length > 0){
                        $.each(resp,function(i,v){
                            $('#item_id').append('<option value="'+v.id+'" data-cost="'+v.cost+'" >'+v.name+'</option>')
                        })
                    }
                }
            }
        })
    })
    $('#add_item').click(function(){
        var item_id = $('#item_id').val();
        var quantity = $('#quantity').val();
        var cost = $('#item_id option:selected').data('cost');
        var item_name = $('#item_id option:selected').text();
        var error = 0;
        if(item_id == null){
            error++;
            $('#item_id').addClass('is-invalid')
        }else{
            $('#item_id').removeClass('is-invalid')
        }
        if(quantity <= 0){
            error++;
            $('#quantity').addClass('is-invalid')
        }else{
            $('#quantity').removeClass('is-invalid')
        }
        if(!error){
            var tr = $('<tr></tr>');
            tr.append('<td>'+item_name+'</td>')
            tr.append('<td>'+quantity+'</td>')
            tr.append('<td>'+cost+'</td>')
            tr.append('<td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="rem_tr(this)">Remover</button><input type="hidden" name="item_id[]" value="'+item_id+'"><input type="hidden" name="cost[]" value="'+cost+'"><input type="hidden" name="quantity[]" value="'+quantity+'"></td>')
            $('[name="item_id"]').val(null).trigger('change')
            $('#quantity').val(1)
            $('#cost').val('')
            $('table > tbody').append(tr)
            calculate_total()
        }
    })
    function rem_tr(_this){
        $(_this).closest('tr').remove();
        calculate_total()
    }
    function calculate_total(){
        var total = 0;
        $('table > tbody > tr').each(function(){
            var cost = $(this).find('td:eq(2)').text()
            total += parseFloat(cost);
        })
        $('[name="total_cost"]').val(total.toFixed(2));
    }
</script>