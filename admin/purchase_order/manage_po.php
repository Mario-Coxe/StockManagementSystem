<?php
if (isset($_GET['id'])) {
    $qry = $conn->query("SELECT p.*,s.name as supplier FROM purchase_order_list p inner join supplier_list s on p.supplier_id = s.id  where p.id = '{$_GET['id']}'");
    if ($qry->num_rows > 0) {
        foreach ($qry->fetch_array() as $k => $v) {
            $$k = $v;
        }
    }
}
?>
<style>
    select[readonly].select2-hidden-accessible+.select2-container {
        pointer-events: none;
        touch-action: none;
        background: #eee;
        box-shadow: none;
    }

    select[readonly].select2-hidden-accessible+.select2-container .select2-selection {
        background: #eee;
        box-shadow: none;
    }
</style>
<div class="card card-outline card-primary">
    <div class="card-header">
        <h4 class="card-title">
            <?php echo isset($id) ? "Detalhes do Pedido de Compra - " . $po_code : 'Criar Novo Pedido de Compra' ?>
        </h4>
    </div>
    <div class="card-body">
        <form action="" id="po-form">
            <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        <label class="control-label text-info">Código do P.O.</label>
                        <input type="text" class="form-control form-control-sm rounded-0"
                            value="<?php echo isset($po_code) ? $po_code : '' ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="supplier_id" class="control-label text-info">Fornecedor</label>
                            <select name="supplier_id" id="supplier_id" class="custom-select select2">
                                <option <?php echo !isset($supplier_id) ? 'selected' : '' ?> disabled></option>
                                <?php
                                $supplier = $conn->query("SELECT * FROM `supplier_list` where status = 1 order by `name` asc");
                                while ($row = $supplier->fetch_assoc()):
                                    ?>
                                    <option value="<?php echo $row['id'] ?>" <?php echo isset($supplier_id) && $supplier_id == $row['id'] ? "selected" : "" ?>><?php echo $row['name'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <hr>
                <fieldset>
                    <legend class="text-info">Formulário de Itens</legend>
                    <div class="row justify-content-center align-items-end">
                        <?php
                        $item_arr = array();
                        $cost_arr = array();
                        $item = $conn->query("SELECT * FROM `item_list` where status = 1 order by `name` asc");
                        while ($row = $item->fetch_assoc()):
                            $item_arr[$row['supplier_id']][$row['id']] = $row;
                            $cost_arr[$row['id']] = $row['cost'];
                        endwhile;
                        ?>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="item_id" class="control-label">Item</label>
                                <select id="item_id" class="custom-select ">
                                    <option disabled selected></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="quantity" class="control-label">Quantidade</label>
                                <input type="number" class="form-control form-control-sm" id="quantity" name="quantity">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="cost" class="control-label">Custo</label>
                                <input type="number" class="form-control form-control-sm" id="cost" name="cost">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="add_item" class="control-label">&nbsp;</label>
                                <button type="button" class="btn btn-sm btn-primary" id="add_item">Adicionar
                                    Item</button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered table-sm" id="item-list">
                                <colgroup>
                                    <col width="5%">
                                    <col width="40%">
                                    <col width="15%">
                                    <col width="15%">
                                    <col width="10%">
                                    <col width="10%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Item</th>
                                        <th>Quantidade</th>
                                        <th>Custo</th>
                                        <th>Total</th>
                                        <th>&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (isset($id)) {
                                        $items = $conn->query("SELECT i.*,i.id as id FROM `purchase_order_item` i where i.po_id = '{$id}'");
                                        while ($row = $items->fetch_assoc()):
                                            ?>
                                            <tr>
                                                <td></td>
                                                <td>
                                                    <?php echo isset($item_arr[$row['supplier_id']][$row['item_id']]) ? $item_arr[$row['supplier_id']][$row['item_id']]['name'] : '' ?>
                                                </td>
                                                <td>
                                                    <?php echo $row['quantity'] ?>
                                                </td>
                                                <td>
                                                    <?php echo $row['cost'] ?>
                                                </td>
                                                <td>
                                                    <?php echo number_format($row['quantity'] * $row['cost'], 2) ?>
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-outline-danger remove_item"><i
                                                            class="fa fa-trash"></i></button>
                                                    <input type="hidden" name="item_id[]" value="<?php echo $row['item_id'] ?>">
                                                    <input type="hidden" name="quantity[]"
                                                        value="<?php echo $row['quantity'] ?>">
                                                    <input type="hidden" name="cost[]" value="<?php echo $row['cost'] ?>">
                                                </td>
                                            </tr>
                                        <?php
                                        endwhile;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </fieldset>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <label class="control-label text-info">Total do Pedido de Compra</label>
                        <table class="table table-sm table-bordered">
                            <tbody>
                                <tr>
                                    <th>Subtotal</th>
                                    <td id="subtotal"></td>
                                </tr>
                                <tr>
                                    <th>Impostos</th>
                                    <td id="tax"></td>
                                </tr>
                                <tr>
                                    <th>Total</th>
                                    <td id="total"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="notes" class="control-label">Notas</label>
                            <textarea class="form-control form-control-sm" id="notes" name="notes" rows="4"></textarea>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <button class="btn btn-sm btn-primary">
                            <?php echo isset($id) ? "Atualizar" : "Salvar" ?>
                        </button>
                        <a href="./" class="btn btn-sm btn-danger">Cancelar</a>
                    </div>
                </div>
        </form>
    </div>
</div>
</div>
<script>
    $(document).ready(function () {
        $('#item-list').DataTable();
        $('select[name="supplier_id"]').select2({
            placeholder: 'Selecione o fornecedor',
            width: '100%'
        });
        $('.summable').keyup(function () {
            var s = 0;
            $('.summable').each(function () {
                s += isNaN(parseInt($(this).val())) ? 0 : parseInt($(this).val());
            })
            $('#subtotal').html(accounting.formatMoney(s));
            compute_total();
        })
        $('input[name="discount_percentage"],input[name="tax_percentage"]').change(function () {
            compute_total();
        })
        $('input[name="discount_percentage"],input[name="tax_percentage"]').keyup(function () {
            compute_total();
        })
        $('input[name="tax_percentage"]').keyup(function () {
            compute_total();
        })
        $('input[name="discount_percentage"]').keyup(function () {
            compute_total();
        })
        $('input[name="tax_percentage"]').change(function () {
            compute_total();
        })
        $('input[name="discount_percentage"]').change(function () {
            compute_total();
        })
        $('#add_item').click(function () {
            if ($('#item-list > tbody > tr').length == 0) {
                $('#item-list > tbody').html('');
            }
            if ($('input[name="supplier_id"]').val() == '') {
                alert_toast('Selecione um fornecedor primeiro.', "warning");
                return false;
            }
            if ($('input[name="item_id"]').val() == '') {
                alert_toast('Selecione um item primeiro.', "warning");
                return false;
            }
            if ($('#quantity').val() == '') {
                alert_toast('Digite uma quantidade primeiro.', "warning");
                return false;
            }
            if ($('#cost').val() == '') {
                alert_toast('Digite o custo primeiro.', "warning");
                return false;
            }
            $.ajax({
                url: 'ajax.php?action=save_purchase_order_item',
                method: 'POST',
                data: { po_id: '<?php echo isset($id) ? $id : '' ?>', supplier_id: $('input[name="supplier_id"]').val(), item_id: $('input[name="item_id"]').val(), quantity: $('#quantity').val(), cost: $('#cost').val() },
                dataType: 'json',
                success: function (resp) {
                    if (resp.status == 'success') {
                        $('#item-list > tbody').append('<tr><td></td><td>' + resp.name + '</td><td>' + resp.quantity + '</td><td>' + resp.cost + '</td><td>' + resp.total + '</td><td class="text-center"><button class="btn btn-sm btn-outline-danger remove_item"><i class="fa fa-trash"></i></button><input type="hidden" name="item_id[]" value="' + resp.item_id + '"><input type="hidden" name="quantity[]" value="' + resp.quantity + '"><input type="hidden" name="cost[]" value="' + resp.cost + '"></td></tr>');
                        alert_toast('Item adicionado com sucesso.', "success");
                        $('input[name="supplier_id"]').val('').trigger('change');
                        $('input[name="item_id"]').val('');
                        $('#quantity').val('');
                        $('#cost').val('');
                        $('.summable').keyup();
                    }
                }
            })
        })
        $(document).on('click', '.remove_item', function () {
            var tr = $(this).closest('tr');
            tr.remove();
            $('.summable').keyup();
        })
    })
    function compute_total() {
        var subtotal = $('#subtotal').html().replace(/[$,]/g, '');
        var discount = $('input[name="discount_percentage"]').val().replace(/[$,%]/g, '');
        var tax = $('input[name="tax_percentage"]').val().replace(/[$,%]/g, '');
        discount = isNaN(discount) ? 0 : parseFloat(discount) / 100;
        tax = isNaN(tax) ? 0 : parseFloat(tax) / 100;
        var total = parseFloat(subtotal) - (parseFloat(subtotal) * parseFloat(discount));
        total = total + (total * parseFloat(tax));
        $('#total').html(accounting.formatMoney(total));
    }
</script>