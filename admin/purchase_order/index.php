<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Lista de Pedidos de Compra</h3>
        <div class="card-tools">
            <a href="<?php echo base_url ?>admin/?page=purchase_order/manage_po" class="btn btn-flat btn-primary"><span
                    class="fas fa-plus"></span> Criar Novo</a>
        </div>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <div class="container-fluid">
                <table class="table table-bordered table-stripped">
                    <colgroup>
                        <col width="5%">
                        <col width="15%">
                        <col width="20%">
                        <col width="20%">
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Data de Criação</th>
                            <th>Código do Pedido</th>
                            <th>Fornecedor</th>
                            <th>Itens</th>
                            <th>Status</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        $qry = $conn->query("SELECT p.*, s.nome as fornecedor FROM `lista_pedido_compra` p inner join lista_fornecedor s on p.id_fornecedor = s.id order by p.`data_criacao` desc");
                        while ($row = $qry->fetch_assoc()):
                            $row['itens'] = $conn->query("SELECT count(id_item) as `itens` FROM `lista_item_pedido` where id_pedido = '{$row['id']}' ")->fetch_assoc()['itens'];
                            ?>
                            <tr>
                                <td class="text-center">
                                    <?php echo $i++; ?>
                                </td>
                                <td>
                                    <?php echo date("Y-m-d H:i", strtotime($row['data_criacao'])) ?>
                                </td>
                                <td>
                                    <?php echo $row['codigo_pedido'] ?>
                                </td>
                                <td>
                                    <?php echo $row['fornecedor'] ?>
                                </td>
                                <td class="text-right">
                                    <?php echo number_format($row['itens']) ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($row['status'] == 0): ?>
                                        <span class="badge badge-primary rounded-pill">Pendente</span>
                                    <?php elseif ($row['status'] == 1): ?>
                                        <span class="badge badge-warning rounded-pill">Recebido Parcialmente</span>
                                    <?php elseif ($row['status'] == 2): ?>
                                        <span class="badge badge-success rounded-pill">Recebido</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger rounded-pill">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td align="center">
                                    <button type="button"
                                        class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon"
                                        data-toggle="dropdown">
                                        Ação
                                        <span class="sr-only">Alternar Menu Suspenso</span>
                                    </button>
                                    <div class="dropdown-menu" role="menu">
                                        <?php if ($row['status'] == 0): ?> <a class="dropdown-item"
                                                href="<?php echo base_url . 'admin?page=receiving/manage_receiving&po_id=' . $row['id'] ?>"
                                                data-id="<?php echo $row['id'] ?>"><span class="fa fa-boxes text-dark"></span>
                                                Receber</a>
                                            <div class="dropdown-divider"></div>
                                        <?php endif; ?>
                                        <a class="dropdown-item"
                                            href="<?php echo base_url . 'admin?page=purchase_order/view_po&id=' . $row['id'] ?>"
                                            data-id="<?php echo $row['id'] ?>"><span class="fa fa-eye text-dark"></span>
                                            Visualizar</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item"
                                            href="<?php echo base_url . 'admin?page=purchase_order/manage_po&id=' . $row['id'] ?>"
                                            data-id="<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span>
                                            Editar</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item delete_data" href="javascript:void(0)"
                                            data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span>
                                            Excluir</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('.delete_data').click(function () {
            _conf("Tem certeza de que deseja excluir este Pedido de Compra permanentemente?", "delete_po", [$(this).attr('data-id')])
        })
        $('.view_details').click(function () {
            uni_modal("Detalhes do Pagamento", "transaction/view_payment.php?id=" + $(this).attr('data-id'), 'mid-large')
        })
        $('.table td,.table th').addClass('py-1 px-2 align-middle')
        $('.table').dataTable();
    })
    function delete_po($id) {
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=delete_po",
            method: "POST",
            data: { id: $id },
            dataType: "json",
            error: err => {
                console.log(err)
                alert_toast("Ocorreu um erro.", 'error');
                end_loader();
            },
            success: function (resp) {
                if (typeof resp == 'object' && resp.status == 'success') {
                    location.reload();
                } else {
                    alert_toast("Ocorreu um erro.", 'error');
                    end_loader();
                }
            }
        })
    }
</script>