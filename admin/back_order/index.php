<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Lista de Back Orders</h3>
        <!-- <div class="card-tools">
            <a href="<?php echo base_url ?>admin/?page=back_order/manage_bo" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Criar Novo</a>
        </div> -->
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
                            <th>Código do BO</th>
                            <th>Fornecedor</th>
                            <th>Itens</th>
                            <th>Status</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        $qry = $conn->query("SELECT p.*, s.name as supplier FROM `back_order_list` p inner join supplier_list s on p.supplier_id = s.id order by p.`date_created` desc");
                        while ($row = $qry->fetch_assoc()):
                            $row['items'] = $conn->query("SELECT count(item_id) as `items` FROM `bo_items` where bo_id = '{$row['id']}' ")->fetch_assoc()['items'];
                            ?>
                            <tr>
                                <td class="text-center">
                                    <?php echo $i++; ?>
                                </td>
                                <td>
                                    <?php echo date("Y-m-d H:i", strtotime($row['date_created'])) ?>
                                </td>
                                <td>
                                    <?php echo $row['bo_code'] ?>
                                </td>
                                <td>
                                    <?php echo $row['supplier'] ?>
                                </td>
                                <td class="text-right">
                                    <?php echo number_format($row['items']) ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($row['status'] == 0): ?>
                                        <span class="badge badge-primary rounded-pill">Pendente</span>
                                    <?php elseif ($row['status'] == 1): ?>
                                        <span class="badge badge-warning rounded-pill">Parcialmente recebido</span>
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
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <div class="dropdown-menu" role="menu">
                                        <?php if ($row['status'] == 0): ?> <a class="dropdown-item"
                                                href="<?php echo base_url . 'admin?page=receiving/manage_receiving&bo_id=' . $row['id'] ?>"
                                                data-id="<?php echo $row['id'] ?>"><span class="fa fa-boxes text-dark"></span>
                                                Receber</a>
                                            <div class="dropdown-divider"></div>
                                        <?php endif; ?>
                                        <a class="dropdown-item"
                                            href="<?php echo base_url . 'admin?page=back_order/view_bo&id=' . $row['id'] ?>"
                                            data-id="<?php echo $row['id'] ?>"><span class="fa fa-eye text-dark"></span>
                                            Ver</a>
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
            _conf("Tem certeza de que deseja excluir este Back Order permanentemente?", "delete_bo", [$(this).attr('data-id')])
        })
        $('.view_details').click(function () {
            uni_modal("Detalhes do Pagamento", "transaction/view_payment.php?id=" + $(this).attr('data-id'), 'mid-large')
        })
        $('.table td,.table th').addClass('py-1 px-2 align-middle')
        $('.table').dataTable();
    })
    function delete_bo($id) {
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=delete_bo",
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