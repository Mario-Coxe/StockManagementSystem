<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">Lista de Fornecedores</h3>
		<div class="card-tools">
			<a href="javascript:void(0)" id="create_new" class="btn btn-flat btn-primary"><span
					class="fas fa-plus"></span> Criar Novo</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<div class="container-fluid">
				<table class="table table-bordered table-striped">
					<colgroup>
						<col width="5%">
						<col width="15%">
						<col width="25%">
						<col width="25%">
						<col width="15%">
						<col width="15%">
					</colgroup>
					<thead>
						<tr>
							<th>#</th>
							<th>Data de Criação</th>
							<th>Fornecedor</th>
							<th>Pessoa de Contato</th>
							<th>Status</th>
							<th>Ação</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$i = 1;
						$qry = $conn->query("SELECT * from `supplier_list`  order by `name` asc ");
						while ($row = $qry->fetch_assoc()):
							?>
							<tr>
								<td class="text-center">
									<?php echo $i++; ?>
								</td>
								<td>
									<?php echo date("Y-m-d H:i", strtotime($row['date_created'])) ?>
								</td>
								<td>
									<?php echo $row['name'] ?>
								</td>
								<td class="">
									<?php echo $row['cperson'] ?>
								</td>
								<td class="text-center">
									<?php if ($row['status'] == 1): ?>
										<span class="badge badge-success rounded-pill">Ativo</span>
									<?php else: ?>
										<span class="badge badge-danger rounded-pill">Inativo</span>
									<?php endif; ?>
								</td>
								<td align="center">
									<button type="button"
										class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon"
										data-toggle="dropdown">
										Ação
										<span class="sr-only">Alternar Dropdown</span>
									</button>
									<div class="dropdown-menu" role="menu">
										<a class="dropdown-item view_data" href="javascript:void(0)"
											data-id="<?php echo $row['id'] ?>"><span class="fa fa-eye text-dark"></span>
											Visualizar</a>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item edit_data" href="javascript:void(0)"
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
			_conf("Tem certeza de que deseja excluir permanentemente este fornecedor?", "delete_category", [$(this).attr('data-id')])
		})
		$('#create_new').click(function () {
			uni_modal("<i class='fa fa-plus'></i> Adicionar Novo Fornecedor", "maintenance/manage_supplier.php", "mid-large")
		})
		$('.edit_data').click(function () {
			uni_modal("<i class='fa fa-edit'></i> Editar Detalhes do Fornecedor", "maintenance/manage_supplier.php?id=" + $(this).attr('data-id'), "mid-large")
		})
		$('.view_data').click(function () {
			uni_modal("<i class='fa fa-truck-loading'></i> Detalhes do Fornecedor", "maintenance/view_supplier.php?id=" + $(this).attr('data-id'), "")
		})
		$('.table td,.table th').addClass('py-1 px-2 align-middle')
		$('.table').dataTable();
	})
	function delete_category($id) {
		start_loader();
		$.ajax({
			url: _base_url_ + "classes/Master.php?f=delete_supplier",
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