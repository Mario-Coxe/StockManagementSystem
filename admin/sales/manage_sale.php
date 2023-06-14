<?php
if (isset($_GET['id'])) {
    $qry = $conn->query("SELECT * FROM sales_list where id = '{$_GET['id']}'");
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
            <?php echo isset($id) ? "Detalhes da Venda - " . $sales_code : 'Criar novo registro de venda' ?>
        </h4>
    </div>
    <div class="card-body">
        <form action="" id="sale-form">
            <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        <label class="control-label text-info">Código da Venda</label>
                        <input type="text" class="form-control form-control-sm rounded-0"
                            value="<?php echo isset($sales_code) ? $sales_code : '' ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="client" class="control-label text-info">Nome do Cliente</label>
                            <input type="text" name="client" class="form-control form-control-sm rounded-0"
                                value="<?php echo isset($client) ? $client : 'Convidado' ?>">
                        </div>
                    </div>
                </div>
                <hr>
                <fieldset>
                    <legend class="text-info">Formulário do Item</legend>
                    <div class="row justify-content-center align-items-end">
                        <?php
                        $item_arr = array();
                        $cost_arr = array();
                        $item = $conn->query("SELECT * FROM `item_list` where status = 1 order by `name` asc");
                        while ($row = $item->fetch_assoc()):
                            $item_arr[$row['id']] = $row;
                            $cost_arr[$row['id']] = $row['cost'];
                        endwhile;
                        ?>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="item_id" class="control-label">Item</label>
                                <select id="item_id" class="custom-select select2">
                                    <option disabled selected></option>
                                    <?php foreach ($item_arr as $k => $v): ?>
                                        <option value="<?php echo $k ?>"> <?php echo $v['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="unit" class="control-label">Unidade</label>
                                <input type="text" class="form-control rounded-0" id="unit">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="qty" class="control-label">Quantidade</label><input type="number"
                                    class="form-control rounded-0" id="qty" min="1">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="total" class="control-label">Total</label>
                                <input type="text" class="form-control rounded-0" id="total" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="button" class="btn btn-sm btn-primary" id="add_item">Adicionar Item</button>
                        </div>
                    </div>
                    <hr>
                    <table class="table table-bordered" id="item-list">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Unidade</th>
                                <th>Quantidade</th>
                                <th>Total</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($id)):
                                $sales_item = $conn->query("SELECT * FROM `sales_item` where `sales_id` = '{$id}'");
                                while ($row = $sales_item->fetch_assoc()):
                                    ?>
                                    <tr data-id="<?php echo $row['id'] ?>">
                                        <td>
                                            <?php echo $item_arr[$row['item_id']]['name'] ?>
                                        </td>
                                        <td>
                                            <?php echo $row['unit'] ?>
                                        </td>
                                        <td>
                                            <?php echo $row['qty'] ?>
                                        </td>
                                        <td>
                                            <?php echo $row['total'] ?>
                                        </td>
                                        <td>
                                            <center>
                                                <button class="btn btn-sm btn-outline-danger remove_item"><i
                                                        class="fa fa-trash"></i></button>
                                            </center>
                                        </td>
                                    </tr>
                                <?php
                                endwhile;
                            endif; ?>
                        </tbody>
                    </table>
                </fieldset>
            </div>
        </form>
    </div>
    <div class="card-footer">
        <button class="btn btn-flat btn-sm btn-success" form="sale-form">Salvar</button>
        <a class="btn btn-flat btn-sm btn-default" href="./">Cancelar</a>
    </div>
</div>
<script>
    $(function () {
        $('select.select2').select2({ placeholder: "Selecione aqui", width: "100%" });
        $('.select2-container').css('width', '100%')
        $('.select2-search__field').css('width', '100%')
        $('#item_id').change(function () {
            var id = $(this).val();
            var cost = '<?php echo json_encode($cost_arr) ?>';
            cost = JSON.parse(cost);
            $('#unit').val(cost[id]);
        })
        $('#qty').keyup(function () {
            var qty = $(this).val();
            var cost = $('#unit').val();
            var total = parseFloat(qty) * parseFloat(cost);
            $('#total').val(total.toFixed(2));
        })
        $('#add_item').click(function () {
            var item_id = $('#item_id').val();
            var item_name = $('#item_id option:selected').text();
            var unit = $('#unit').val();
            var qty = $('#qty').val();
            var total = $('#total').val();
            var tr = $('<tr data-id="' + item_id + '"></tr>');
            tr.append('<td>' + item_name + '</td>')
            tr.append('<td>' + unit + '</td>')
            tr.append('<td>' + qty + '</td>')
            tr.append('<td>' + total + '</td>')
            tr.append('<td><center><button class="btn btn-sm btn-outline-danger remove_item"><i class="fa fa-trash"></i></button></center></td>')
            $('#item-list tbody').append(tr);
            $('#item_id').val('');
            $('#unit').val('');
            $('#qty').val('');
            $('#total').val('');
        })
        $(document).on('click', '.remove_item', function () {
            $(this).closest('tr').remove()
        })
    })
</script>