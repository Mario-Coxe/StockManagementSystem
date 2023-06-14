<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Lista de Pedidos Recebidos</h3>
        <!-- <div class="card-tools">
            <a href="<?php echo base_url ?>admin/?page=purchase_order/manage_po" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Criar Novo</a>
        </div> -->
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <div class="container-fluid">
                <table class="table table-bordered table-stripped">
                    <colgroup>
                        <col width="5%">
                        <col width="25%">
                        <col width="25%">
                        <col width="25%">
                        <col width="20%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Data de Criação</th>
                            <th>Origem</th>
                            <th>Itens</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        $qry = $conn->query("SELECT * FROM `receiving_list` order by `date_created` desc");
                        while ($row = $qry->fetch_assoc()):
                            $row['items'] = explode(',', $row['stock_ids']);
                            if ($row['from_order'] == 1) {
                                $code = $conn->query("SELECT po_code from `purchase_order_list` where id='{$row['form_id']}' ")->fetch_assoc()['po_code'];
                            } else {
                                $code = $conn->query("SELECT bo_code from `back_order_list` where id='{$row['form_id']}' ")->fetch_assoc()['bo_code'];
                            }
                            ?>
                            <tr>
                                <td class="text-center">
                                    <?php echo $i++; ?>
                                </td>
                                <td>
                                    <?php echo date("Y-m-d H:i", strtotime($row['date_created'])) ?>
                                </td>
                                <td>
                                    <?php echo $code ?>
                                </td>
                                <td class="text-right">
                                    <?php echo number_format(count($row['items'])) ?>
                                </td>
                                <td align="center">
                                    <button type="button"
                                        class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon"
                                        data-toggle="dropdown">
                                        Ação
                                        <span class="sr-only">Alternar Dropdown</span>
                                    </button>
                                    <div class="dropdown-menu" role="menu">
                                        <a class="dropdown-item"
                                            href="<?php echo base_url . 'admin?page=receiving/view_receiving&id=' . $row['id'] ?>"
                                            data-id="<?php echo $row['id'] ?>"><span class="fa fa-eye text-dark"></span>
                                            Ver</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item"
                                            href="<?php echo base_url . 'admin?page=receiving/manage_receiving&id=' . $row['id'] ?>"
                                            data-id="<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span>
                                            Editar</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item delete_data" href="javascript:void(0)"
                                            data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span>
                                            Apagar</a>
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
            _conf("Tem certeza de que deseja excluir permanentemente este Pedido Recebido?", "delete_receiving", [$(this).attr('data-id')])
        })
        $('.view_details').click(function () {
            uni_modal("Detalhes do Recebimento", "receiving/view_receiving.php?id=" + $(this).attr('data-id'), 'mid-large')
        })
        $('.table td,.table th').addClass('py-1 px-2 align-middle')
        $('.table').dataTable();
    })
    function delete_receiving($id) {
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=delete_receiving",
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