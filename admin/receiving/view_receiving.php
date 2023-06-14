<?php
$qry = $conn->query("SELECT * FROM lista_recebimento where id = '{$_GET['id']}'");
if ($qry->num_rows > 0) {
    foreach ($qry->fetch_array() as $k => $v) {
        $$k = $v;
    }
    if ($from_order == 1) {
        $po_qry = $conn->query("SELECT p.*,s.nome as fornecedor FROM `lista_pedido_compra` p inner join `lista_fornecedor` s on p.id_fornecedor = s.id where p.id= '{$form_id}' ");
        if ($po_qry->num_rows > 0) {
            foreach ($po_qry->fetch_array() as $k => $v) {
                if (!isset($$k))
                    $$k = $v;
            }
        }
    } else {
        $qry = $conn->query("SELECT b.*,s.nome as fornecedor,p.codigo_po FROM lista_back_order b inner join lista_fornecedor s on b.id_fornecedor = s.id inner join lista_pedido_compra p on b.id_po = p.id  where b.id = '{$form_id}'");
        if ($qry->num_rows > 0) {
            foreach ($qry->fetch_array() as $k => $v) {
                if ($k == 'id')
                    $k = 'bo_id';
                if (!isset($$k))
                    $$k = $v;
            }
        }
    }
}
?>
<div class="card card-outline card-primary">
    <div class="card-header">
        <h4 class="card-title">Detalhes do Pedido Recebido -
            <?php echo $codigo_po ?>
        </h4>
    </div>
    <div class="card-body" id="print_out">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <label class="control-label text-info">Código do P.O. de</label>
                    <div>
                        <?php echo isset($codigo_po) ? $codigo_po : '' ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="id_fornecedor" class="control-label text-info">Fornecedor</label>
                        <div>
                            <?php echo isset($fornecedor) ? $fornecedor : '' ?>
                        </div>
                    </div>
                </div>
                <?php if (isset($bo_id)): ?>
                    <div class="col-md-6">
                        <label class="control-label text-info">Código do B.O. de</label>
                        <div>
                            <?php echo isset($codigo_bo) ? $codigo_bo : '' ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <h4 class="text-info">Pedidos</h4>
            <table class="table table-striped table-bordered" id="list">
                <colgroup>
                    <col width="10%">
                    <col width="10%">
                    <col width="30%">
                    <col width="25%">
                    <col width="25%">
                </colgroup>
                <thead>
                    <tr class="text-light bg-navy">
                        <th class="text-center py-1 px-2">Qtd</th>
                        <th class="text-center py-1 px-2">Unidade</th>
                        <th class="text-center py-1 px-2">Item</th>
                        <th class="text-center py-1 px-2">Custo</th>
                        <th class="text-center py-1 px-2">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    $qry = $conn->query("SELECT s.*,i.nome,i.descricao FROM `lista_estoque` s inner join lista_item i on s.id_item = i.id where s.id in ({$stock_ids})");
                    while ($row = $qry->fetch_assoc()):
                        $total += $row['total']
                            ?>
                        <tr>
                            <td class="py-1 px-2 text-center">
                                <?php echo number_format($row['quantidade'], 2) ?>
                            </td>
                            <td class="py-1 px-2 text-center">
                                <?php echo ($row['unidade']) ?>
                            </td>
                            <td class="py-1 px-2">
                                <?php echo $row['nome'] ?> <br>
                                <?php echo $row['descricao'] ?>
                            </td>
                            <td class="py-1 px-2 text-right">
                                <?php echo number_format($row['preco']) ?>
                            </td>
                            <td class="py-1 px-2 text-right">
                                <?php echo number_format($row['total']) ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>

                </tbody>
                <tfoot>
                    <tr>
                        <th class="text-right py-1 px-2" colspan="4">Subtotal</th>
                        <th class="text-right py-1 px-2 sub-total">
                            <?php echo number_format($total, 2) ?>
                        </th>
                    </tr>
                    <tr>
                        <th class="text-right py-1 px-2" colspan="4">Desconto
                            <?php echo isset($desconto_perc) ? $desconto_perc : 0 ?>%
                        </th>
                        <th class="text-right py-1 px-2 desconto">
                            <?php echo isset($desconto) ? number_format($desconto, 2) : 0 ?>
                        </th>
                    </tr>
                    <tr>
                        <th class="text-right py-1 px-2" colspan="4">Imposto
                            <?php echo isset($imposto_perc) ? $imposto_perc : 0 ?>%
                        </th>
                        <th class="text-right py-1 px-2 imposto">
                            <?php echo isset($imposto) ? number_format($imposto, 2) : 0 ?>
                        </th>
                    </tr>
                    <tr>
                        <th class="text-right py-1 px-2" colspan="4">Total</th>
                        <th class="text-right py-1 px-2 total-geral">
                            <?php echo isset($valor) ? number_format($valor, 2) : 0 ?>
                        </th>
                    </tr>
                </tfoot>
            </table>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="observacoes" class="text-info control-label">Observações</label>
                        <p>
                            <?php echo isset($observacoes) ? $observacoes : '' ?>
                        </p>
                    </div>
                </div>
                <?php if ($status > 0): ?>
                    <div class="col-md-6">
                        <span class="text-info">
                            <?php echo ($status == 2) ? "RECEBIDO" : "RECEBIDO PARCIALMENTE" ?>
                        </span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="card-footer py-1 text-center">
        <button class="btn btn-flat btn-success" type="button" id="print">Imprimir</button>
        <a class="btn btn-flat btn-primary"
            href="<?php echo base_url . '/admin?page=recebimento/gerenciar_recebimento&id=' . (isset($id) ? $id : '') ?>">Editar</a>
        <a class="btn btn-flat btn-dark" href="<?php echo base_url . '/admin?page=recebimento' ?>">Voltar para a Lista</a>
    </div>
</div>
<table id="clone_list" class="d-none">
    <tr>
        <td class="py-1 px-2 text-center">
            <button class="btn btn-outline-danger btn-sm rem_row" type="button"><i class="fa fa-times"></i></button>
        </td>
        <td class="py-1 px-2 text-center qty">
            <span class="visible"></span>
            <input type="hidden" name="id_item[]">
            <input type="hidden" name="unidade[]">
            <input type="hidden" name="qtd[]">
            <input type="hidden" name="preco[]">
            <input type="hidden" name="total[]">
        </td>
        <td class="py-1 px-2 text-center unit">
        </td>
        <td class="py-1 px-2 item">
        </td>
        <td class="py-1 px-2 text-right cost">
        </td>
        <td class="py-1 px-2 text-right total">
        </td>
    </tr>
</table>
<script>$(function () {
        $('#print').click(function () {
            start_loader()
            var _el = $('<div>')
            var _head = $('head').clone()
            _head.find('title').text("Detalhes do Pedido Recebido - Visualização de Impressão")
            var p = $('#print_out').clone()
            p.find('tr.text-light').removeClass("text-light bg-navy")
            _el.append(_head)
            _el.append('<div class="d-flex justify-content-center">' +
                '<div class="col-1 text-right">' +
                '<img src="<?php echo validate_image($_settings->info('logo')) ?>" width="65px" height="65px" />' +
                '</div>' +
                '<div class="col-10">' +
                '<h4 class="text-center"><?php echo $_settings->info('nome') ?></h4>' +
                '<h4 class="text-center">Pedido Recebido</h4>' +
                '</div>' +
                '<div class="col-1 text-right">' +
                '</div>' +
                '</div><hr/>')
            _el.append(p.html())
            var nw = window.open("", "", "width=1200,height=900,left=250,location=no,titlebar=yes")
            nw.document.write(_el.html())
            nw.document.close()
            setTimeout(() => {
                nw.print()
                setTimeout(() => {
                    nw.close()
                    end_loader()
                }, 200);
            }, 500);
        })
    })
</script>